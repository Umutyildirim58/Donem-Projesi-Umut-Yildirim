
<?php
session_start();

// Admin kontrolü
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

require_once 'db.php';

$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$message = '';

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // === FILM EKLE ===
    if (isset($_POST['add_film'])) {
        $film_adi = trim($_POST['film_adi'] ?? '');
        $yonetmen = trim($_POST['yonetmen'] ?? '');
        $yil = (int)($_POST['yil'] ?? 0);
        $tur = trim($_POST['tur'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $imdb = (float)($_POST['imdb'] ?? 0);
        $afis_resmi = '';

        if (!empty($_FILES['afis_resmi']['name']) && $_FILES['afis_resmi']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['afis_resmi']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $new_filename = uniqid('film_') . '.' . $ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['afis_resmi']['tmp_name'], $destination)) {
                    $afis_resmi = $new_filename; // ✅ Sadece dosya adı
                } else {
                    $message = "<div class='alert alert-danger'>Dosya yüklenemedi!</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Geçersiz dosya uzantısı!</div>";
            }
        }

        if (empty($film_adi) || empty($yonetmen) || $yil <= 0) {
            $message = "<div class='alert alert-danger'>Zorunlu alanları doldurun!</div>";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO filmler (film_adi, yonetmen, yil, tur, aciklama, afis_resmi, imdb) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$film_adi, $yonetmen, $yil, $tur, $aciklama, $afis_resmi, $imdb]);
                $message = "<div class='alert alert-success'>Film eklendi!</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Veritabanı hatası: " . $e->getMessage() . "</div>";
            }
        }

    // === FILM GÜNCELLE ===
    } elseif (isset($_POST['edit_film'])) {
        $film_id = (int)($_POST['film_id'] ?? 0);
        $film_adi = trim($_POST['film_adi'] ?? '');
        $yonetmen = trim($_POST['yonetmen'] ?? '');
        $yil = (int)($_POST['yil'] ?? 0);
        $tur = trim($_POST['tur'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $imdb = (float)($_POST['imdb'] ?? 0);
        $eski_afis_resmi = trim($_POST['eski_afis_resmi'] ?? '');
        $afis_resmi = $eski_afis_resmi;

        if (!empty($_FILES['afis_resmi']['name']) && $_FILES['afis_resmi']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['afis_resmi']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $new_filename = uniqid('film_') . '.' . $ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['afis_resmi']['tmp_name'], $destination)) {
                    if (!empty($eski_afis_resmi) && file_exists($upload_dir . $eski_afis_resmi)) {
                        unlink($upload_dir . $eski_afis_resmi);
                    }
                    $afis_resmi = $new_filename; // ✅ Sadece dosya adı
                } else {
                    $message = "<div class='alert alert-danger'>Yeni afiş yüklenemedi!</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Geçersiz dosya uzantısı!</div>";
            }
        }

        if ($film_id <= 0 || empty($film_adi) || empty($yonetmen) || $yil <= 0) {
            $message = "<div class='alert alert-danger'>Geçerli veriler girilmelidir!</div>";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE filmler SET film_adi=?, yonetmen=?, yil=?, tur=?, aciklama=?, afis_resmi=?, imdb=? WHERE id=?");
                $stmt->execute([$film_adi, $yonetmen, $yil, $tur, $aciklama, $afis_resmi, $imdb, $film_id]);
                $message = "<div class='alert alert-success'>Film güncellendi!</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Veritabanı hatası: " . $e->getMessage() . "</div>";
            }
        }

    // === FILM SİL ===
    } elseif (isset($_POST['delete_film'])) {
        $film_id = (int)($_POST['film_id'] ?? 0);
        $afis_resmi = trim($_POST['afis_resmi'] ?? '');

        if ($film_id <= 0) {
            $message = "<div class='alert alert-danger'>Geçerli bir film ID girilmelidir!</div>";
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM filmler WHERE id = ?");
                $stmt->execute([$film_id]);

                if (!empty($afis_resmi) && file_exists($upload_dir . $afis_resmi)) {
                    unlink($upload_dir . $afis_resmi);
                }

                $message = "<div class='alert alert-success'>Film silindi!</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Silme hatası: " . $e->getMessage() . "</div>";
            }
        }

    // === ÇIKIŞ ===
    } elseif (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: admin_login.php");
        exit;
    }
}

