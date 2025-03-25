düzenle.php
<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM filmler WHERE id = :id");
    $stmt->bindParam(':id', $id);
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = $_POST['ad'];
    $tur = $_POST['tur'];
    $yil = $_POST['yil'];

    $updateStmt = $pdo->prepare("UPDATE filmler SET ad = :ad, tur = :tur, yil = :yil WHERE id = :id");
    $updateStmt->bindParam(':ad', $ad);
    $updateStmt->bindParam(':tur', $tur);
    $updateStmt->bindParam(':yil', $yil);
    $updateStmt->bindParam(':id', $id);
    
    if ($updateStmt->execute()) {
        echo "Film başarıyla güncellendi!";
        header("Location: index.php");
        exit;
    } else {
        echo "Film güncellenirken bir hata oluştu!";
    }
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
        }
        .container {
            margin-top: 50px;
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
            <input type="text" class="form-control" id="ad" name="ad" value="<?= htmlspecialchars($film['ad']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="tur" class="form-label">Türü</label>
            <input type="text" class="form-control" id="tur" name="tur" value="<?= htmlspecialchars($film['tur']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="yil" class="form-label">Yıl</label>
            <input type="number" class="form-control" id="yil" name="yil" value="<?= htmlspecialchars($film['yil']) ?>" required>
        </div>
        <button type="submit" class="btn btn-warning w-100">Film Güncelle</button>
    </form>
    <br>
    <a href="index.php" class="btn btn-secondary w-100">Geri Dön</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
