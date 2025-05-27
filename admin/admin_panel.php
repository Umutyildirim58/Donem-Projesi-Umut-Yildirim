<?php
require_once 'db.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get all movies
$stmt = $pdo->query("SELECT * FROM filmler ORDER BY id DESC");
$filmler = $stmt->fetchAll();

// Delete movie if requested
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Get poster path to delete file
    $stmt_poster = $pdo->prepare("SELECT poster FROM filmler WHERE id = ?");
    $stmt_poster->execute([$delete_id]);
    $poster_data = $stmt_poster->fetch();
    
    if ($poster_data && !empty($poster_data['poster'])) {
        $poster_path = 'uploads/' . $poster_data['poster'];
        if (file_exists($poster_path)) {
            unlink($poster_path);
        }
    }
    
    // Delete from database
    $stmt_delete = $pdo->prepare("DELETE FROM filmler WHERE id = ?");
    if ($stmt_delete->execute([$delete_id])) {
        header("Location: admin_panel.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #1c1c1c;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            padding: 15px;
            border-bottom: 2px solid #ff0000;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        
        .admin-header {
            background: #1e1e2f;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        h2 {
            color: #ff0000;
            margin-bottom: 20px;
        }
        
        .btn-add {
            background-color: #28a745;
            border: none;
        }
        
        .btn-add:hover {
            background-color: #218838;
        }
        
        .btn-logout {
            background-color: #dc3545;
            border: none;
        }
        
        .btn-logout:hover {
            background-color: #c82333;
        }
        
        .card {
            background: #2c2c44;
            border: none;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-img-top {
            height: 300px;
            object-fit: cover;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-title {
            color: #fff;
            font-weight: bold;
            font-size: 1.25rem;
        }
        
        .card-text {
            color: #aaa;
        }
        
        .movie-info {
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .btn-edit {
            background-color: #ffc107;
            border: none;
            color: #000;
        }
        
        .btn-edit:hover {
            background-color: #e0a800;
            color: #000;
        }
        
        .btn-delete {
            background-color: #dc3545;
            border: none;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
        }
        
        .alert {
            border-radius: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-icon {
            font-size: 5rem;
            color: #555;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Film Arşivi</a>
        <div class="ms-auto">
            <span class="navbar-text me-3">
                <i class="fas fa-user-shield me-2"></i>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['admin']); ?>
            </span>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="admin-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-film me-2"></i>Film Yönetimi</h2>
                <p>Burada tüm film arşivinizi yönetebilirsiniz.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="film_ekle.php" class="btn btn-add btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Yeni Film Ekle
                </a>
            </div>
        </div>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>İşlem başarıyla tamamlandı!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>Bir hata oluştu!
        </div>
    <?php endif; ?>
    
    <?php if (count($filmler) > 0): ?>
        <div class="row">
            <?php foreach ($filmler as $film): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if (!empty($film['poster'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($film['poster']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($film['baslik']); ?>">
                        <?php else: ?>
                            <div class="no-poster d-flex align-items-center justify-content-center bg-secondary" style="height: 300px;">
                                <i class="fas fa-film fa-4x"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($film['baslik']); ?></h5>
                            
                            <div class="movie-info">
                                <p><i class="fas fa-calendar-alt me-2"></i>Yıl: <?php echo htmlspecialchars($film['yil']); ?></p>
                                <p><i class="fas fa-star me-2"></i>IMDB: <?php echo htmlspecialchars($film['imdb_puani']); ?></p>
                                <p><i class="fas fa-tags me-2"></i>Tür: <?php echo htmlspecialchars($film['tur']); ?></p>
                            </div>
                            
                            <p class="card-text">
                                <?php echo mb_strlen($film['ozet']) > 100 ? mb_substr(htmlspecialchars($film['ozet']), 0, 100) . '...' : htmlspecialchars($film['ozet']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <a href="film_duzenle.php?id=<?php echo $film['id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit me-2"></i>Düzenle
                                </a>
                                <a href="admin_panel.php?delete_id=<?php echo $film['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Bu filmi silmek istediğinizden emin misiniz?');">
                                    <i class="fas fa-trash-alt me-2"></i>Sil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-film empty-icon"></i>
            <h3>Henüz film eklenmemiş</h3>
            <p>Film arşivinize yeni filmler ekleyerek başlayın.</p>
            <a href="film_ekle.php" class="btn btn-add mt-3">
                <i class="fas fa-plus-circle me-2"></i>İlk Filmi Ekle
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>