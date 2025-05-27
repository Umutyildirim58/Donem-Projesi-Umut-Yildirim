<?php
require_once 'db.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];
    
    if (empty($kullanici_adi) || empty($sifre)) {
        $error = "Lütfen tüm alanları doldurun!";
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ?");
        $stmt->execute([$kullanici_adi]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if (password_verify($sifre, $user['sifre'])) {
                // Login successful
                $_SESSION['user'] = $user['kullanici_adi'];
                $_SESSION['user_id'] = $user['id'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Kullanıcı adı veya şifre hatalı!";
            }
        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Film Arşivi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1c1c1c, #3a0ca3);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
        }
        
        .login-container {
            max-width: 450px;
            margin: 100px auto;
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
        
        .btn-login {
            background-color: #daa520;
            border-color: #daa520;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 50px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        
        .btn-login:hover {
            background-color: #ffd700;
            border-color: #ffd700;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #ffd700;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
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
        <h2><i class="fas fa-film me-2"></i>Giriş Yap</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
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
                <label for="sifre" class="form-label">Şifre</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="sifre" name="sifre" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>
        
        <div class="register-link">
            Hesabınız yok mu? <a href="register.php">Kayıt olun</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>