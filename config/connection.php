<?php
// connection.php
try {
    $conn = new PDO("mysql:host=localhost;dbname=test_blog_pribadi", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle koneksi gagal agar lebih mudah dibaca
    echo "Connection failed: " . $e->getMessage();
}
?>