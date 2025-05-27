<?php 
// Hata raporlama ayarı - geliştirme sırasında açık
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
session_start();

// Favorileme işlemi
if(isset($_POST['toggle_favorite']) && isset($_SESSION['user'])) {
    $film_id = $_POST['film_id'];
    $user_id = $_SESSION['user_id'];
    
    // Önce favori W durumu kontrol et
    $check_query = "SELECT * FROM favoriler WHERE kullanici_id = :user_id AND film_id = :film_id";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(['user_id' => $user_id, 'film_id' => $film_id]);
    
    if($check_stmt->rowCount() > 0) {
        // Favori ise kaldır
        $delete_query = "DELETE FROM favoriler WHERE kullanici_id = :user_id AND film_id = :film_id";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_stmt->execute(['user_id' => $user_id, 'film_id' => $film_id]);
    } else {
        // Favori değilse ekle
        $insert_query = "INSERT INTO favoriler (kullanici_id, film_id) VALUES (:user_id, :film_id)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->execute(['user_id' => $user_id, 'film_id' => $film_id]);
    }
    
    // İşlem sonrası aynı sayfaya yönlendir
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['search']) ? "?search=".$_GET['search'] : ""));
    exit;
}

// Eğer uploads klasörü yoksa oluştur
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}
?>

