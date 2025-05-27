<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Doğru veritabanı bağlantı dosyasının yolunu kontrol et
if (file_exists('db.php')) {
    require_once 'db.php';
} else if (file_exists('../db.php')) {
    require_once '../db.php';
} else {
    die("Veritabanı bağlantı dosyası bulunamadı. Lütfen db.php dosyasının konumunu kontrol edin.");
}

// Start the session
session_start();

// Redirect if already logged in as admin
if (isset($_SESSION['admin'])) {
    header("Location: admin_panel.php");
    exit();
}

$error = '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $admin_kullanici = trim(htmlspecialchars($_POST['admin_kullanici'] ?? ''));
    $admin_sifre = $_POST['admin_sifre'] ?? '';
    
    if (empty($admin_kullanici) || empty($admin_sifre)) {
        $error = "Lütfen tüm alanları doldurun!";
    } else {
        try {
            // Check admin credentials - Add debug info
            echo "<!-- Giriş denemesi: " . $admin_kullanici . " -->";
            
            $stmt = $pdo->prepare("SELECT * FROM adminler WHERE kullanici_adi = ?");
            $stmt->execute([$admin_kullanici]);
            
            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Debug info - hash verification
                echo "<!-- Parola kontrolü yapılıyor -->";
                
                // Check if password is stored as plain text in database
                if ($admin_sifre == $admin['sifre']) {
                    // Plain text password match
                    $_SESSION['admin'] = $admin['kullanici_adi'];
                    $_SESSION['admin_id'] = $admin['id'];
                    session_regenerate_id(true);
                    header("Location: admin_panel.php");
                    exit();
                }
                // Check if password is hashed with password_hash()
                else if (password_verify($admin_sifre, $admin['sifre'])) {
                    // Hashed password match
                    $_SESSION['admin'] = $admin['kullanici_adi'];
                    $_SESSION['admin_id'] = $admin['id'];
                    session_regenerate_id(true);
                    header("Location: admin_panel.php");
                    exit();
                }
                // Check if password is MD5 hashed
                else if (md5($admin_sifre) == $admin['sifre']) {
                    // MD5 password match
                    $_SESSION['admin'] = $admin['kullanici_adi'];
                    $_SESSION['admin_id'] = $admin['id'];
                    session_regenerate_id(true);
                    header("Location: admin_panel.php");
                    exit();
                }
                // Check if password is SHA1 hashed
                else if (sha1($admin_sifre) == $admin['sifre']) {
                    // SHA1 password match
                    $_SESSION['admin'] = $admin['kullanici_adi'];
                    $_SESSION['admin_id'] = $admin['id'];
                    session_regenerate_id(true);
                    header("Location: admin_panel.php");
                    exit();
                }
                else {
                    $error = "Kullanıcı adı veya şifre hatalı! (Şifre eşleşmedi)";
                }
            } else {
                $error = "Kullanıcı adı veya şifre hatalı! (Kullanıcı bulunamadı)";
            }
        } catch (PDOException $e) {
            // Log the error with details
            error_log("Database error: " . $e->getMessage());
            $error = "Veritabanı hatası oluştu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1c1c1c, #3a0ca3);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            background: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.2);
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
        
        h2 {
            color: #ff0000;
            text-align: center;
            margin-bottom: 30px;
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
        
        .btn-login {
            background-color: #ff0000;
            border-color: #ff0000;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 50px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        
        .btn-login:hover {
            background-color: #cc0000;
            border-color: #cc0000;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #ff0000;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
        }

        /* Fix for mobile responsiveness */
        @media (max-width: 576px) {
            .login-container {
                margin: 50px auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Film Arşivi</a>
    </div>
</nav>

<div class="container">
    <div class="login-container">
        <h2><i class="fas fa-user-shield me-2"></i>Admin Girişi</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="admin_kullanici" class="form-label">Admin Kullanıcı Adı</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-cog"></i></span>
                    <input type="text" class="form-control" id="admin_kullanici" name="admin_kullanici" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="admin_sifre" class="form-label">Admin Şifresi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="admin_sifre" name="admin_sifre" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">Admin Girişi</button>
        </form>
        
        <div class="back-link">
            <a href="index.php">Ana Sayfaya Dön</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>