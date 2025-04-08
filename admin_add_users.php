<?php
session_start();
$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    if (!empty($username) && !empty($password) && in_array($role, ['student', 'teacher', 'admin'])) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            $message = "✅ User added successfully.";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "❌ Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
            width: 90%;
            max-width: 500px;
            animation: slideInUp 0.8s ease-out;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: scale(1.02);
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
            transform: scale(1.02);
            transition: all 0.3s ease-in-out;
        }

        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
            transform: scale(1.02);
            transition: all 0.3s ease-in-out;
        }

        button[type="submit"] {
            transition: all 0.3s ease-in-out;
            transform: scale(1);
        }

        button[type="submit"]:hover {
            background-color: #0b5ed7;
            transform: scale(1.05) rotate(-1deg);
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.4);
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

<div class="form-container">
    <h3 class="mb-3 text-center">Add New User</h3>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="text" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add User</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
