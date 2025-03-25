film_ekle.php
<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = $_POST['ad'];
    $tur = $_POST['tur'];
    $yil = $_POST['yil'];
    $yonetmen = $_POST['yonetmen']; 
    $aciklama = $_POST['aciklama'];

    $query = "INSERT INTO filmler (ad, tur, yil, yonetmen, aciklama) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$ad, $tur, $yil, $yonetmen, $aciklama]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: white;
            padding-top: 50px;
        }
        .container {
            max-width: 600px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Film Ekle</h1>

    <form method="POST">
        <div class="mb-3">
            <label for="ad" class="form-label">Film Adı</label>
            <input type="text" class="form-control" name="ad" id="ad" placeholder="Film Adını Girin" required>
        </div>

        <div class="mb-3">
            <label for="tur" class="form-label">Türü</label>
            <input type="text" class="form-control" name="tur" id="tur" placeholder="Film Türünü Girin" required>
        </div>

        <div class="mb-3">
            <label for="yil" class="form-label">Yıl</label>
            <input type="number" class="form-control" name="yil" id="yil" placeholder="Çıkış Yılını Girin" required>
        </div>

        <div class="mb-3">
            <label for="yonetmen" class="form-label">Yönetmen</label>
            <input type="text" class="form-control" name="yonetmen" id="yonetmen" placeholder="Yönetmeni Girin" required>
        </div>

        <div class="mb-3">
            <label for="aciklama" class="form-label">Açıklama</label>
            <textarea class="form-control" name="aciklama" id="aciklama" rows="4" placeholder="Film Açıklamasını Girin"></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Ekle</button>
    </form>

    <br>
    <a href="index.php" class="btn btn-secondary w-100">Geri Dön</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
