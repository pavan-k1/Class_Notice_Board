<?php
session_start();
$conn = new mysqli("localhost", "root", "", "notice_board_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_username'])) {
    $username = $_POST['delete_username'];

    // Get user role
    $stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    if ($role === 'admin') {
        $enteredPassword = $_POST['admin_password'] ?? '';

        // Check password of the admin to be deleted
        $checkStmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->bind_result($storedPassword);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($enteredPassword !== $storedPassword) {
            $message = "Password incorrect. Admin not deleted.";
        } else {
            $delUser = $conn->prepare("DELETE FROM users WHERE username = ?");
            $delUser->bind_param("s", $username);
            if ($delUser->execute()) {
                $message = "Admin '$username' deleted successfully.";
            } else {
                $message = "Error deleting admin.";
            }
            $delUser->close();
        }
    } else {
        if ($role !== 'student') {
            $delNotices = $conn->prepare("DELETE FROM notices WHERE posted_by = ?");
            $delNotices->bind_param("s", $username);
            $delNotices->execute();
            $delNotices->close();
        }

        $delUser = $conn->prepare("DELETE FROM users WHERE username = ?");
        $delUser->bind_param("s", $username);
        if ($delUser->execute()) {
            $message = "User '$username' deleted successfully.";
        } else {
            $message = "Error deleting user.";
        }
        $delUser->close();
    }
}

// Search and filter
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$sql = "SELECT username, role FROM users WHERE username != ?";
$params = [$_SESSION['username']];
$types = "s";

if (!empty($search)) {
    $sql .= " AND username LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if (!empty($roleFilter)) {
    $sql .= " AND role = ?";
    $params[] = $roleFilter;
    $types .= "s";
}

$sql .= " ORDER BY role, username";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #60d0fc, #c4faee);
            min-height: 100vh;
        }
        .user-table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
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
        <a class="navbar-brand" href="#">🎓 EduAssist</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="admin.html">Home</a></li>
                
                <li class="nav-item"><a class="nav-link" href="admin_add_users.php">AddUsers</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_delete_user.php">DeleteUsers</a></li>
            </ul>
            <a href="index.html" class="btn btn-outline-success">Logout</a>
        </div>
    </div>
</nav>

<div class="user-table">
    <h2 class="mb-4">👥 All Users</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by username..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="role" class="form-select">
                <option value="">Filter by role</option>
                <option value="student" <?php if($roleFilter=="student") echo "selected"; ?>>Student</option>
                <option value="teacher" <?php if($roleFilter=="teacher") echo "selected"; ?>>Teacher</option>
                <option value="admin" <?php if($roleFilter=="admin") echo "selected"; ?>>Admin</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Apply</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['role'])); ?></td>
                <td>
                    <?php if ($row['role'] === 'admin'): ?>
                        <button class="btn btn-danger btn-sm" onclick="showAdminModal('<?php echo $row['username']; ?>')">Delete</button>
                    <?php else: ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="delete_username" value="<?php echo $row['username']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Admin Password Modal -->
<div class="modal fade" id="adminDeleteModal" tabindex="-1" aria-labelledby="adminDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Admin Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="delete_username" id="modalDeleteUsername">
                <div class="mb-3">
                    <label for="adminPassword" class="form-label">Enter admin's password to confirm:</label>
                    <input type="password" name="admin_password" class="form-control" id="adminPassword" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Delete Admin</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showAdminModal(username) {
        document.getElementById('modalDeleteUsername').value = username;
        const modal = new bootstrap.Modal(document.getElementById('adminDeleteModal'));
        modal.show();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
