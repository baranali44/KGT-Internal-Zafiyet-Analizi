<?php
/**
 * Proje: KGT-Internal Zafiyetli Web Uygulaması
 * Geliştirici: Ali Baran Berktaş (2420191033)
 */
echo "<!DOCTYPE html><html><head><title>KGT-Internal</title></head><body>";
echo "<h1>🛡️ KGT-Internal Yönetim Paneli</h1>";

// ZAFİYET: Girdi doğrulanmadan include ediliyor (LFI)
if (isset($_GET['page'])) {
    $sayfa = $_GET['page'];
    if (!empty($sayfa)) {
        include($sayfa); 
    }
} else {
    echo "<p>Lütfen modül seçin:</p><ul><li><a href='?page=iletisim.php'>İletişim</a></li></ul>";
}
echo "</body></html>";
?>