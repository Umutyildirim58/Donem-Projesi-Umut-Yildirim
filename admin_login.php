<?php
// Start the session
session_start();

// Redirect to admin panel if already logged in
if (isset($_SESSION['admin']) && $_SESSION['admin'] === 'admin') {
    header("Location: admin_panel.php");
    exit;
}

// Initialize variables
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define admin credentials
    $admin_username = "UMUT";
    $admin_password = "Umut123";
    
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate credentials
    if ($username === $admin_username && $password === $admin_password) {
        // Set session variables
        $_SESSION['admin'] = 'admin';
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_username'] = $admin_username;
        
        // Redirect to admin panel
        header("Location: admin_panel.php");
        exit;
    } else {
        $error_message = "Geçersiz kullanıcı adı veya şifre!";
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        
        h2 {
            color: #ff0000;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ff0000;
            color: #fff;
            margin-bottom: 20px;
            padding: 12px;
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
            padding: 10px 20px;
            border-radius: 50px;
            text-transform: uppercase;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background-color: #cc0000;
            border-color: #cc0000;
        }
        
        .input-group-text {
            background-color: rgba(255, 0, 0, 0.2);
            border: 1px solid #ff0000;
            color: #ff0000;
        }
        
        .logo-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-text {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ff0000;
            text-decoration: none;
        }
        
        .back-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-wrapper">
        <div class="logo-text">Film Arşivi</div>
        <p class="text-muted">Admin Paneli Girişi</p>
    </div>
    
    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adı" required>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifre" required>
                <button class="btn btn-outline-danger" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
        
        <button type="submit" class="btn btn-login btn-lg">
            <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
        </button>
    </form>
    
    <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left me-1"></i> Ana Sayfaya Dön
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        // Toggle password visibility
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
</script>
</body>
</html>