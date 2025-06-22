<?php
require_once 'config/connection.php';

if (!empty($_POST)) {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO posts (title, content, slug, created_at) VALUES (:title, :content, :slug, :created_at)");
    $stmt->execute([':title' => $title, ':content' => $content, ':slug' => $slug, ':created_at' => $date]);

    $postId = $conn->lastInsertId();

    foreach ($category as $cat) {
        $stmt = $conn->prepare("INSERT INTO post_categories (post_id,category_id) VALUES (:post_id, :category_id)");
        $stmt->execute([':post_id' => $postId, 'category_id' => $cat]);
    }

    header('Location: dashboard.php');
    exit();
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f7f9fc;
        padding: 40px;
    }

    .container {
        background: #fff;
        max-width: 600px;
        margin: auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    label {
        font-weight: bold;
        display: block;
        margin: 15px 0 5px;
    }

    select,
    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    button {
        background-color: #007bff;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        margin-top: 20px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
    }

    button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <?php
    $stmt = $conn->prepare("SELECT * FROM category");
    $stmt->execute();
    $rowCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container">
        <h2>Create New Post</h2>
        <form action="create.php" method="POST">
            <label for="category">Choose Categories:</label>
            <select name="category[]" id="category" multiple="multiple" required>
                <?php foreach ($rowCategories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="title">Post Title:</label>
            <input type="text" name="title" placeholder="Enter title" required>

            <label for="content">Post Content:</label>
            <input type="text" name="content" placeholder="Enter content" required>

            <button type="submit" name="submit">Create Post</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#category').select2({
            placeholder: "Select one or more categories",
            allowClear: true
        });
    });
    </script>
</body>

</html>