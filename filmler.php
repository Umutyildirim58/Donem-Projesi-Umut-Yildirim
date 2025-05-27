<?php
require_once 'db.php';

$query = "SELECT * FROM filmler";
$stmt = $pdo->query($query);
$filmler = $stmt->fetchAll(PDO::FETCH_ASSOC);   
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212; 
            color: white;
            padding-top: 70px; 
            padding-bottom: 100px; 
            font-family: 'Roboto', sans-serif;
        }
        .container {
            max-width: 1000px;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
            color: #fff;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #f8f9fa;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #28a745;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.85);
        }
        .card {
            background-color: #343a40;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 30px;
        }
        .card-header {
            background-color: #495057;
            color: #f8f9fa;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table-dark thead {
            background-color: #212529;
            border-bottom: 2px solid #444;
        }
        .table-dark tbody tr {
            border-bottom: 1px solid #444;
        }
        .table-dark tbody tr:hover {
            background-color: #343a40;
        }
        .table-dark th, .table-dark td {
            padding: 15px;
            text-align: center;
        }
        .table-dark th {
            font-weight: bold;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #bd2130;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        footer {
            background-color: #212529;
            color: #868e96;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #343a40;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .back-to-home {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="filmler.php">Film Arşivi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto"> 
                <li class="nav-item">
                    <a class="nav-link" href="film_ekle.php">Yeni Film Ekle</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Ana Sayfaya Dön</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center mb-4">Film Arşivi</h1>
    
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Ad</th>
                <th>Tür</th>
                <th>Yıl</th>
                <th>Açıklama</th>
                <th>Yönetmen</th> 
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($filmler) > 0): ?>
                <?php foreach ($filmler as $film): ?>
                    <tr>
                        <td><?= htmlspecialchars($film['id']) ?></td>
                        <td><?= htmlspecialchars($film['ad']) ?></td>
                        <td><?= htmlspecialchars($film['tur']) ?></td>
                        <td><?= htmlspecialchars($film['yil']) ?></td>
                        <td><?= htmlspecialchars($film['aciklama']) ?></td>
                        <td><?= htmlspecialchars($film['yonetmen']) ?></td>
                        <td>
                            
                            <a href="film_duzenle.php?id=<?= $film['id'] ?>" class="btn btn-warning btn-sm">Düzenle</a>
                            <a href="film_sil.php?id=<?= $film['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu filmi silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Henüz eklenmiş bir film yok!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="film_ekle.php" class="btn btn-success w-100">Yeni Film Ekle</a>
    
    
    <div class="back-to-home">
        <a href="index.php" class="btn btn-primary w-100">Ana Sayfaya Dön</a>
    </div>
</div>


<footer>
    <p>© 2024 Film Arşivi | Tüm Hakları Saklıdır</p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
