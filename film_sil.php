film_sil.php
<?php
require_once 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id']; // ID değerini güvenli bir şekilde alıyoruz
    $query = "DELETE FROM filmler WHERE id = ?";
    $statement = $pdo->prepare($query); // Yazım hatası düzeltildi

    try {
        $statement->execute([$id]); // Silme işlemini gerçekleştiriyoruz
        header('Location: index.php'); // İşlem sonrası yönlendirme
        exit;
    } catch (PDOException $e) {
        die("Silme işlemi sırasında bir hata oluştu: " . $e->getMessage());
    }
} else {
    die("Geçersiz ID değeri.");
}
?>
