#!/usr/bin/env python3
"""Kamuya açık yayın veritabanından bibliyografik klasör yapısı üretir.

Girdi: publications.json — her öğesi bir yayını tanımlayan sözlük listesi
(şema için publications.sample.json).

Üretilenler:
  prof_literatur/yayinlar/<yil>_<kisa_slug>/kunye.md
  prof_literatur/yayinlar/<yil>_<kisa_slug>/makale.pdf  (yalnızca açık erişimliyse)

İlkeler:
  - Sahte User-Agent / sahte Referer KULLANILMAZ; araç kendini dürüstçe tanıtır.
  - Erişim engeli (403 vb.) veya PDF olmayan içerik -> atlanır, künyeye not düşülür.
  - Bütünlük kontrolü yalnızca girdideki doğrulanabilir 'retraction' alanını
    yansıtır; çıkarım/ima yapılmaz.
"""
from __future__ import annotations

import argparse
import json
import re
import sys
import unicodedata
from pathlib import Path

try:
    import requests
except ImportError:
    requests = None  # PDF indirme istenmezse gerekmez

USER_AGENT = "academic-bibliography-archiver/1.0 (+research; respects-403)"
PDF_TIMEOUT = 30


def slugify(text: str, max_words: int = 6) -> str:
    text = unicodedata.normalize("NFKD", text)
    text = text.encode("ascii", "ignore").decode("ascii").lower()
    text = re.sub(r"[^a-z0-9\s-]", "", text)
    words = [w for w in re.split(r"[\s-]+", text) if w]
    return "-".join(words[:max_words]) or "yayin"


def dedupe(pubs: list[dict]) -> list[dict]:
    """Başlık+yıl ve DOI üzerinden mükerrer kayıtları birleştirir."""
    by_key: dict[tuple, dict] = {}
    for pub in pubs:
        doi = (pub.get("doi") or "").strip().lower()
        title_key = re.sub(r"\s+", " ", (pub.get("title") or "").strip().lower())
        key = ("doi", doi) if doi else ("title", title_key, pub.get("year"))
        if key in by_key:
            existing = by_key[key]
            merged = set(existing.get("confirmed_by", [])) | set(pub.get("confirmed_by", []))
            existing["confirmed_by"] = sorted(merged)
            for field, value in pub.items():
                if not existing.get(field) and value:
                    existing[field] = value
        else:
            by_key[key] = dict(pub)
    return list(by_key.values())


def integrity_note(pub: dict) -> str:
    retraction = pub.get("retraction")
    if not retraction:
        return ("Geri-çekilme/erratum kaydı bulunmadı "
                "(Crossref/Retraction Watch). İhlal bulgusu yok.")
    rtype = retraction.get("type", "kayıt")
    source = retraction.get("source", "kaynak belirtilmemiş")
    note = retraction.get("note", "")
    return (f"Bütünlük kaydı ({rtype}): {source}\n"
            f"  {note}".rstrip())


def write_kunye(folder: Path, pub: dict, pdf_status: str) -> None:
    authors = ", ".join(pub.get("authors", [])) or "—"
    confirmed = ", ".join(pub.get("confirmed_by", [])) or "—"
    lines = [
        f"# {pub.get('title', '—')}",
        "",
        "## Künye",
        f"- **Yazarlar:** {authors}",
        f"- **Dergi/Yer:** {pub.get('venue', '—')}",
        f"- **Yıl:** {pub.get('year', '—')}",
        f"- **Cilt/Sayı:** {pub.get('volume', '—')} / {pub.get('issue', '—')}",
        f"- **Sayfalar:** {pub.get('pages', '—')}",
        f"- **DOI:** {pub.get('doi', '—')}",
        f"- **Açık erişim linki:** {pub.get('oa_pdf_url') or '—'}",
        f"- **Kaynak linki:** {pub.get('source_url', '—')}",
        f"- **Teyit eden kaynaklar:** {confirmed} "
        f"({len(pub.get('confirmed_by', []))} kaynak)",
        f"- **PDF:** {pdf_status}",
        "",
        "## Bütünlük kontrolü",
        integrity_note(pub),
        "",
        "## Notlar",
        "(serbest not alanı)",
        "",
    ]
    (folder / "kunye.md").write_text("\n".join(lines), encoding="utf-8")


def download_pdf(url: str, dest: Path) -> str:
    """Açık erişim PDF'i indirir. Engel/başarısızlıkta açıklayıcı durum döner."""
    if requests is None:
        return "PDF indirilemedi: 'requests' kütüphanesi yüklü değil. Kaynak link künyede."
    try:
        resp = requests.get(
            url,
            headers={"User-Agent": USER_AGENT, "Accept": "application/pdf"},
            timeout=PDF_TIMEOUT,
            stream=True,
        )
    except requests.RequestException as exc:
        return f"PDF indirilemedi: bağlantı hatası ({exc.__class__.__name__}). Kaynak link künyede."
    if resp.status_code == 403:
        return "PDF indirilemedi: erişim engeli (403). Kaynak link künyede."
    if resp.status_code != 200:
        return f"PDF indirilemedi: HTTP {resp.status_code}. Kaynak link künyede."
    ctype = resp.headers.get("Content-Type", "").lower()
    if "pdf" not in ctype:
        return (f"PDF indirilemedi: içerik PDF değil ({ctype or 'bilinmiyor'}). "
                "Kaynak link künyede.")
    try:
        with dest.open("wb") as fh:
            for chunk in resp.iter_content(chunk_size=8192):
                fh.write(chunk)
    except OSError as exc:
        return f"PDF indirilemedi: yazma hatası ({exc}). Kaynak link künyede."
    return "makale.pdf olarak indirildi."


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--input", default="publications.json",
                        help="Yayın veritabanı JSON dosyası")
    parser.add_argument("--root", default="prof_literatur/yayinlar",
                        help="Çıktı kök klasörü")
    parser.add_argument("--no-pdf", action="store_true",
                        help="PDF indirmeyi atla (yalnızca künyeleri üret)")
    args = parser.parse_args()

    src = Path(args.input)
    if not src.exists():
        print(f"Girdi bulunamadı: {src}", file=sys.stderr)
        return 1

    pubs = dedupe(json.loads(src.read_text(encoding="utf-8")))
    root = Path(args.root)
    root.mkdir(parents=True, exist_ok=True)

    used: set[str] = set()
    for pub in pubs:
        year = pub.get("year", "tarihsiz")
        slug = slugify(pub.get("title", ""))
        name = f"{year}_{slug}"
        suffix = 2
        while name in used:
            name = f"{year}_{slug}-{suffix}"
            suffix += 1
        used.add(name)

        folder = root / name
        folder.mkdir(parents=True, exist_ok=True)

        pdf_status = "açık erişim linki yok."
        oa = pub.get("oa_pdf_url")
        if oa and not args.no_pdf:
            pdf_status = download_pdf(oa, folder / "makale.pdf")
        elif oa:
            pdf_status = "indirme atlandı (--no-pdf). Kaynak link künyede."

        write_kunye(folder, pub, pdf_status)
        print(f"[ok] {name}  ({pdf_status})")

    print(f"\nToplam {len(pubs)} tekil yayın işlendi -> {root}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
