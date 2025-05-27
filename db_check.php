<?php
// Veritabanı bağlantı durumunu kontrol etmek için güvenli bir araç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantı dosyasını dahil et
if (file_exists('db.php')) {
    require_once 'db.php';
    echo "<p>db.php dosyası bulundu ve dahil edildi.</p>";
} else if (file_exists('../db.php')) {
    require_once '../db.php';
    echo "<p>../db.php dosyası bulundu ve dahil edildi.</p>";
} else {
    die("<p style='color:red'>db.php dosyası bulunamadı!</p>");
}

// PDO bağlantısı kontrol et
if (isset($pdo) && $pdo instanceof PDO) {
    echo "<p style='color:green'>PDO bağlantısı başarılı.</p>";
    
    // Adminler tablosunu kontrol et
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'adminler'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>adminler tablosu bulundu.</p>";
            
            // Adminler tablosunun yapısını göster
            $stmt = $pdo->query("DESCRIBE adminler");
            echo "<p>adminler tablosu yapısı:</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Alan</th><th>Tip</th><th>Null</th><th>Anahtar</th><th>Varsayılan</th><th>Ekstra</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            // Admin kayıtlarını kontrol et (şifreleri göstermeden)
            $stmt = $pdo->query("SELECT id, kullanici_adi, SUBSTRING(sifre, 1, 10) as sifre_kismi, LENGTH(sifre) as sifre_uzunlugu FROM adminler");
            echo "<p>Admin kayıtları:</p>";
            if ($stmt->rowCount() > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Kullanıcı Adı</th><th>Şifre (ilk 10 karakter)</th><th>Şifre Uzunluğu</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kullanici_adi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sifre_kismi'] . '...') . "</td>";
                    echo "<td>" . htmlspecialchars($row['sifre_uzunlugu']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:red'>Adminler tablosunda hiç kayıt yok!</p>";
            }
        } else {
            echo "<p style='color:red'>adminler tablosu bulunamadı!</p>";
            
            // Mevcut tabloları göster
            $stmt = $pdo->query("SHOW TABLES");
            echo "<p>Mevcut tablolar:</p>";
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                echo "<li>" . htmlspecialchars($row[0]) . "</li>";
            }
            echo "</ul>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Sorgu hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color:red'>PDO bağlantısı sağlanamadı. db.php dosyasını kontrol edin.</p>";
    
    if (isset($pdo)) {
        echo "<p>pdo değişkeni tanımlı, ancak PDO nesnesi değil.</p>";
    } else {
        echo "<p>pdo değişkeni tanımlı değil.</p>";
    }
}
?>

<p>
<a href="create_admin.php">Admin Oluştur</a>
</p>