<?php
require_once 'config/connection.php';
session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    // QUERY 1
    // QUERY 1 (dengan pencarian)
    $stmt = $conn->prepare("
    SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at, GROUP_CONCAT(category.name SEPARATOR ', ') AS category_names FROM posts JOIN post_categories ON posts.id = post_categories.post_id JOIN category ON post_categories.category_id = category.id WHERE posts.title LIKE :search OR posts.content LIKE :search GROUP BY posts.id ORDER BY posts.created_at DESC
");
    $stmt->execute([':search' => '%' . $search . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // QUERY 2
    $stmt = $conn->prepare("
    SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at, GROUP_CONCAT(category.name SEPARATOR ', ') AS category_names FROM posts JOIN post_categories ON posts.id = post_categories.post_id JOIN category ON post_categories.category_id = category.id GROUP BY posts.id ORDER BY posts.created_at DESC");
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
    <h1>Dashboard Posts</h1>
    <a href="create.php">Create New Post</a>

    <!-- Search Box -->
    <form action="dashboard.php" method="get">
        <label>Cari Post:</label>
        <input type="text" name="search" placeholder="Search by title or content">
        <input type="submit" value="Cari">
    </form>

    <!-- Filter by Category -->

    <table border="1">
        <tr>
            <th>Category</th>
            <th>Title</th>
            <th>Content</th>
            <th>Slug</th>
            <th>Created at</th>
            <th colspan="2">Action</th>
        </tr>
        <?php
        // cek result
        if (count($results) === 0): ?>
            <tr>
                <td colspan="7">No posts found.</td>
            </tr>

        <?php else:
            foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['category_names']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['content']) ?></td>
                    <td><?= htmlspecialchars($row['slug']) ?></td>
                    <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                    <td><a href="update.php?slug=<?= urlencode($row['slug']) ?>">Edit</a></td>
                    <td><a href="delete.php?slug=<?= urlencode($row['slug']) ?>"
                            onclick="return confirm('Are you sure you want to delete this post?')">Delete</a></td>
                </tr>
            <?php endforeach;
        endif;
        ?>
    </table>
    <div>
        <a href="logout.php">Logout</a>
    </div>
</body>

</html>