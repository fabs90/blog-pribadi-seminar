<?php
require_once 'config/connection.php';
$results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    // QUERY 1
    $stmt = $conn->prepare("SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at, category.name AS category_name
                            FROM posts
                            JOIN post_categories ON posts.id = post_categories.post_id
                            JOIN category ON post_categories.category_id = category.id
                            WHERE posts.title LIKE :search OR posts.content LIKE :search
                            ORDER BY posts.created_at DESC");
    $stmt->execute([':search' => '%' . $search . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // QUERY 2
    $stmt = $conn->prepare("SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at, category.name AS category_name
                            FROM posts
                            JOIN post_categories ON posts.id = post_categories.post_id
                            JOIN category ON post_categories.category_id = category.id
                            ORDER BY posts.created_at DESC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>

<body>
    <h1>Welcome to MyBlog</h1>

    <!-- Search Box -->
    <form action="index.php" method="get">
        <label>Cari Post:</label>
        <input type="text" name="search" placeholder="Search by title or content">
        <input type="submit" value="Cari">
    </form>

    <!-- Filter by Category -->


    <!-- Post Data Card -->
    <div style="margin-top: 10px;">
        <?php
        //   if (count($results) === 0):
        if (!empty($results)):
            foreach ($results as $row):
                ?>
        <div style="border:1px solid #ccc; padding:16px; margin-bottom:16px; border-radius:8px; max-width:400px;">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p><?= htmlspecialchars($row['content']) ?></p>
            <small>Category: <?= htmlspecialchars($row['category_name']) ?> | Created at:
                <?= date('Y-m-d', strtotime($row['created_at'])) ?></small>
        </div>
        <?php
            endforeach;
        else: ?>
        <div style="border:1px solid #ccc; padding:16px; margin-bottom:16px; border-radius:8px; max-width:400px;">
            <h2>No posts found.</h2>
            <p>Please try searching for something else.</p>
            <?php
        endif; ?>
        </div>
</body>

</html>