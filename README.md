# Adana Akü Forklift

Adana Akü Forklift için kurumsal tanıtım sitesi. Traksiyoner/lityum akü, akü revizyonu, redresör ve XCMG forklift hizmetlerini tanıtan, SEO ve performans odaklı tek sayfalık (single-page) bir PHP sitesidir.

## 🧱 Teknoloji

- **PHP** — sunucu tarafı şablon (`index.php`), CSRF korumalı teklif formu
- **Vanilla CSS/JS** — build aracı yok; `assets/style.css` ve `assets/app.js` doğrudan servis edilir
- Self-hosted **Inter** & **Space Grotesk** fontları (`assets/fonts/`, sadece latin + latin-ext alt kümeleri)
- Inline SVG ikon seti (harici görsel isteği yok)

## 📁 Proje Yapısı

```
├── index.php             # Ana uygulama (gerçek kaynak dosya)
├── api/index.php          # Vercel serverless giriş noktası (index.php'yi require eder)
├── assets/
│   ├── style.css
│   ├── app.js
│   ├── fonts/             # Self-hosted woff2 fontlar
│   └── *.png               # İkonlar / OG görseli
├── robots.txt
├── sitemap.xml
├── .htaccess               # Apache (XAMPP / klasik hosting) için önbellek & sıkıştırma
├── vercel.json              # Vercel dağıtım yapılandırması
└── docs/                    # Geçmiş optimizasyon raporları
```

## 💻 Yerel Geliştirme

**XAMPP / Apache + PHP (önerilen, tam uyumlu):**
```
X:\xampp\htdocs\adanaaku altına kopyalayın, Apache+PHP'yi başlatın,
http://localhost/adanaaku/ adresini açın.
```

**PHP yerleşik sunucusu:**
```sh
php -S 127.0.0.1:8000
```

## 🚀 Dağıtım (Vercel)

Bu proje Vercel'de **topluluk tarafından sağlanan [`vercel-php`](https://github.com/vercel-community/php) çalışma zamanı** ile yayınlanır (Vercel PHP'yi resmi olarak desteklemez).

> ⚠️ **Bilinmesi gerekenler:**
> - `vercel-php` topluluk projesi arşivlenmiş durumda; resmi Vercel desteği yoktur.
> - İletişim formundaki CSRF doğrulaması PHP `$_SESSION` kullanır. Sunucusuz (serverless) ortamda oturumlar her zaman kalıcı olmayabileceğinden, form zaman zaman "güvenlik doğrulaması başarısız" mesajı gösterebilir — kullanıcı sayfayı yenileyip tekrar deneyebilir.
> - Form şu an gerçek bir e-posta göndermez (`mail()` çağrısı kodda devre dışı/yorum satırı); yalnızca başarı/hata mesajı gösterir.
> - Tam üretim güvenilirliği için önerilen yol: statik HTML + Vercel Serverless Function (Node.js) ile form işleme, ya da PHP'yi tam destekleyen bir sunucu (Render, Railway, geleneksel paylaşımlı hosting).

Dağıtım adımları:
```sh
npm i -g vercel
vercel login
vercel          # preview dağıtım
vercel --prod   # production dağıtım
```

## 📚 Dokümantasyon

Geçmiş SEO ve performans optimizasyon raporları için [docs/](docs) klasörüne bakın.
