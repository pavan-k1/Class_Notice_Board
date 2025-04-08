<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "notice_board_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT title, content, posted_by, posted_on FROM notices ORDER BY posted_on DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Notices</title>
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

<nav class="navbar navbar-expand-lg bg-body-tertiary custom-navbar">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">ClassNoticeBoard</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link "  href="student.html">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="student_notices.php">Notices</a>
              </li>
            
            
            </ul>
            <a href="index.html" class="btn btn-outline-success">Logout</a>

          </div>
        </div>
      </nav>

<h2 class="mt-4 mb-4">📢 All Class Notices</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="notice-container">
            <h4 class="notice-title"><?= htmlspecialchars($row['title']) ?></h4>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <div class="notice-meta">
                Posted by: <?= htmlspecialchars($row['posted_by']) ?> |
                <?= $row['posted_on'] ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No notices available right now.</p>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