// Film listesini çek
$filmler = [];
try {
    $stmt = $pdo->query("SELECT * FROM filmler ORDER BY id DESC");
    $filmler = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Listeleme hatası: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1c1c1c, #3a0ca3);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
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
        
        .admin-container {
            background: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.2);
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #ff0000;
            margin-bottom: 25px;
            text-align: center;
        }
        
        h3 {
            color: #ff0000;
            margin-bottom: 20px;
            border-bottom: 1px solid #ff0000;
            padding-bottom: 10px;
        }
        
        .nav-tabs {
            border-bottom: 1px solid #ff0000;
            margin-bottom: 25px;
        }
        
        .nav-tabs .nav-link {
            color: #fff;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 10px 20px;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            border-bottom: 3px solid #ff0000;
            color: #ff0000;
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: transparent;
            color: #ff0000;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ff0000;
            color: #fff;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #ff0000;
            box-shadow: 0 0 8px rgba(255, 0, 0, 0.5);
        }
        
        .btn-action {
            background-color: #ff0000;
            border-color: #ff0000;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 50px;
            text-transform: uppercase;
        }
        
        .btn-action:hover {
            background-color: #cc0000;
            border-color: #cc0000;
        }
        
        .table {
            color: #fff;
        }
        
        .table th {
            background-color: rgba(255, 0, 0, 0.2);
            border-color: #ff0000;
        }
        
        .table td {
            border-color: rgba(255, 0, 0, 0.2);
            vertical-align: middle;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .film-list {
            max-height: 500px;
            overflow-y: auto;
        }
        
        /* Custom scrollbar */
        .film-list::-webkit-scrollbar {
            width: 8px;
        }
        
        .film-list::-webkit-scrollbar-track {
            background: #1e1e2f;
        }
        
        .film-list::-webkit-scrollbar-thumb {
            background: #ff0000;
            border-radius: 10px;
        }
        
        .film-list::-webkit-scrollbar-thumb:hover {
            background: #cc0000;
        }
        
        /* Image preview styles */
        .image-preview {
            width: 100%;
            max-height: 200px;
            border: 2px dashed #ff0000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            border-radius: 10px;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 196px;
            object-fit: contain;
        }
        
        .image-preview.has-image {
            border-style: solid;
        }
        
        .image-preview-text {
            color: #ff0000;
            padding: 20px;
            text-align: center;
        }
        
        .film-poster {
            max-width: 100px;
            max-height: 150px;
            border: 2px solid #ff0000;
            border-radius: 5px;
            object-fit: cover;
        }
        
        .image-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .image-upload-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .delete-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .delete-image-btn:hover {
            opacity: 1;
        }

        .user-dropdown {
            cursor: pointer;
        }
        
        .user-dropdown .dropdown-menu {
            background-color: #1e1e2f;
            border: 1px solid #ff0000;
        }
        
        .user-dropdown .dropdown-item {
            color: #fff;
        }
        
        .user-dropdown .dropdown-item:hover {
            background-color: rgba(255, 0, 0, 0.2);
        }
        
        .user-dropdown .dropdown-divider {
            border-top: 1px solid rgba(255, 0, 0, 0.2);
        }
        
        .welcome-message {
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(255, 0, 0, 0.1);
            border-radius: 10px;
            border-left: 4px solid #ff0000;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Film Arşivi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
                </li>
                <li class="nav-item dropdown user-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <button type="submit" name="logout" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="admin-container">
        <h2><i class="fas fa-user-shield me-2"></i>Admin Panel</h2>
        
        <div class="welcome-message">
            <h4>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</h4>
            <p class="mb-0">Film arşivi yönetim paneline erişim sağladınız. Buradan film ekleyebilir, düzenleyebilir ve silebilirsiniz.</p>
        </div>
        
        <?php 
        if (isset($db_error)) {
            echo "<div class='alert alert-danger'>" . $db_error . "</div>";
        }
        
        echo $message; 
        ?>
        
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="film-ekle-tab" data-bs-toggle="tab" data-bs-target="#film-ekle" type="button" role="tab" aria-controls="film-ekle" aria-selected="true">
                    <i class="fas fa-plus-circle"></i> Film Ekle
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="film-listesi-tab" data-bs-toggle="tab" data-bs-target="#film-listesi" type="button" role="tab" aria-controls="film-listesi" aria-selected="false">
                    <i class="fas fa-list"></i> Film Listesi ve Düzenleme
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="adminTabsContent">
            <!-- Film Ekle -->
            <div class="tab-pane fade show active" id="film-ekle" role="tabpanel" aria-labelledby="film-ekle-tab">
                <h3>Yeni Film Ekle</h3>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="film_adi" class="form-label">Film Adı</label>
                                <input type="text" class="form-control" id="film_adi" name="film_adi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="yonetmen" class="form-label">Yönetmen</label>
                                <input type="text" class="form-control" id="yonetmen" name="yonetmen" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
    <label for="izle_link" class="form-label">İzle Linki</label>
    <input type="url" class="form-control" id="izle_link" name="izle_link">
</div>
                                        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imdb" class="form-label">IMDB Puanı</label>
                                <input type="number" class="form-control" id="imdb" name="imdb" min="0" max="10" step="0.1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tur" class="form-label">Tür</label>
                                <input type="text" class="form-control" id="tur" name="tur">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="yil" class="form-label">Yıl</label>
                                <input type="number" class="form-control" id="yil" name="yil" min="1900" max="2099" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="aciklama" class="form-label">Film Açıklaması</label>
                        <textarea class="form-control" id="aciklama" name="aciklama" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="afis_resmi" class="form-label">Afiş Resmi</label>
                        <div class="image-preview" id="add-image-preview">
                            <div class="image-preview-text">Afiş resmini görüntülemek için dosya seçin.</div>
                        </div>
                        <div class="image-upload-wrapper">
                            <button type="button" class="btn btn-action">
                                <i class="fas fa-upload"></i> Afiş Resmi Seç
                            </button>
                            <input type="file" class="form-control" id="afis_resmi" name="afis_resmi" accept="image/*" onchange="previewImage('afis_resmi', 'add-image-preview')">
                        </div>
                    </div>
                    
                    <button type="submit" name="add_film" class="btn btn-action">
                        <i class="fas fa-plus-circle"></i> Film Ekle
                    </button>
                </form>
            </div>
            
            <!-- Film Listesi ve Düzenleme -->
            <div class="tab-pane fade" id="film-listesi" role="tabpanel" aria-labelledby="film-listesi-tab">
                <h3>Film Listesi</h3>
                
                <div class="film-list">
                    <?php if (count($filmler) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Afiş</th>
                                    <th>Film Adı</th>
                                    <th>Yönetmen</th>
                                    <th>Yıl</th>
                                    <th>Tür</th>
                                    <th>IMDB</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filmler as $film): ?>
                                <tr>
                                    <td><?php echo $film['id']; ?></td>
                                    <td>
                                        <?php if (!empty($film['afis_resmi']) && file_exists($upload_dir . $film['afis_resmi'])): ?>
                                            <img src="<?php echo $upload_dir . $film['afis_resmi']; ?>" class="film-poster" alt="<?php echo htmlspecialchars($film['film_adi']); ?>">
                                        <?php else: ?>
                                            <div class="film-poster d-flex align-items-center justify-content-center">
                                                <i class="fas fa-film"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($film['film_adi']); ?></td>
                                    <td><?php echo htmlspecialchars($film['yonetmen']); ?></td>
                                    <td><?php echo $film['yil']; ?></td>
                                    <td><?php echo htmlspecialchars($film['tur']); ?></td>
                                    <td><?php echo number_format((float)($film['imdb'] ?? 0), 1); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFilmModal<?php echo $film['id']; ?>">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#deleteFilmModal<?php echo $film['id']; ?>">
                                            <i class="fas fa-trash-alt"></i> Sil
                                        </button>
                                    </td>
                                </tr>
                                    
                                    <!-- Edit Film Modal -->
                                    <div class="modal fade" id="editFilmModal<?php echo $film['id']; ?>" tabindex="-1" aria-labelledby="editFilmModalLabel<?php echo $film['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content" style="background-color: #1e1e2f; color: #fff; border: 1px solid #ff0000;">
                                                <div class="modal-header" style="border-bottom: 1px solid #ff0000;">
                                                    <h5 class="modal-title" id="editFilmModalLabel<?php echo $film['id']; ?>">
                                                        <i class="fas fa-edit"></i> Film Düzenle: <?php echo htmlspecialchars($film['film_adi']); ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: #ff0000;"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                                                        <input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
                                                        <input type="hidden" name="eski_afis_resmi" value="<?php echo $film['afis_resmi']; ?>">
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="film_adi<?php echo $film['id']; ?>" class="form-label">Film Adı</label>
                                                                    <input type="text" class="form-control" id="film_adi<?php echo $film['id']; ?>" name="film_adi" value="<?php echo htmlspecialchars($film['film_adi']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="yonetmen<?php echo $film['id']; ?>" class="form-label">Yönetmen</label>
                                                                    <input type="text" class="form-control" id="yonetmen<?php echo $film['id']; ?>" name="yonetmen" value="<?php echo htmlspecialchars($film['yonetmen']); ?>" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="yil<?php echo $film['id']; ?>" class="form-label">Yıl</label>
                                                                    <input type="number" class="form-control" id="yil<?php echo $film['id']; ?>" name="yil" min="1900" max="2099" value="<?php echo $film['yil']; ?>" required>
                                                                </div>
                                                            </div>

                                                              <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="imdb<?php echo $film['id']; ?>" class="form-label">IMDB Puanı</label>
                                                                <input type="number" class="form-control" id="imdb<?php echo $film['id']; ?>" name="imdb" min="0" max="10" step="0.1" value="<?php echo number_format((float)$film['imdb'], 1); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                            
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="tur<?php echo $film['id']; ?>" class="form-label">Tür</label>
                                                                    <input type="text" class="form-control" id="tur<?php echo $film['id']; ?>" name="tur" value="<?php echo htmlspecialchars($film['tur']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="aciklama<?php echo $film['id']; ?>" class="form-label">Film Açıklaması</label>
                                                            <textarea class="form-control" id="aciklama<?php echo $film['id']; ?>" name="aciklama" rows="4"><?php echo htmlspecialchars($film['aciklama']); ?></textarea>
                                                        </div>
                                                        
                                                        <div class="mb-4">
                                                            <label for="afis_resmi<?php echo $film['id']; ?>" class="form-label">Afiş Resmi</label>
                                                            <div class="image-preview <?php echo !empty($film['afis_resmi']) ? 'has-image' : ''; ?>" id="edit-image-preview<?php echo $film['id']; ?>">
                                                                <?php if (!empty($film['afis_resmi']) && file_exists($film['afis_resmi'])): ?>
                                                                    <img src="<?php echo $film['afis_resmi']; ?>" alt="<?php echo htmlspecialchars($film['film_adi']); ?>">
                                                                <?php else: ?>
                                                                    <div class="image-preview-text">Afiş resmini görüntülemek için dosya seçin.</div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="image-upload-wrapper">
                                                                <button type="button" class="btn btn-action">
                                                                    <i class="fas fa-upload"></i> Afiş Resmi Seç
                                                                </button>
                                                                <input type="file" class="form-control" id="afis_resmi<?php echo $film['id']; ?>" name="afis_resmi" accept="image/*" onchange="previewImage('afis_resmi<?php echo $film['id']; ?>', 'edit-image-preview<?php echo $film['id']; ?>')">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="modal-footer" style="border-top: 1px solid #ff0000;">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                            <button type="submit" name="edit_film" class="btn btn-action">
                                                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- Delete Film Modal -->
                                    <div class="modal fade" id="deleteFilmModal<?php echo $film['id']; ?>" tabindex="-1" aria-labelledby="deleteFilmModalLabel<?php echo $film['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="background-color: #1e1e2f; color: #fff; border: 1px solid #ff0000;">
                                                <div class="modal-header" style="border-bottom: 1px solid #ff0000;">
                                                    <h5 class="modal-title" id="deleteFilmModalLabel<?php echo $film['id']; ?>">
                                                        <i class="fas fa-trash-alt"></i> Film Sil
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: #ff0000;"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>"<?php echo htmlspecialchars($film['film_adi']); ?>" filmini silmek istediğinizden emin misiniz?</p>
                                                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Bu işlem geri alınamaz!</p>
                                                </div>
                                                <div class="modal-footer" style="border-top: 1px solid #ff0000;">
                                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                        <input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
                                                        <input type="hidden" name="afis_resmi" value="<?php echo $film['afis_resmi']; ?>">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                        <button type="submit" name="delete_film" class="btn btn-danger">
                                                            <i class="fas fa-trash-alt"></i> Evet, Sil
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Henüz film eklenmemiş.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(inputId, previewId) {
            const preview = document.getElementById(previewId);
            const file = document.getElementById(inputId).files[0];
            const reader = new FileReader();
            
            reader.onloadend = function () {
                preview.innerHTML = '';
                preview.classList.add('has-image');
                
                const img = document.createElement('img');
                img.src = reader.result;
                img.alt = 'Film Afişi';
                preview.appendChild(img);
                
                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'delete-image-btn';
                deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                deleteBtn.onclick = function(e) {
                    e.preventDefault();
                    clearImagePreview(inputId, previewId);
                };
                preview.appendChild(deleteBtn);
            }
            
            if (file) {
                reader.readAsDataURL(file);
            } else {
                clearImagePreview(inputId, previewId);
            }
        }
        
        function clearImagePreview(inputId, previewId) {
            const preview = document.getElementById(previewId);
            const input = document.getElementById(inputId);
            
            preview.innerHTML = '<div class="image-preview-text">Afiş resmini görüntülemek için dosya seçin.</div>';
            preview.classList.remove('has-image');
            input.value = '';
        }
    </script>
    </body>
    </html>