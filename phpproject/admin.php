<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$currentUser = $_SESSION['username'];
$stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.html");
    exit();
}
$userData = $result->fetch_assoc();
if ($userData['role'] !== 'admin') {
    echo "Access denied. You are not an admin.";
    exit();
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['role'])) {
    $usernameToUpdate = $_POST['username'];
    $newRole = $_POST['role'] === 'admin' ? 'admin' : 'user';

    if ($usernameToUpdate === $currentUser && $newRole !== 'admin') {
        $message = "<p class='error'>You cannot remove your own admin role.</p>";
    } else {
        $updateStmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
        $updateStmt->bind_param("ss", $newRole, $usernameToUpdate);
        if ($updateStmt->execute()) {
            $message = "<p class='success'>Role updated for user <strong>" . htmlspecialchars($usernameToUpdate) . "</strong>.</p>";
        } else {
            $message = "<p class='error'>Failed to update role.</p>";
        }
        $updateStmt->close();
    }
}

$usersResult = $conn->query("SELECT username, role FROM users ORDER BY username ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Management</title>
<style>
  * { box-sizing: border-box; }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 0;
    color: #2c3e50;
    display: flex;
  }
  .sidebar {
    height: 100vh;
    width: 250px;
    position: fixed;
    left: -250px;
    top: 0;
    background: #3498db;
    transition: left 0.3s ease;
    padding-top: 60px;
    z-index: 1100;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  .sidebar.active { left: 0; }
  .sidebar a {
    padding: 15px 30px;
    text-decoration: none;
    font-size: 18px;
    color: #fff;
    display: block;
    transition: 0.3s;
  }
  .sidebar a:hover {
    background: #2980b9;
  }
  .sidebar a.active {
    background: #1f618d;
    font-weight: 700;
  }
  .sidebar .close-btn {
    position: absolute;
    top: 12px;
    right: 18px;
    font-size: 30px;
    color: white;
    cursor: pointer;
    user-select: none;
    z-index: 1201;
  }
  .main-content {
    margin: 80px auto 40px auto;
    max-width: 900px;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    flex-grow: 1;
  }
  .header {
    background: #3498db;
    color: #fff;
    padding: 18px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
    position: fixed;
    top: 0;
    width: 100%;
    left: 0;
    z-index: 1200;
  }
  .header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 28px;
    letter-spacing: 1px;
  }
  .logout-btn {
    background: #fff;
    color: #3498db;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(52, 152, 219, 0.4);
    transition: background-color 0.25s ease;
  }
  .logout-btn:hover {
    background: #e1e7ee;
  }
  .burger {
    font-size: 28px;
    cursor: pointer;
    color: #fff;
    margin-right: 20px;
    user-select: none;
    position: relative;
    z-index: 1300;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 60px;
  }
  th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
  }
  th {
    background-color: #3498db;
    color: white;
    font-size: 16px;
  }
  tr:hover {
    background-color: #f1f7fd;
  }
  select, button {
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  select {
    background: #fff;
  }
  select:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px #3498db;
  }
  button {
    background: #3498db;
    color: #fff;
    border: none;
    font-weight: 600;
  }
  button:hover {
    background: #2980b9;
  }
  form {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
  }
  .success {
    background: #d4edda;
    color: #155724;
    border-left: 6px solid #28a745;
    padding: 10px 15px;
    margin: 15px 0;
    border-radius: 6px;
  }
  .error {
    background: #f8d7da;
    color: #721c24;
    border-left: 6px solid #dc3545;
    padding: 10px 15px;
    margin: 15px 0;
    border-radius: 6px;
  }
 .sidebar a:first-of-type {
  margin-top: 15px;
}

</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <span class="close-btn" id="closeBtn">&times;</span>
  <a href="index.php">Dashboard</a>
  <a href="admin.php" class="active">Users</a>
  <a href="logs.php">Logs</a>
  <a href="admin_tasks.php">All Tasks</a>
</div>

<div class="main-content" id="mainContent">
  <div class="header">
    <span class="burger" id="burger">&#9776;</span>
    <h2>Admin Management</h2>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>

  <?php if (!empty($message)) echo $message; ?>

  <table>
    <thead>
      <tr>
        <th>Username</th>
        <th>Current Role</th>
        <th>Change Role</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($user = $usersResult->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($user['username']); ?></td>
        <td><?php echo htmlspecialchars($user['role']); ?></td>
        <td>
          <form method="POST">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" />
            <select name="role" required>
              <option value="user" <?php if ($user['role'] === 'user') echo 'selected'; ?>>User</option>
              <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit">Update</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
  const burger = document.getElementById('burger');
  const sidebar = document.getElementById('sidebar');
  const closeBtn = document.getElementById('closeBtn');

  burger.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('active');
  });
</script>

</body>
</html>

<?php $conn->close(); ?>
