filmduzenle.php
<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM filmler WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $film = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$film) {
        echo "Film bulunamadı!";
        exit;
    }
} else {
    echo "Film ID'si belirtilmedi!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = $_POST['ad'];
    $tur = $_POST['tur'];
    $yil = $_POST['yil'];
    $aciklama = $_POST['aciklama'];
    $yonetmen = $_POST['yonetmen']; 
    $query = "UPDATE filmler SET ad = ?, tur = ?, yil = ?, aciklama = ?, yonetmen = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$ad, $tur, $yil, $aciklama, $yonetmen, $id]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Düzenle</title>
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
        .btn-warning {
            background-color: #f5c518;
            border-color: #f5c518;
        }
        .btn-warning:hover {
            background-color: #d4a300;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Film Düzenle</h1>

    <form method="POST">
        <div class="mb-3">
            <label for="ad" class="form-label">Film Adı</label>
            <input type="text" class="form-control" name="ad" id="ad" value="<?= htmlspecialchars($film['ad']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="tur" class="form-label">Türü</label>
            <input type="text" class="form-control" name="tur" id="tur" value="<?= htmlspecialchars($film['tur']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="yil" class="form-label">Yıl</label>
            <input type="number" class="form-control" name="yil" id="yil" value="<?= htmlspecialchars($film['yil']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="aciklama" class="form-label">Açıklama</label>
            <textarea class="form-control" name="aciklama" id="aciklama" rows="4"><?= htmlspecialchars($film['aciklama']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="yonetmen" class="form-label">Yönetmen</label>
            <input type="text" class="form-control" name="yonetmen" id="yonetmen" value="<?= htmlspecialchars($film['yonetmen']) ?>" required>
        </div>

        <button type="submit" class="btn btn-warning w-100">Düzenle</button>
    </form>

    <br>
    <a href="index.php" class="btn btn-secondary w-100">Geri Dön</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
