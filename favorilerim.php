<?php 
require_once 'db.php';
session_start();

// Kullanıcı giriş yapmamışsa ana sayfaya yönlendir
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Favori kaldırma işlemi
if (isset($_POST['remove_favorite']) && isset($_POST['film_id'])) {
    $film_id = $_POST['film_id'];
    $user_id = $_SESSION['user_id'];
    
    $delete_query = "DELETE FROM favoriler WHERE kullanici_id = :user_id AND film_id = :film_id";
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->execute(['user_id' => $user_id, 'film_id' => $film_id]);
    
    // Sayfayı yenile
    header("Location: favorilerim.php");
    exit;
}

// Kullanıcının favorilerini getir
$favorites_query = "SELECT f.*, fr.afis_resmi, fr.film_adi, fr.tur, fr.yil, fr.yonetmen, fr.imdb, fr.aciklama 
                   FROM favoriler f 
                   JOIN filmler fr ON f.film_id = fr.id 
                   WHERE f.kullanici_id = :user_id";
$favorites_stmt = $pdo->prepare($favorites_query);
$favorites_stmt->execute(['user_id' => $_SESSION['user_id']]);
$favorites = $favorites_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorilerim - Film Arşivi</title>
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
            color: #daa520;
            font-size: 20px;
        }
        
        .card-img-container {
            position: relative;
        }
        
        /* Boş favoriler için stil */
        .empty-favorites {
            text-align: center;
            padding: 50px 20px;
            border-radius: 15px;
            margin: 50px auto;
            max-width: 600px;
        }
        
        .dark-mode .empty-favorites {
            background-color: rgba(30, 30, 47, 0.8);
            border: 2px solid var(--gold-accent);
        }
        
        .light-mode .empty-favorites {
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--gold-button);
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
            <span class="me-3 text-light">Hoşgeldin, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
            <a href="index.php" class="btn btn-warning me-3">
                <i class="fas fa-film me-1"></i>Film Arşivi
            </a>
            <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="display-4 text-center mb-4">Favorilerim</h1>
    
    <?php if (empty($favorites)): ?>
        <div class="empty-favorites">
            <i class="fas fa-heart-broken fa-5x mb-4" style="color: #daa520;"></i>
            <h2 class="mb-3">Henüz favori filminiz bulunmuyor</h2>
            <p class="lead mb-4">Film arşivimizden beğendiğiniz filmleri favorilerinize ekleyebilirsiniz.</p>
            <a href="index.php" class="btn text-dark" style="background-color: #daa520; border-color: #daa520; font-weight: bold;">
                <i class="fas fa-film me-2"></i>Film Arşivine Git
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($favorites as $film): ?>
                <div class="col-md-4 mb-4">
                    <div class="card dark-mode" style="border-radius: 15px; transition: transform 0.3s ease-in-out; overflow: hidden;">
                        <div class="card-img-container">
                            <img src="uploads/<?php echo htmlspecialchars($film['afis_resmi']); ?>" class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($film['film_adi']); ?>" 
                                 style="height: 300px; object-fit: cover; border-bottom: 2px solid #ffd700;">
                            
                            <form method="POST">
                                <input type="hidden" name="film_id" value="<?php echo $film['film_id']; ?>">
                                <button type="submit" name="remove_favorite" class="favorite-btn">
                                    <i class="fas fa-heart favorite-icon"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($film['film_adi']); ?></h5>
                            <p><strong>Türü:</strong> <?php echo htmlspecialchars($film['tur']); ?></p>
                            <p><strong>Yıl:</strong> <?php echo htmlspecialchars($film['yil']); ?></p>
                            <p><strong>Yönetmen:</strong> <?php echo htmlspecialchars($film['yonetmen']); ?></p>
                            <p><strong>IMDB Puanı:</strong> <?php echo htmlspecialchars($film['imdb']); ?></p>
                            
                            <!-- Detaylar butonu -->
                            <button class="btn text-dark detay-btn" data-film-id="<?php echo $film['film_id']; ?>" 
                                    style="background-color: #daa520; border-color: #daa520; font-weight: bold;">
                                <i class="fa fa-film"></i> Detaylar
                            </button>
                            
                            <!-- Film açıklaması için gizli div -->
                            <div id="aciklama-<?php echo $film['film_id']; ?>" class="film-aciklama mt-3">
                                <?php if (!empty($film['aciklama'])): ?>
                                    <p><?php echo nl2br(htmlspecialchars($film['aciklama'])); ?></p>
                                <?php else: ?>
                                    <p>Bu film için henüz açıklama eklenmemiştir.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

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
        const emptyFavorites = document.querySelector('.empty-favorites');
        
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
                
                if (emptyFavorites) {
                    emptyFavorites.classList.remove('dark-mode');
                    emptyFavorites.classList.add('light-mode');
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
                
                if (emptyFavorites) {
                    emptyFavorites.classList.remove('light-mode');
                    emptyFavorites.classList.add('dark-mode');
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