<?php
require_once 'db.php';
session_start();

// Admin kontrolü
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik']);
    $yil = intval($_POST['yil']);
    $tur = trim($_POST['tur']);
    $yonetmen = trim($_POST['yonetmen']);
    $imdb_puani = floatval($_POST['imdb_puani']);
    $ozet = trim($_POST['ozet']);

    if (empty($baslik) || empty($tur) || empty($yonetmen) || empty($ozet)) {
        $error = "Lütfen tüm gerekli alanları doldurun!";
    } else {
        $poster_name = null;

        // Poster yüklenmişse
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['poster']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            if (!in_array(strtolower($ext), $allowed)) {
                $error = "Geçersiz dosya türü! Sadece JPG, JPEG, PNG ve WEBP formatları kabul edilir.";
            } else {
                // uploads klasörü yoksa oluştur
                if (!file_exists('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                // Sadece dosya adını üret
                $poster_name = 'film_' . uniqid() . '.' . $ext;

                // Tam yolu oluştur (ama sadece yüklemek için kullanılır, veritabanına girmez!)
                $destination = 'uploads/' . $poster_name;

                // Dosyayı yükle
                if (!move_uploaded_file($_FILES['poster']['tmp_name'], $destination)) {
                    $error = "Dosya yüklenirken hata oluştu!";
                }
            }
        }

        if (empty($error)) {
            // Veritabanına sadece dosya adını kaydet!
            $stmt = $pdo->prepare("INSERT INTO filmler (baslik, yil, tur, yonetmen, imdb_puani, ozet, poster) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$baslik, $yil, $tur, $yonetmen, $imdb_puani, $ozet, $poster_name])) {
                $success = "Film başarıyla eklendi!";
                $baslik = $tur = $yonetmen = $ozet = '';
                $yil = $imdb_puani = '';
            } else {
                $error = "Film eklenirken bir hata oluştu!";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Ekle - Film Arşivi</title>
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
        
        .form-container {
            background: #1e1e2f;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            margin: 30px 0;
        }
        
        h2 {
            color: #ff0000;
            margin-bottom: 20px;
        }
        
        .form-label {
            color: #ddd;
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #444;
            color: #fff;
            margin-bottom: 20px;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #ff0000;
            box-shadow: 0 0 8px rgba(255, 0, 0, 0.5);
        }
        
        .input-group-text {
            background-color: rgba(255, 0, 0, 0.7);
            border: 1px solid rgba(255, 0, 0, 0.7);
            color: #fff;
        }
        
        .btn-save {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
        }
        
        .btn-save:hover {
            background-color: #218838;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        
        .alert {
            border-radius: 10px;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .custom-file-label {
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px dashed #444;
            color: #fff;
            padding: 40px 20px;
            text-align: center;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .custom-file-label:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #ff0000;
        }
        
        .custom-file-input {
            display: none;
        }
        
        .upload-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #ff0000;
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
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-container">
        <h2><i class="fas fa-plus-circle me-2"></i>Yeni Film Ekle</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="baslik" class="form-label">Film Başlığı *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                            <input type="text" class="form-control" id="baslik" name="baslik" required 
                                   value="<?php echo isset($baslik) ? htmlspecialchars($baslik) : ''; ?>">
                        </div>
            </div>
            
            <div class="mb-3">
                <label for="ozet" class="form-label">Film Özeti *</label>
                <textarea class="form-control" id="ozet" name="ozet" rows="5" required><?php echo isset($ozet) ? htmlspecialchars($ozet) : ''; ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="admin_panel.php" class="btn btn-cancel">
                    <i class="fas fa-arrow-left me-2"></i>İptal
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save me-2"></i>Filmi Kaydet
                </button>
            </div>        </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="yil" class="form-label">Yapım Yılı</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="number" class="form-control" id="yil" name="yil" min="1900" max="<?php echo date('Y'); ?>" 
                                           value="<?php echo isset($yil) ? htmlspecialchars($yil) : date('Y'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imdb_puani" class="form-label">IMDB Puanı</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    <input type="number" class="form-control" id="imdb_puani" name="imdb_puani" step="0.1" min="0" max="10" 
                                           value="<?php echo isset($imdb_puani) ? htmlspecialchars($imdb_puani) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tur" class="form-label">Film Türü *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                            <input type="text" class="form-control" id="tur" name="tur" required 
                                   value="<?php echo isset($tur) ? htmlspecialchars($tur) : ''; ?>" 
                                   placeholder="Örn: Dram, Komedi, Aksiyon">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="yonetmen" class="form-label">Yönetmen *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-video"></i></span>
                            <input type="text" class="form-control" id="yonetmen" name="yonetmen" required 
                                   value="<?php echo isset($yonetmen) ? htmlspecialchars($yonetmen) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Film Posteri</label>
                        <label for="poster" class="custom-file-label d-block">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <div>Poster yüklemek için tıklayın</div>
                            <small class="text-muted">Max 5MB - JPG, PNG, WEBP</small>
                        </label>
                        <input type="file" class="custom-file-input" id="poster" name="poster" accept="image/jpeg,image/png,image/webp">
                    </div>
                </div>