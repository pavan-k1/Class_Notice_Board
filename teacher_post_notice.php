<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $posted_by = $_SESSION['username'];

    $sql = "INSERT INTO notices (title, content, posted_by, posted_on) 
            VALUES ('$title', '$content', '$posted_by', NOW())";

    $message = $conn->query($sql) === TRUE 
        ? "✅ Notice posted successfully!" 
        : "❌ Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post New Notice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notice-form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
            margin-top: 30px;
            animation: slideUp 0.7s ease forwards;
            transform: translateY(50px);
            opacity: 0;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
            font-size: 1.6rem;
            color: #0d6efd;
            transition: transform 0.3s ease;
        }

        .custom-navbar .navbar-brand:hover {
            transform: scale(1.1);
        }

        .custom-navbar .nav-link {
            margin-right: 20px;
            font-size: 1rem;
            color: #333;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .custom-navbar .nav-link:hover,
        .custom-navbar .nav-link.active {
            color: #0d6efd;
            transform: scale(1.05);
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

        .form-control, .btn-primary {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            transform: scale(1.02);
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #084298;
            transform: scale(1.05);
        }

        h3 {
            text-align: center;
            color: #0d6efd;
            animation: pulse 1s infinite alternate;
        }

        @keyframes pulse {
            from { transform: scale(1); }
            to { transform: scale(1.02); }
        }

        .alert {
            animation: fadeIn 0.5s ease-out;
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



<div class="notice-form-container">
    <h3 class="mb-4">Post a New Notice</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="title" class="form-label">Notice Title</label>
            <input type="text" class="form-control" id="title" name="title" required maxlength="100">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Notice Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Post Notice</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
