<?php if (isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>
<!-- Slider Başlangıç -->
<div class="container-fluid px-0 mt-0 mb-5">
    <div id="filmSlider" class="carousel slide" data-bs-ride="carousel">
        <!-- İndikatörler -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#filmSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#filmSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#filmSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        
        <!-- Slider İçeriği -->
        <div class="carousel-inner">
            <?php
            // Slider için en yüksek IMDB puanına sahip 3 filmi getir
            $slider_query = "SELECT * FROM filmler ORDER BY imdb DESC LIMIT 3";
            $slider_stmt = $pdo->query($slider_query);
            $first = true; // İlk slide için active class eklemek için
            
            while($slide = $slider_stmt->fetch(PDO::FETCH_ASSOC)) {
                $image_path = 'uploads/' . $slide['afis_resmi'];
                
                // Resim yoksa varsayılan resmi kullan
                if (!file_exists($image_path)) {
                    $image_path = 'uploads/default.jpg';
                }
                
                echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">';
                echo '<div class="d-block w-100 slider-img-container" style="height: 500px; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url(\'' . htmlspecialchars($image_path) . '\'); background-size: cover; background-position: center; position: relative;">';
                echo '<div class="carousel-caption" style="background-color: rgba(0,0,0,0.5); border-left: 4px solid #daa520; padding: 20px; bottom: 50px; text-align: left;">';
                echo '<h2>' . htmlspecialchars($slide['film_adi']) . '</h2>';
                echo '<p class="d-none d-md-block"><strong>Yönetmen:</strong> ' . htmlspecialchars($slide['yonetmen']) . ' | <strong>IMDB:</strong> <span class="badge bg-warning text-dark">' . htmlspecialchars($slide['imdb']) . '</span></p>';
                
                // Kısa açıklama (ilk 150 karakter)
                $short_desc = !empty($slide['aciklama']) ? substr($slide['aciklama'], 0, 150) . '...' : 'Bu film için henüz açıklama eklenmemiştir.';
                echo '<p class="d-none d-md-block">' . htmlspecialchars($short_desc) . '</p>';
                
                // Detay butonu
                echo '<button class="btn btn-warning" onclick="showFilmDetails(' . $slide['id'] . ')"><i class="fa fa-film"></i> Detayları Gör</button>';
                echo '</div></div></div>';
                
                $first = false; // İlk slide'dan sonra active class eklenmeyecek
            }
            ?>
        </div>
        
        <!-- Kontrol Butonları -->
        <button class="carousel-control-prev" type="button" data-bs-target="#filmSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Önceki</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#filmSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sonraki</span>
        </button>
    </div>
</div>
<!-- Slider Bitiş -->
<?php endif; ?>

<!-- Slider için ek stil kodları -->
<style>
    /* Slider göstergeleri stilini özelleştirme */
    .carousel-indicators button {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: rgba(255, 255, 255, 0.5);
    }
    
    .carousel-indicators button.active {
        background-color: #daa520;
    }
    
    /* Slider okları için özel stil */
    .carousel-control-prev, .carousel-control-next {
        width: 50px;
        height: 50px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.7;
    }
    
    .carousel-control-prev {
        left: 15px;
    }
    
    .carousel-control-next {
        right: 15px;
    }
    
    .carousel-control-prev:hover, .carousel-control-next:hover {
        opacity: 1;
        background-color: rgba(218, 165, 32, 0.7);
    }
    
    /* Temalara göre slide caption stilleri */
    .light-mode .carousel-caption {
        background-color: rgba(255, 255, 255, 0.8) !important;
        color: #333 !important;
    }
    
    .light-mode .carousel-caption h2, 
    .light-mode .carousel-caption p {
        color: #333 !important;
    }
</style>

<!-- Slider için JavaScript fonksiyonları -->
<script>
function showFilmDetails(filmId) {
    // Sayfada ilgili filmin detaylarına git
    const detailButton = document.querySelector(`.detay-btn[data-film-id="${filmId}"]`);
    
    if (detailButton) {
        // Butona scroll yap
        detailButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Biraz bekleyip detayları aç (scroll tamamlandıktan sonra)
        setTimeout(() => {
            detailButton.click();
        }, 1000);
    }
}
</script>