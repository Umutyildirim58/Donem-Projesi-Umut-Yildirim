<?php
// Başlatılan oturumu devam ettir
session_start();

// Tüm oturum değişkenlerini temizle
$_SESSION = array();

// Oturum çerezini sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Oturumu sonlandır
session_destroy();

// Giriş sayfasına yönlendir
header("Location: login.php");
exit();
?>
