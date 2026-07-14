# Prof. Dr. Fatih Yılmaz — Yayın Derlemesi

**Kurum:** Tokat Gaziosmanpaşa Üniversitesi, Türkçe Eğitimi Anabilim Dalı
**Derleme tarihi:** (oluşturulurken doldurulur)
**Niteliği:** Tarafsız, yalnızca kamuya açık kaynaklara dayanan bibliyografik derleme.

## Kapsam

Bu klasör, adı geçen akademisyenin kamuya açık akademik yayın kaydının
(makale, bildiri, kitap bölümü) derlenmiş ve tekilleştirilmiş bir kopyasını
içerir. Her yayın `yayinlar/<yil>_<kisa_slug>/kunye.md` altında künyesiyle
birlikte tutulur; mevcutsa açık erişim PDF'i aynı klasöre `makale.pdf` olarak
indirilir.

## Kaynaklar

- ORCID: https://orcid.org/0000-0001-8025-8439
- YÖK Akademik (Author ID: 7A20E7CB7681FDB7)
- Google Scholar ve DergiPark yazar aramaları
- Bütünlük kontrolü: Crossref ve Retraction Watch geri-çekilme kayıtları

Her yayın künyesinde, kaydı kaç bağımsız kaynağın teyit ettiği belirtilir.

## Önemli not

Bu derleme **hiçbir suçlama, değer yargısı veya nitelendirme içermez.**
Yalnızca doğrulanabilir bibliyografik olgular kaydedilir. "Bütünlük kontrolü"
bölümü, salt Crossref/Retraction Watch'ta belgelenmiş ve kaynağı gösterilen
geri-çekilme/erratum kayıtlarını yansıtır; böyle bir kayıt yoksa
"geri-çekilme kaydı bulunmadı" yazılır. Çıkarım, ima veya söylentiye dayalı
hiçbir ifade yer almaz.

## Yapı

```
prof_literatur/
├── README.md
├── build_bibliography.py      # veritabanından klasör yapısını üreten script
├── publications.sample.json   # girdi şeması örneği
└── yayinlar/
    └── <yil>_<kisa_slug>/
        ├── kunye.md
        └── makale.pdf          # yalnızca açık erişimliyse
```

## Kullanım

İnternet erişimli bir makinede (ör. kendi masaüstü bilgisayarınız):

```bash
pip install requests

# 1) ORCID herkese açık API'sinden veriyi çek (+ Crossref ile zenginleştir)
python fetch_orcid.py --orcid 0000-0001-8025-8439 --mailto sizinmail@ornek.com

# 2) Klasör yapısını ve künyeleri üret, açık erişim PDF'leri indir
python build_bibliography.py --input publications.json
```

Alternatif: `fetch_orcid.py` yerine yayınları elle `publications.json`
dosyasına da yazabilirsiniz (şema için `publications.sample.json`). YÖK
Akademik / Google Scholar / DergiPark kayıtlarını elle ekleyip
`confirmed_by` alanına ilgili kaynağı yazarak teyit sayısını artırabilirsiniz.

Script, sahte tarayıcı/cihaz taklidi yapmaz; erişim engeli (ör. 403) alınan
PDF'leri atlar ve ilgili künyeye "PDF indirilemedi" notu düşer.
