<?php 
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Arşivi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
            margin-top: 70px; 
            margin-bottom: 100px;
        }
        .container {
            max-width: 1200px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 2rem;
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
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        h1, h2 {
            color: #f8f9fa;
            text-align: center;
            font-weight: bold;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        footer {
            background-color: #212529;
            color: #868e96;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #343a40;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        nav.navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .container {
            margin-top: 120px; 
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Film Arşivi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="filmler.php">Film ekle & Düzenle</a>
                    </li>  
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>36504123022-Umut-Yıldırım</h2>
        <h1>Film Arşivine Göz Atın</h1>
        <p class="text-center mb-5">En sevdiğiniz filmleri keşfedin, arşivinize yeni eklemeler yapın.</p>

        <div class="card p-4">
            <div class="card-header">
                Film Listesi
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Film Adı</th>
                            <th scope="col">Türü</th>
                            <th scope="col">Yıl</th>
                            <th scope="col">Yönetmeni</th>
                            <th scope="col">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        $sql = "SELECT * FROM filmler";
                        $stmt = $pdo->query($sql); 


                        if ($stmt->rowCount() > 0) { 
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['ad']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['tur']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['yil']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['yonetmen']) . "</td>"; 
                                echo "<td>" . htmlspecialchars($row['aciklama']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Hiç film bulunamadı.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2024 Film Arşivi | Tüm Hakları Saklıdır</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>  
