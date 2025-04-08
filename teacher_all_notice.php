<?php
session_start();
$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");
if (!$result) die("Query Failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>All Notices</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>

  <style>
    body {
      background: linear-gradient(135deg, #60d0fc, #c4faee);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .custom-navbar {
      background: linear-gradient(135deg, #60d0fc, #c4faee);
      padding: 1rem 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 0 0 12px 12px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

    .main-container {
      flex: 1;
      padding: 30px 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .notice-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      width: 100%;
      max-width: 700px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .notice-card:hover {
      transform: translateY(-5px);
    }

    .notice-title {
      font-weight: bold;
      font-size: 1.2rem;
      color: #0d6efd;
    }

    .notice-meta {
      font-size: 0.85rem;
      color: #555;
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      .notice-card {
        width: 95%;
      }

      .custom-navbar {
        padding: 1rem;
      }

      .navbar-brand {
        font-size: 1.2rem;
      }

      .nav-link {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ClassNoticeBoard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="teacher.html">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="teacher_all_notice.php">AllNotices</a></li>
        <li class="nav-item"><a class="nav-link" href="teacher_my_notices.php">MyNotices</a></li>
        <li class="nav-item"><a class="nav-link" href="teacher_post_notice.php">PostNewNotice</a></li>
      </ul>
      <a href="index.html" class="btn btn-outline-success">Logout</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <h2 class="mb-4">📋 All Notices</h2>

  <?php while($row = $result->fetch_assoc()): ?>
    <div class="notice-card">
      <div class="notice-title"><?php echo htmlspecialchars($row['title']); ?></div>
      <div class="notice-content mt-2"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>
      <div class="notice-meta">
        Posted by: <?php echo htmlspecialchars($row['posted_by']); ?> | 
        <?php echo htmlspecialchars($row['posted_on']); ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
