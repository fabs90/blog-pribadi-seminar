<?php
require_once 'config/connection.php';
$slugParam = $_GET["slug"];

$stmt = $conn->prepare("SELECT * FROM posts WHERE slug = :slug");
$stmt->execute([':slug' => $slugParam]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($post)) {
    $postId = $post['id'];
    $stmt = $conn->prepare("DELETE FROM post_categories WHERE post_id = :post_id");
    $stmt->execute([':post_id' => $postId]);

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $postId]);
}
header('Location: dashboard.php');
exit();
?>