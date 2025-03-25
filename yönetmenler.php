yonetmenlr.php
<?php 
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetmenler</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #1a1a1a;
        }
        .navbar-brand, .nav-link {
            color: #f5c518;
            font-weight: bold;
        }
        .nav-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        table {
            background-color: #333;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border: none;
        }

        th, td {
            color: white;
            font-size: 1.1em;
            text-align: center;
            padding: 15px;
        }

        th {
            background-color: #1a1a1a;
            font-size: 1.2em;
        }

        .btn-warning {
            background-color: #f5c518;
            border-color: #f5c518;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: bold;
        }
        .btn-warning:hover {
            background-color: #d4a300;
        }

        tr:hover {
            background-color: #444;
            cursor: pointer;
        }

        h1 {
            font-size: 2.5em;
            font-weight: bold;
            color: #f5c518;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                        <a class="nav-link" href="filmler.php">Filmler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="yonetmenler.php">Yönetmenler</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Yönetmenler</h1>
        <p class="text-center mb-5">Film dünyasının önemli yönetmenlerini keşfedin.</p>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Yönetmen Adı</th>
                    <th scope="col">Doğum Yılı</th>
                    <th scope="col">En Ünlü Filmi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Christopher Nolan</td>
                    <td>1970</td>
                    <td>Inception</td>
                </tr>
                <tr>
                    <td>Quentin Tarantino</td>
                    <td>1963</td>
                    <td>Pulp Fiction</td>
                </tr>
                <tr>
                    <td>Hayao Miyazaki</td>
                    <td>1941</td>
                    <td>Spirited Away</td>
                </tr>
            </tbody>
        </table>

        <div class="btn-container">
            <a href="yonetmen_ekle.php" class="btn btn-warning">Yönetmen Ekle</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
