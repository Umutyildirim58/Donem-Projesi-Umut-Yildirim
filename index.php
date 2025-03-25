<?php 
require_once 'db.php';
session_start();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Arşivi - Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.9);
            padding: 15px;
            border-bottom: 2px solidrgb(37, 159, 66);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .card {
            background: #2c3e50;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            height: 300px;
            object-fit: cover;
            border-bottom: 2px solid #28a745;
        }
        .card-body {
            text-align: center;
        }
        .table {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
        }
        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        footer {
            background: #141e30;
            color: #bbb;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Film Arşivi</a>
            <div class="ms-auto">
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="me-3">Hoşgeldin, <?php echo $_SESSION['user']; ?>!</span>
                    <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-success">Giriş Yap</a>
                    <a href="register.php" class="btn btn-warning">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container text-center mt-5">
        <h1 class="display-4">Film Arşivine Göz Atın</h1>
        <p class="lead">En sevdiğiniz filmleri keşfedin ve koleksiyonunuzu genişletin.</p>
    </div>
    
    <div class="container mt-4">
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" placeholder="Film Ara..." class="form-control w-50 d-inline">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <div class="row">
            <?php
            $query = "SELECT * FROM filmler";
            if (!empty($_GET['search'])) {
                $search = $_GET['search'];
                $query .= " WHERE ad LIKE '%$search%' OR tur LIKE '%$search%' OR yonetmen LIKE '%$search%'";
            }
            $stmt = $pdo->query($query);
            
            if ($stmt->rowCount() > 0) {
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card'>";
                    echo "<img src='uploads/" . htmlspecialchars($row['resim']) . "' class='card-img-top' alt='Film Resmi'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($row['ad']) . "</h5>";
                    echo "<p><strong>Türü:</strong> " . htmlspecialchars($row['tur']) . "</p>";
                    echo "<p><strong>Yıl:</strong> " . htmlspecialchars($row['yil']) . "</p>";
                    echo "<p><strong>Yönetmen:</strong> " . htmlspecialchars($row['yonetmen']) . "</p>";
                    echo "<p><strong>IMDB Puanı:</strong> " . htmlspecialchars($row['imdb']) . "</p>";
                    echo "<a href='#' class='btn btn-primary'><i class='fa fa-film'></i> Detaylar</a>";
                    echo "</div></div></div>";
                }
            } else {
                echo "<p class='text-center'>Hiç film bulunamadı.</p>";
            }
            ?>
        </div>
    </div>
    
    <footer>
        <p>© 2024 Film Arşivi | Premium Edition</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
