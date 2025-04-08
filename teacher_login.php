<?php
session_start();
require 'db.php'; // This should contain your $conn setup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = 'teacher';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // For now we assume plain password, ideally use password_verify()
            if ($password === $user['password']) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: teacher.html");
                exit;
            } else {
                echo "❌ Invalid password.";
            }
        } else {
            echo "❌ Teacher not found or wrong role.";
        }
    } else {
        echo "❌ Query failed.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request.";
}
