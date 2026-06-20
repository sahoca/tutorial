#!/usr/bin/env python3
"""ORCID herkese açık API'sinden (ve isteğe bağlı Crossref'ten) publications.json üretir.

İnternet erişimli bir makinede (ör. kendi masaüstün) çalıştırılır:

    python fetch_orcid.py --orcid 0000-0001-8025-8439 --mailto seninmail@ornek.com

Çıktı: publications.json  (build_bibliography.py'nin girdisi)

İlkeler:
  - Yalnızca kamuya açık ORCID/Crossref API'leri kullanılır; sahte UA/Referer yok.
  - Crossref'te 'is-retracted-by' / 'has-correction' türü bağlar varsa, bunlar
    'retraction' alanına olgusal olarak yazılır; yoksa alan null kalır. Çıkarım yok.
"""
from __future__ import annotations

import argparse
import json
import sys
import time
from pathlib import Path

try:
    import requests
except ImportError:
    print("Bu script 'requests' gerektirir:  pip install requests", file=sys.stderr)
    raise SystemExit(1)

ORCID_API = "https://pub.orcid.org/v3.0"
CROSSREF_API = "https://api.crossref.org/works"
TIMEOUT = 30
# Crossref'te geri-çekilme/düzeltme ilişkisi sayılan güncelleme tipleri:
RETRACTION_UPDATE_TYPES = {
    "retraction", "partial_retraction", "removal", "withdrawal",
    "erratum", "correction", "expression_of_concern",
}


def ua(mailto: str | None) -> dict:
    tag = f" (mailto:{mailto})" if mailto else ""
    return {"User-Agent": f"academic-bibliography-archiver/1.0{tag}",
            "Accept": "application/json"}


def get_json(url: str, headers: dict) -> dict | None:
    try:
        resp = requests.get(url, headers=headers, timeout=TIMEOUT)
    except requests.RequestException as exc:
        print(f"  ! istek hatası: {exc.__class__.__name__} -> {url}", file=sys.stderr)
        return None
    if resp.status_code == 403:
        print(f"  ! 403 (erişim engeli / allowlist): {url}", file=sys.stderr)
        return None
    if resp.status_code != 200:
        print(f"  ! HTTP {resp.status_code}: {url}", file=sys.stderr)
        return None
    try:
        return resp.json()
    except ValueError:
        return None


def first_doi(ext_ids: dict) -> str | None:
    for eid in ext_ids.get("external-id", []) or []:
        if (eid.get("external-id-type") or "").lower() == "doi":
            val = eid.get("external-id-value")
            if val:
                return val.strip().lower()
    return None


def crossref_enrich(doi: str, headers: dict) -> dict:
    """Crossref'ten yazar/dergi/cilt vb. ve geri-çekilme bilgisi alır."""
    out: dict = {"retraction": None}
    data = get_json(f"{CROSSREF_API}/{doi}", headers)
    if not data or "message" not in data:
        return out
    msg = data["message"]
    authors = []
    for a in msg.get("author", []) or []:
        family = a.get("family", "")
        given = a.get("given", "")
        authors.append(", ".join(p for p in (family, given) if p))
    if authors:
        out["authors"] = authors
    if msg.get("container-title"):
        out["venue"] = msg["container-title"][0]
    for key, dst in (("volume", "volume"), ("issue", "issue"), ("page", "pages")):
        if msg.get(key):
            out[dst] = msg[key]
    # Geri-çekilme/düzeltme bağları
    updates = msg.get("update-to", []) or []
    flagged = [u for u in updates
               if (u.get("type") or "").lower() in RETRACTION_UPDATE_TYPES]
    if flagged or msg.get("update-to"):
        types = sorted({(u.get("type") or "kayıt").lower() for u in updates})
        out["retraction"] = {
            "type": ", ".join(types) or "update",
            "source": f"{CROSSREF_API}/{doi}",
            "note": "Crossref 'update-to' bağı mevcut (olgusal kayıt).",
        }
    return out


def main() -> int:
    ap = argparse.ArgumentParser(description=__doc__)
    ap.add_argument("--orcid", required=True, help="ORCID iD, ör. 0000-0001-8025-8439")
    ap.add_argument("--mailto", default=None, help="Crossref nezaketi için e-posta")
    ap.add_argument("--output", default="publications.json")
    ap.add_argument("--no-crossref", action="store_true",
                    help="Crossref zenginleştirmeyi atla")
    args = ap.parse_args()

    headers = ua(args.mailto)
    print(f"ORCID works çekiliyor: {args.orcid}")
    works = get_json(f"{ORCID_API}/{args.orcid}/works", headers)
    if not works:
        print("ORCID works alınamadı (yukarıdaki hataya bak).", file=sys.stderr)
        return 1

    pubs: list[dict] = []
    groups = works.get("group", []) or []
    print(f"{len(groups)} yayın grubu bulundu.")
    for grp in groups:
        summaries = grp.get("work-summary", []) or []
        if not summaries:
            continue
        s = summaries[0]
        title = (((s.get("title") or {}).get("title") or {}).get("value")) or ""
        year = (((s.get("publication-date") or {}).get("year") or {}).get("value"))
        doi = first_doi(grp.get("external-ids") or {})
        venue = (s.get("journal-title") or {}).get("value") or ""
        url = ((s.get("url") or {}) or {}).get("value")
        pub = {
            "title": title,
            "authors": [],
            "venue": venue,
            "year": int(year) if year and str(year).isdigit() else year,
            "volume": None, "issue": None, "pages": None,
            "doi": doi,
            "oa_pdf_url": None,
            "source_url": url or f"https://orcid.org/{args.orcid}",
            "confirmed_by": ["ORCID"],
            "retraction": None,
        }
        if doi and not args.no_crossref:
            enrich = crossref_enrich(doi, headers)
            for k, v in enrich.items():
                if v:
                    pub[k] = v
            if "Crossref" not in pub["confirmed_by"] and enrich.get("authors"):
                pub["confirmed_by"].append("Crossref")
            time.sleep(0.2)  # Crossref'e nazik ol
        pubs.append(pub)
        print(f"  - {pub['year']}  {title[:60]}")

    Path(args.output).write_text(
        json.dumps(pubs, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"\n{len(pubs)} yayın -> {args.output}")
    print("Sonraki adım:  python build_bibliography.py --input "
          f"{args.output}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
