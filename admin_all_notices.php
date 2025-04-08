<?php
session_start();
$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Notice deleted successfully.";
    } else {
        $message = "Failed to delete notice.";
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");
if (!$result) die("Query Failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - All Notices</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInBody 1s ease-in-out;
        }

        @keyframes fadeInBody {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notice-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
            margin-bottom: 30px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            transform: scale(0.95);
            animation: slideFadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .notice-container:hover {
            transform: scale(1.02) rotateZ(1deg);
            box-shadow: 0px 12px 25px rgba(0,0,0,0.25);
        }

        @keyframes slideFadeIn {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .notice-title {
            color: #0d6efd;
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 10px;
            animation: titleBounce 1.2s ease-in-out infinite alternate;
        }

        @keyframes titleBounce {
            from { transform: translateY(0); }
            to { transform: translateY(-5px); }
        }

        .notice-meta {
            font-size: 0.9rem;
            color: #555;
            font-style: italic;
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
            transition: transform 0.4s;
        }

        .custom-navbar .navbar-brand:hover {
            transform: scale(1.1) rotate(-1deg);
        }

        .custom-navbar .nav-link {
            margin-right: 20px;
            font-size: 1rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .nav-link.active {
            color: #0d6efd;
            transform: translateY(-2px);
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
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">ClassNoticeBoard</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" href="admin.html">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin_all_notices.php">AllNotices</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin_add_users.php">AddUsers</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin_delete_user.php">DeleteUsers</a>
          </li>
        </ul>
        <a href="index.html" class="btn btn-outline-success">Logout</a>
      </div>
    </div>
  </nav>

<h2 class="mb-4 mt-4">📋 All Notices</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-info w-75 text-center"><?php echo $message; ?></div>
<?php endif; ?>

<?php while($row = $result->fetch_assoc()): ?>
    <div class="notice-container">
        <h4 class="notice-title"><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><?php echo htmlspecialchars($row['content']); ?></p>
        <div class="notice-meta">
            Posted by: <?php echo htmlspecialchars($row['posted_by']); ?> |
            <?php echo htmlspecialchars($row['posted_on']); ?>
        </div>
        <form method="post" class="delete-btn mt-3 text-end" onsubmit="return confirm('Delete this notice?');">
            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
