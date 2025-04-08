<?php
session_start();
$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Check if teacher is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];

// ✅ Handle delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ? AND posted_by = ?");
    $stmt->bind_param("is", $delete_id, $username);
    $stmt->execute();
    $stmt->close();
}

// ✅ Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $new_title = $conn->real_escape_string($_POST['new_title']);
    $new_content = $conn->real_escape_string($_POST['new_content']);

    $stmt = $conn->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ? AND posted_by = ?");
    $stmt->bind_param("ssis", $new_title, $new_content, $edit_id, $username);
    $stmt->execute();
    $stmt->close();
}

// ✅ Fetch notices posted by this teacher
$result = $conn->query("SELECT * FROM notices WHERE posted_by = '$username' ORDER BY posted_on DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notices</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .notice-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .notice-title {
            color: #0d6efd;
            font-weight: bold;
        }
        .notice-meta {
            font-size: 0.9rem;
            color: #555;
        }
        .custom-navbar {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            padding: 1rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 12px 12px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            width: 100vw;
        }
        .custom-navbar .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #0d6efd;
        }
        .custom-navbar .nav-link {
            margin-right: 20px;
            font-size: 1rem;
            color: #333;
            transition: color 0.3s ease;
        }
        .custom-navbar .nav-link:hover,
        .custom-navbar .nav-link.active {
            color: #0d6efd;
        }
        .custom-navbar .btn-outline-success {
            border-radius: 20px;
            padding: 5px 20px;
            transition: all 0.3s ease;
        }
        .custom-navbar .btn-outline-success:hover {
            background-color: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }
        .delete-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">ClassNoticeBoard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="teacher.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher_all_notice.php">AllNotices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="teacher_my_notices.php">MyNotices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher_post_notice.php">PostNewNotice</a>
                </li>
            </ul>
            <a href="index.html" class="btn btn-outline-success">Logout</a>
        </div>
    </div>
</nav>

<h2 class="mb-4 mt-3">📌 My Notices</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="notice-container">
        <h4 class="notice-title"><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><?php echo htmlspecialchars($row['content']); ?></p>
        <div class="notice-meta">
            Posted on: <?php echo htmlspecialchars($row['posted_on']); ?>
        </div>

        <!-- Delete + Edit Buttons -->
        <div class="d-flex gap-2 mt-3">
            <!-- Delete Form -->
            <form method="post">
                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>

            <!-- Edit Button -->
            <button class="btn btn-warning btn-sm" type="button" onclick="toggleEditForm(<?php echo $row['id']; ?>)">Edit</button>
        </div>

        <!-- Hidden Edit Form -->
        <div id="edit-form-<?php echo $row['id']; ?>" style="display:none; margin-top:10px;">
            <form method="post">
                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                <div class="mb-2">
                    <input type="text" name="new_title" class="form-control" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                </div>
                <div class="mb-2">
                    <textarea name="new_content" class="form-control" rows="3" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </form>
        </div>
    </div>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
