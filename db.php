<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=film_arsivi;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
    
}