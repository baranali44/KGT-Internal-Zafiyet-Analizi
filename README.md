<p align="center">
  <img src="https://upload.wikimedia.org/wikipedia/tr/6/6f/%C4%B0stinye_%C3%9Cniversitesi_logo.png" alt="İstinye Üniversitesi Logosu" width="150">
</p>

# 🛡️ Zincirleme Zafiyet Analizi: LFI to RCE (Gelişmiş Penetrasyon Testi Raporu)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Security: Critical](https://img.shields.io/badge/Security-Critical-red.svg)]()
[![Platform: Linux/PHP](https://img.shields.io/badge/Platform-Linux%20%7C%20PHP-blue.svg)]()

**Öğrenci:** Ali Baran Berktaş (2420191033)  
**Üniversite:** İstinye Üniversitesi  
**Bölüm:** Bilişim Güvenliği Teknolojisi (İ.Ö)  

---

## 📑 İçindekiler
1. [Projenin Amacı ve Özeti](#1-projenin-amacı-ve-özeti)
2. [Tehdit Modeli ve Mimari Yapı](#2-tehdit-modeli-ve-mimari-yapı)
3. [İncelenen Zafiyetler ve İstismar Süreci](#3-incelenen-zafiyetler-ve-i̇stismar-süreci)
4. [Güvenlik Sıkılaştırması ve Savunma (Remediation)](#4-güvenlik-sıkılaştırması-ve-savunma-remediation)
5. [🎬 Otomatize Test ve Demo](#-otomatize-test-ve-demo)

---

## 1. Projenin Amacı ve Özeti
Bu proje, günümüz siber güvenlik tehditleri arasında kritik bir yer tutan "Zincirleme Zafiyet" (Vulnerability Chaining) konseptini uygulamalı bir laboratuvar ortamında incelemek amacıyla geliştirilmiştir. Yalnızca teorik bir dokümantasyon sunmak yerine; zafiyet barındıran kurgusal bir web uygulaması (KGT-Internal) tasarlanmış ve bu sistem üzerindeki açıkları otomatize bir şekilde tespit edip sömüren Python tabanlı bir istismar (exploit) aracı kodlanmıştır. Proje, hem saldırgan (Red Team) hem de savunmacı (Blue Team) perspektiflerini bir araya getirerek kapsamlı bir güvenlik analizi sunar.

## 2. Tehdit Modeli ve Mimari Yapı
Sistem, gerçek dünya senaryolarını yansıtması adına modern mimari ayrımlar gözetilerek tasarlanmıştır:
* **`/src/` (Hedef Sistem):** PHP 7.4 altyapısında çalışan, girdi doğrulama mekanizmalarından yoksun bırakılmış zafiyetli yönetim paneli.
* **`/exploit/` (Saldırı Vektörü):** Python `requests` kütüphanesi kullanılarak geliştirilmiş, hedefteki açıkları HTTP GET istekleriyle otonom olarak tarayan ve raporlayan profesyonel sızma testi aracı.
* **`Dockerfile`:** Hedef sistemin izole (sandbox) bir ortamda, ana makineye zarar vermeden güvenli bir şekilde ayağa kaldırılmasını sağlayan konteyner yapılandırması.

## 3. İncelenen Zafiyetler ve İstismar Süreci
Proje kapsamında düşük/orta seviyeli bir zafiyetin, sistem konfigürasyon hatalarıyla birleşerek nasıl kritik (CVSS: 10.0) bir boyuta ulaştığı kanıtlanmıştır:

* **Adım 1 - Local File Inclusion (LFI):** `src/index.php` dosyasında kullanıcıdan alınan `page` parametresi, herhangi bir filtreleme işlemine tabi tutulmadan doğrudan `include()` fonksiyonuna aktarılmaktadır. Bu durum, saldırganın Directory Traversal (`../`) karakterlerini kullanarak sunucu içindeki hassas sistem dosyalarını (örneğin `/etc/passwd`) okumasına olanak tanır.
* **Adım 2 - SSH Log Poisoning (RCE'ye Geçiş):** LFI zafiyeti tek başına dosya okuma imkanı sunarken, sunucunun SSH yetkilendirme loglarının (`/var/log/auth.log`) web sunucusu tarafından okunabilir olması senaryoyu değiştirir. Saldırgan, SSH bağlantısı yaparken kullanıcı adı yerine zararlı bir PHP komutu (payload) yazar. Bu payload log dosyasına kaydedilir. Ardından LFI açığı üzerinden bu log dosyası web tarayıcısında çağrıldığında, logun içindeki PHP kodu çalıştırılır ve sistemde tam yetkili Uzaktan Komut Çalıştırma (Remote Code Execution) elde edilir.

## 4. Güvenlik Sıkılaştırması ve Savunma (Remediation)
Bu zafiyet zincirini kırmak ve sistemi üretim (production) ortamına güvenli bir şekilde entegre etmek için uygulanması gereken "Derinlemesine Savunma" (Defense in Depth) adımları şunlardır:
1. **Kesin Girdi Doğrulama (Strict Input Validation):** Dışarıdan alınan hiçbir parametre doğrudan dosya sistemine yansıtılmamalıdır. `page` parametresi mutlaka `basename()` fonksiyonundan geçirilerek dizin atlama karakterlerinden arındırılmalıdır.
2. **Beyaz Liste (Whitelist) Yaklaşımı:** Uygulamanın sadece önceden tanımlanmış ve izin verilmiş modülleri (`iletisim.php` gibi) yüklemesine izin veren sıkı bir kontrol mantığı kurulmalıdır.
3. **En Az Yetki Prensibi (Principle of Least Privilege):** Web sunucusunu çalıştıran servisin (`www-data`), işletim sisteminin hassas log dosyalarına (`/var/log/`) erişim yetkisi ivedilikle kaldırılmalıdır.

## 5. 🎬 Otomatize Test ve Demo
Geliştirilen zafiyetli mimarinin ayağa kaldırılması ve özel Python exploit scripti aracılığıyla LFI zafiyetinin tespit edilip sistem dosyalarının ifşa edilme sürecini içeren operasyonel test kaydı aşağıda sunulmuştur. Antigravity AI simülasyonu yönergelerine uygun olarak hazırlanmıştır.

[▶️ KGT-Internal Penetrasyon Testi ve İstismar Videosunu İzlemek İçin Tıklayın (demo/project-demo.mp4)](./demo/project-demo.mp4)