<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Arşivi - Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --light-bg: linear-gradient(to right, #f5f5f5, #e0e0e0);
            --dark-bg: linear-gradient(to right, #1c1c1c, #3a0ca3);
            --light-nav: linear-gradient(to right, #e9e9e9, #c7c7c7);
            --dark-nav: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            --light-card: #ffffff;
            --dark-card: #1e1e2f;
            --light-text: #212529;
            --dark-text: #ffffff;
            --gold-accent: #ffd700;
            --gold-button: #daa520;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        body.light-mode {
            background: var(--light-bg);
            color: var(--light-text);
        }
        
        body.dark-mode {
            background: var(--dark-bg);
            color: var(--dark-text);
        }
        
        /* Arama formu düzenlemeleri */
        .dark-mode .form-control {
            background-color: #2c2c42;
            border-color: #484869;
            color: #ffffff;
        }
        
        .dark-mode .form-control::placeholder {
            color: #a0a0a0;
        }
        
        .light-mode .form-control {
            background-color: #ffffff;
            border-color: #ced4da;
            color: #212529;
        }
        
        .navbar.light-mode {
            background: var(--light-nav) !important;
            border-bottom: 2px solid var(--gold-accent);
        }
        
        .navbar.dark-mode {
            background: var(--dark-nav) !important;
            border-bottom: 2px solid var(--gold-accent);
        }
        
        .card.light-mode {
            background: var(--light-card);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            color: var(--light-text);
        }
        
        .card.dark-mode {
            background: var(--dark-card);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
            color: var(--dark-text);
        }
        
        /* Karanlık modda metin renklerini belirginleştirme */
        .dark-mode .card-title, 
        .dark-mode .card-body p, 
        .dark-mode .card-body strong {
            color: #ffffff;
        }
        
        .light-mode .card-title, 
        .light-mode .card-body p, 
        .light-mode .card-body strong {
            color: #212529;
        }
        
        footer.light-mode {
            background: #e9e9e9;
            color: #555;
        }
        
        footer.dark-mode {
            background: #0f0c29;
            color: #ccc;
        }
        
        .theme-toggle {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .theme-toggle i {
            margin-right: 5px;
        }
        
        /* Login/Register prompt container */
        .login-prompt {
            padding: 50px 20px;
            border-radius: 15px;
            margin: 50px auto;
            max-width: 800px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        
        .dark-mode .login-prompt {
            background-color: rgba(30, 30, 47, 0.8);
            border: 2px solid var(--gold-accent);
        }
        
        .light-mode .login-prompt {
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--gold-button);
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(218, 165, 32, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(218, 165, 32, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(218, 165, 32, 0);
            }
        }
        
        .btn-auth {
            margin: 10px;
            padding: 10px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Admin button styles */
        .admin-button {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            transition: all 0.3s ease;
        }
        
        .admin-button:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Film açıklaması için stiller */
        .film-aciklama {
            display: none;
            padding: 15px;
            margin-top: 10px;
            border-top: 1px solid #daa520;
            transition: all 0.3s ease;
        }
        
        .dark-mode .film-aciklama {
            background-color: rgba(30, 30, 60, 0.7);
        }
        
        .light-mode .film-aciklama {
            background-color: rgba(245, 245, 245, 0.9);
        }
        
        /* Favori buton stili */
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .favorite-btn:hover {
            transform: scale(1.1);
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        .favorite-icon {
            color: #ccc;
            font-size: 20px;
        }
        
        .favorite-active .favorite-icon {
            color: #daa520;
        }
        
        .card-img-container {
            position: relative;
        }

        /* İzle butonu stilleri */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: all 0.3s ease;
            z-index: 5; /* Butonun üstte olduğundan emin olmak için */
            position: relative;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            z-index: 5; /* Butonun üstte olduğundan emin olmak için */
            position: relative;
        }
    </style>
</head>
<body class="dark-mode">

<nav class="navbar navbar-expand-lg navbar-dark dark-mode" style="padding: 15px; z-index: 1000;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php" style="font-weight: bold; font-size: 2rem; letter-spacing: 1px;">Film Arşivi</a>
        <div class="d-flex align-items-center">
            <button id="themeToggle" class="theme-toggle btn btn-outline-warning me-3">
                <i class="fas fa-sun"></i> <span>Aydınlık Mod</span>
            </button>
            <?php if (isset($_SESSION['user'])): ?>
                <span class="me-3 text-light">Hoşgeldin, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
                <a href="favorilerim.php" class="btn btn-warning me-3">
                    <i class="fas fa-heart me-1"></i>Favorilerim
                </a>
                <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
            <?php elseif (isset($_SESSION['admin'])): ?>
                <span class="me-3 text-light">Admin: <?php echo htmlspecialchars($_SESSION['admin']); ?></span>
                <a href="admin_panel.php" class="btn btn-primary me-2">Yönetim Paneli</a>
                <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-success me-2">Giriş Yap</a>
                <a href="register.php" class="btn btn-warning me-2">Kayıt Ol</a>
                <a href="admin_login.php" class="btn admin-button">
                    <i class="fas fa-user-shield me-1"></i>Admin Girişi
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
<div class="container text-center mt-5">
    <h1 class="display-4">Film Arşivine Göz Atın</h1>
    <p class="lead">En sevdiğiniz filmleri keşfedin ve koleksiyonunuzu genişletin.</p>
</div>
<?php else: ?>
<div class="container text-center mt-5">
    <h1 class="display-4">Film Arşivimize Hoş Geldiniz</h1>
    <p class="lead">Geniş film koleksiyonumuza erişmek için lütfen giriş yapın.</p>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
<!-- Sadece giriş yapmış kullanıcılar için içerik -->
<div class="container mt-4">
    <div class="search-bar text-center mb-4">
        <form method="GET">
            <input type="text" name="search" placeholder="Film Ara..." class="form-control w-50 d-inline" style="display:inline-block;" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn text-dark" style="background-color: #daa520; border-color: #daa520; font-weight: bold;"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <div class="row">
        <?php
        $query = "SELECT * FROM filmler";
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            // Doğru sütun adlarını kullan
            $query .= " WHERE film_adi LIKE :search OR tur LIKE :search OR yonetmen LIKE :search";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt = $pdo->query($query);
        }

        if ($stmt->rowCount() > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Kullanıcı için favori kontrolü
                $favorite = false;
                if(isset($_SESSION['user_id'])) {
                    $fav_check = $pdo->prepare("SELECT * FROM favoriler WHERE kullanici_id = ? AND film_id = ?");
                    $fav_check->execute([$_SESSION['user_id'], $row['id']]);
                    $favorite = $fav_check->rowCount() > 0;
                }
                
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card dark-mode' style='border-radius: 15px; transition: transform 0.3s ease-in-out; overflow: hidden;'>";
                
                // Resim container'ı eklendi
                echo "<div class='card-img-container' style='position: relative; z-index: 1;'>"; // z-index düşürüldü
                
                // Doğru sütun adını kullan: afis_resmi
                $image_path = 'Uploads/' . $row['afis_resmi'];
                
                // Debug bilgisi
                echo "<!-- Resim yolu: " . htmlspecialchars($image_path) . " -->";
                
                // Resim dosyasının varlığını kontrol et
                if (file_exists($image_path)) {
                    echo "<img src='" . htmlspecialchars($image_path) . "' class='card-img-top' alt='Film Afişi' style='height: 300px; object-fit: cover; border-bottom: 2px solid #ffd700;'>";
                } else {
                    // Resim bulunamadıysa varsayılan resmi göster
                    echo "<img src='Uploads/default.jpg' class='card-img-top' alt='Film Afişi Bulunamadı' style='height: 300px; object-fit: cover; border-bottom: 2px solid #ffd700;'>";
                    // Hata ayıklama için yorum
                    echo "<!-- Resim bulunamadı: " . htmlspecialchars($image_path) . " -->";
                }
                
                // Favori butonu (sadece normal kullanıcılar için)
                if(isset($_SESSION['user'])) {
                    echo "<form method='POST' style='position: absolute; top: 10px; right: 10px; z-index: 10;'>";
                    echo "<input type='hidden' name='film_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='toggle_favorite' value='1'>";
                    echo "<button type='submit' class='favorite-btn " . ($favorite ? 'favorite-active' : '') . "'>";
                    echo "<i class='fas fa-heart favorite-icon'></i>";
                    echo "</button>";
                    echo "</form>";
                }
                
                echo "</div>"; // card-img-container kapanış
                
                echo "<div class='card-body text-center'>";
                // Doğru sütun adını kullan: film_adi
                echo "<h5 class='card-title'>" . htmlspecialchars($row['film_adi']) . "</h5>";
                echo "<p><strong>Türü:</strong> " . htmlspecialchars($row['tur']) . "</p>";
                echo "<p><strong>Yıl:</strong> " . htmlspecialchars($row['yil']) . "</p>";
                echo "<p><strong>Yönetmen:</strong> " . htmlspecialchars($row['yonetmen']) . "</p>";
                echo "<p><strong>IMDB Puanı:</strong> " . htmlspecialchars($row['imdb']) . "</p>";

                // Detaylar butonunu ekle
                echo "<button class='btn text-dark detay-btn' data-film-id='" . $row['id'] . "' style='background-color: #daa520; border-color: #daa520; font-weight: bold; margin-right: 10px; z-index: 5; position: relative;'>";
                echo "<i class='fa fa-film'></i> Detaylar</button>";

               
                    
$izle_link = isset($row['izle_link']) ? $row['izle_link'] : '';

if (!empty($izle_link)) {
    echo "<a href='" . htmlspecialchars($izle_link) . "' class='btn btn-success' target='_blank' style='font-weight: bold; z-index: 5; position: relative;'>";
    echo "<i class='fa fa-play'></i> İzle</a>";
} else {
    echo "<button class='btn btn-secondary' disabled style='font-weight: bold; z-index: 5; position: relative;'>";
    echo "<i class='fa fa-play'></i> İzle (link yok)</button>";
}

echo "<!-- Debug: izle_link = " . (isset($row['izle_link']) ? htmlspecialchars($row['izle_link']) : 'Boş') . " -->";


                // Debug: izle_link değerini göster
                echo "<!-- Debug: izle_link = " . (isset($row['izle_link']) ? htmlspecialchars($row['izle_link']) : 'Boş') . " -->";

                // Film açıklaması için gizli div
                echo "<div id='aciklama-" . $row['id'] . "' class='film-aciklama mt-3'>";
                if (!empty($row['aciklama'])) {
                    echo "<p>" . nl2br(htmlspecialchars($row['aciklama'])) . "</p>";
                } else {
                    echo "<p>Bu film için henüz açıklama eklenmemiştir.</p>";
                }
                echo "</div>";
                
                echo "</div></div></div>";
            }
        } else {
            echo "<p class='text-center'>Hiç film bulunamadı.</p>";
        }
        ?>
    </div>
</div>
<?php else: ?>
<!-- Giriş yapmamış kullanıcılar için giriş/kayıt mesajı -->
<div class="container">
    <div class="login-prompt dark-mode">
        <i class="fas fa-lock fa-5x mb-4" style="color: #daa520;"></i>
        <h2 class="mb-4">Özel Film Koleksiyonumuz</h2>
        <p class="lead mb-4">Premium film arşivimize erişmek için üye girişi yapmalısınız.</p>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Film koleksiyonumuz sadece kayıtlı kullanıcılarımıza özeldir.
        </div>
        <div class="mt-4">
            <a href="login.php" class="btn btn-success btn-auth"><i class="fas fa-sign-in-alt me-2"></i>Giriş Yap</a>
            <a href="register.php" class="btn btn-warning btn-auth"><i class="fas fa-user-plus me-2"></i>Kayıt Ol</a>
        </div>
        <div class="mt-4">
            <a href="admin_login.php" class="btn admin-button btn-auth">
                <i class="fas fa-user-shield me-2"></i>Admin Girişi
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Varsayılan resim oluştur/kontrol et -->
<?php
// uploads klasöründeki default.jpg dosyasını kontrol et, yoksa oluştur
if (!file_exists('uploads/default.jpg')) {
    // GD kütüphanesi kullanılarak basit bir varsayılan resim oluştur
    if (function_exists('imagecreatetruecolor')) {
        $image = imagecreatetruecolor(300, 400);
        $bg = imagecolorallocate($image, 30, 30, 50);
        $text_color = imagecolorallocate($image, 220, 165, 32); // Altın rengi
        
        imagefill($image, 0, 0, $bg);
        imagestring($image, 5, 100, 180, 'Film', $text_color);
        imagestring($image, 5, 90, 200, 'Resmi', $text_color);
        imagestring($image, 5, 75, 220, 'Bulunamadi', $text_color);
        
        imagejpeg($image, 'uploads/default.jpg');
        imagedestroy($image);
    }
}
?>

<footer class="dark-mode" style="text-align: center; padding: 20px; margin-top: 50px;">
    <p>© 2024 Film Arşivi | Umut YILDIRIM</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        const navbar = document.querySelector('.navbar');
        const cards = document.querySelectorAll('.card');
        const footer = document.querySelector('footer');
        const htmlElement = document.documentElement;
        const loginPrompt = document.querySelector('.login-prompt');
        
        // Tema değişimi için fonksiyon
        function toggleTheme() {
            if (body.classList.contains('dark-mode')) {
                // Aydınlık moda geçiş
                body.classList.remove('dark-mode');
                body.classList.add('light-mode');
                navbar.classList.remove('dark-mode');
                navbar.classList.add('light-mode');
                navbar.classList.remove('navbar-dark');
                navbar.classList.add('navbar-light');
                footer.classList.remove('dark-mode');
                footer.classList.add('light-mode');
                htmlElement.setAttribute('data-bs-theme', 'light');
                
                if (loginPrompt) {
                    loginPrompt.classList.remove('dark-mode');
                    loginPrompt.classList.add('light-mode');
                }
                
                cards.forEach(card => {
                    card.classList.remove('dark-mode');
                    card.classList.add('light-mode');
                });
                
                themeToggle.innerHTML = '<i class="fas fa-moon"></i> <span>Karanlık Mod</span>';
                themeToggle.classList.remove('btn-outline-warning');
                themeToggle.classList.add('btn-outline-dark');
                
                // Tema tercihini localStorage'a kaydet
                localStorage.setItem('theme', 'light');
            } else {
                // Karanlık moda geçiş
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
                navbar.classList.remove('light-mode');
                navbar.classList.add('dark-mode');
                navbar.classList.remove('navbar-light');
                navbar.classList.add('navbar-dark');
                footer.classList.remove('light-mode');
                footer.classList.add('dark-mode');
                htmlElement.setAttribute('data-bs-theme', 'dark');
                
                if (loginPrompt) {
                    loginPrompt.classList.remove('light-mode');
                    loginPrompt.classList.add('dark-mode');
                }
                
                cards.forEach(card => {
                    card.classList.remove('light-mode');
                    card.classList.add('dark-mode');
                });
                
                themeToggle.innerHTML = '<i class="fas fa-sun"></i> <span>Aydınlık Mod</span>';
                themeToggle.classList.remove('btn-outline-dark');
                themeToggle.classList.add('btn-outline-warning');
                
                // Tema tercihini localStorage'a kaydet
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Tema değişimi düğmesi için olay dinleyicisi
        themeToggle.addEventListener('click', toggleTheme);
        
        // Sayfa yüklendiğinde localStorage'dan tema tercihini al
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            toggleTheme(); // Sayfa varsayılan olarak karanlık modda, aydınlık moda geçiş yap
        }
        
        // Film detayları için tüm butonlara event listener ekle
        const detayButtons = document.querySelectorAll('.detay-btn');
        detayButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const filmId = this.getAttribute('data-film-id');
                const aciklamaDiv = document.getElementById('aciklama-' + filmId);
                
                // Diğer tüm açık açıklamaları kapat
                document.querySelectorAll('.film-aciklama').forEach(div => {
                    if (div.id !== 'aciklama-' + filmId && div.style.display === 'block') {
                        div.style.display = 'none';
                    }
                });
                
                // Toggle açıklama görünürlüğü
                if (aciklamaDiv.style.display === 'block') {
                    aciklamaDiv.style.display = 'none';
                    this.innerHTML = '<i class="fa fa-film"></i> Detaylar';
                } else {
                    aciklamaDiv.style.display = 'block';
                    this.innerHTML = '<i class="fa fa-times"></i> Kapat';
                }
            });
        });
    });
</script>
</body>
</html>