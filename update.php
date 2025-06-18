<?php
require_once 'config/connection.php';
if (!isset($_GET['slug'])) {
    header('Location: index.php');
    exit();
}
$slugParams = $_GET['slug'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE slug = :slug");
$stmt->execute([':slug' => $_GET['slug']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $stmt = $conn->prepare("UPDATE posts SET title = :title, content = :content, slug = :slug WHERE slug = :old_slug");
    $stmt->execute([':title' => $title, ':content' => $content, ':slug' => $slug, ':old_slug' => $_GET['slug']]);

    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
</head>

<body>
    <form action="update.php?slug=<?= $slugParams ?>" method="POST">
        <div>
            <input type="text" name="title" placeholder="Title" value="<?php echo $post["title"] ?>" required>
            <br>
            <input type="text" name="content" placeholder="Content" value="<?php echo $post["content"] ?>" required>
            <br>

            <button type="submit" name="submit">Update</button>
        </div>

    </form>
</body>

</html>