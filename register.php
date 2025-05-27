<?php
require_once 'db.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];
    
    // Validate inputs
    if (empty($kullanici_adi) || empty($email) || empty($sifre) || empty($sifre_tekrar)) {
        $error = "Lütfen tüm alanları doldurun!";
    } elseif ($sifre !== $sifre_tekrar) {
        $error = "Şifreler eşleşmiyor!";
    } elseif (strlen($sifre) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Geçerli bir e-posta adresi giriniz!";
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ?");
        $stmt->execute([$kullanici_adi]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Bu kullanıcı adı zaten kullanılıyor!";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Bu e-posta adresi zaten kullanılıyor!";
            } else {
                // Register the user
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO kullanicilar (kullanici_adi, email, sifre, kayit_tarihi) VALUES (?, ?, ?, NOW())");
                
                if ($stmt->execute([$kullanici_adi, $email, $hashed_password])) {
                    $success = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
                } else {
                    $error = "Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.";
                }
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
    <title>Kayıt Ol - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1c1c1c, #3a0ca3);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        }
        
        .navbar {
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            padding: 15px;
            border-bottom: 2px solid #ffd700;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        
        h2 {
            color: #ffd700;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ffd700;
            color: #fff;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-color: #ffd700;
            box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }
        
        .btn-register {
            background-color: #daa520;
            border-color: #daa520;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 50px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        
        .btn-register:hover {
            background-color: #ffd700;
            border-color: #ffd700;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #ffd700;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
        }
        
        .password-requirements {
            font-size: 0.8rem;
            color: #ccc;
            margin-top: -15px;
            margin-bottom: 15px;
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
    <div class="register-container">
        <h2><i class="fas fa-user-plus me-2"></i>Kayıt Ol</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
                <div class="mt-2">
                    <a href="login.php" class="btn btn-sm btn-outline-light">Giriş Yap</a>
                </div>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">E-posta Adresi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="sifre" class="form-label">Şifre</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="sifre" name="sifre" required>
                </div>
                <div class="password-requirements">
                    * Şifreniz en az 6 karakter uzunluğunda olmalıdır.
                </div>
            </div>
            
            <div class="mb-3">
                <label for="sifre_tekrar" class="form-label">Şifre Tekrar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-register">Kayıt Ol</button>
        </form>
        
        <div class="login-link">
            Zaten hesabınız var mı? <a href="login.php">Giriş yapın</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>