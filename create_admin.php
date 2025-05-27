<?php
// Yeni bir admin hesabı oluşturmak için güvenli bir araç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantı dosyasını dahil et
if (file_exists('db.php')) {
    require_once 'db.php';
} else if (file_exists('../db.php')) {
    require_once '../db.php';
} else {
    die("<p style='color:red'>db.php dosyası bulunamadı!</p>");
}

$message = '';

// Tablo yoksa oluştur
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS adminler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
        sifre VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $message .= "<p style='color:green'>adminler tablosu kontrol edildi/oluşturuldu.</p>";
} catch (PDOException $e) {
    $message .= "<p style='color:red'>Tablo oluşturma hatası: " . $e->getMessage() . "</p>";
}

// Form gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    $sifre_tekrar = $_POST['sifre_tekrar'] ?? '';
    
    // Doğrulama
    if (empty($kullanici_adi) || empty($sifre) || empty($sifre_tekrar)) {
        $message .= "<p style='color:red'>Tüm alanları doldurun!</p>";
    } elseif ($sifre !== $sifre_tekrar) {
        $message .= "<p style='color:red'>Şifreler eşleşmiyor!</p>";
    } else {
        try {
            // Kullanıcı adı var mı kontrol et
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM adminler WHERE kullanici_adi = ?");
            $stmt->execute([$kullanici_adi]);
            if ($stmt->fetchColumn() > 0) {
                $message .= "<p style='color:red'>Bu kullanıcı adı zaten kullanılıyor!</p>";
            } else {
                // Yeni admin ekle
                $hashedPassword = password_hash($sifre, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO adminler (kullanici_adi, sifre) VALUES (?, ?)");
                $stmt->execute([$kullanici_adi, $hashedPassword]);
                $message .= "<p style='color:green'>Admin hesabı başarıyla oluşturuldu!</p>";
                
                // Hash bilgilerini göster
                $message .= "<p>Şifre güvenli bir şekilde hash edildi.<br>
                Hash türü: PASSWORD_DEFAULT (bcrypt)<br>
                Hash uzunluğu: " . strlen($hashedPassword) . " karakter</p>";
            }
        } catch (PDOException $e) {
            $message .= "<p style='color:red'>Veritabanı hatası: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Oluştur - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Hesabı Oluştur</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="kullanici_adi" class="form-label">Admin Kullanıcı Adı</label>
                <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
            </div>
            
            <div class="mb-3">
                <label for="sifre" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="sifre" name="sifre" required>
            </div>
            
            <div class="mb-3">
                <label for="sifre_tekrar" class="form-label">Şifre (Tekrar)</label>
                <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger">Admin Hesabı Oluştur</button>
                <a href="db_check.php" class="btn btn-secondary">Veritabanı Kontrol Sayfasına Dön</a>
            </div>
        </form>
    </div>
</body>
</html>