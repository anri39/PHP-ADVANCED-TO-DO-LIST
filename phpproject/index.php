<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include 'database.php';

$username = $_SESSION['username'];
$role = '';
$user_id = 0;

$userQuery = $conn->prepare("SELECT id, role FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
if ($userResult && $userResult->num_rows === 1) {
    $userRow = $userResult->fetch_assoc();
    $role = $userRow['role'];
    $user_id = $userRow['id'];
    $_SESSION['role'] = $role;
    $_SESSION['user_id'] = $user_id;
} else {
    session_destroy();
    header("Location: login.html");
    exit();
}

$statusCounts = ['completed' => 0, 'pending' => 0];
$categoryCounts = [];

$stmt = $conn->prepare("SELECT Status, category FROM tasks WHERE user_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = strtolower($row['Status']);
        if (isset($statusCounts[$status])) {
            $statusCounts[$status]++;
        }
        $category = $row['category'];
        if (!isset($categoryCounts[$category])) {
            $categoryCounts[$category] = 0;
        }
        $categoryCounts[$category]++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>To-Do List Dashboard</title>
  <style>
    * {
      font-family: Arial, sans-serif;
    }

    body {
      margin: 0;
      background-color: #f4f6f8;
    }

    .header {
      background-color: #3498db;
      color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header .logout-btn {
      padding: 8px 14px;
      background-color: white;
      color: #3498db;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      width: 100px;
      height: 30px;
    }

    .header .logout-btn:hover {
      background-color: #f0f0f0;
    }

    .headerforwelcome {
      font-size: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
    }

    .welcometext {
      font-size: 20px;
    }

    .container {
      text-align: center;
      padding: 50px 20px;
    }

    .container h1 {
      font-size: 36px;
      margin-bottom: 20px;
    }

    .container button {
      padding: 12px 24px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      width: 150px;
      height: 40px;
      margin: 10px 5px;
    }

    .container button:hover {
      background-color: #2980b9;
    }

    .spana {
      background: #3498db;
      height: 2px;
      display: block;
      margin: 0 auto -20px auto;
      width: 1330px;
      max-width: 200rem;
    }

    .dashboard {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 30px;
      margin-top: 50px;
      flex-wrap: wrap;
    }

    .box {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 350px;
      height: 400px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
    }

    .task-box {
      background-color: #f9f9f9;
      padding: 50px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 650px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      background-color: transparent;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px 10px;
      text-align: center;
    }

    th {
      background-color: #3498db;
      color: white;
    }

    a {
      text-decoration: none;
      font-weight: bold;
    }

    .edit-link {
      color: #3498db;
    }

    .delete-link {
      color: red;
    }

    .chart-canvas {
      width: 100%;
      max-width: 300px;
      height: auto;
    }
  </style>
</head>
<body>
  <div class="header">
    <div><strong class="welcometext">To-Do-List</strong></div>
    <div class="headerforwelcome">
      Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
      <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </div>

  <div class="container">
    <h1>To-Do-List</h1>
    <button onclick="window.location.href='createtask.html'">Make a task</button>

    <?php if ($role === 'admin'): ?>
      <button style="background-color: #2c3e50;" onclick="window.location.href='admin.php'">View Logs</button>
    <?php endif; ?>

    <h2 style="margin-top: 50px;">Created Tasks:</h2>
    <span class="spana"></span><br>

    <div class="dashboard">
      <div class="box">
        <h3>Status Breakdown</h3>
        <canvas id="statusChart" class="chart-canvas" width="300" height="300"></canvas>
      </div>

      <div class="task-box">
        <table>
          <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
          <?php
          include 'database.php';
          $user_id = $_SESSION['user_id'];
          $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_ID = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>" . htmlspecialchars($row['Title']) . "</td>
                          <td>" . htmlspecialchars($row['Status']) . "</td>
                          <td>" . htmlspecialchars($row['Priority']) . "</td>
                          <td>" . htmlspecialchars($row['category']) . "</td>
                          <td>
                            <a class='edit-link' href='edit.php?id=" . $row['ID'] . "'>Edit</a> |
                            <a class='delete-link' href='delete.php?id=" . $row['ID'] . "'>Delete</a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No tasks found.</td></tr>";
          }
          $conn->close();
          ?>
        </table>
      </div>

      <div class="box">
        <h3>Tasks by Category</h3>
        <canvas id="categoryChart" class="chart-canvas" width="300" height="300"></canvas>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const statusData = {
      completed: <?= $statusCounts['completed']; ?>,
      pending: <?= $statusCounts['pending']; ?>
    };

    const categoryData = <?= json_encode($categoryCounts); ?>;

    new Chart(document.getElementById("statusChart"), {
      type: "pie",
      data: {
        labels: ["Completed", "Pending"],
        datasets: [{
          data: [statusData.completed, statusData.pending],
          backgroundColor: ["#2ecc71", "#e74c3c"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: "bottom" }
        }
      }
    });

    new Chart(document.getElementById("categoryChart"), {
      type: "bar",
      data: {
        labels: Object.keys(categoryData),
        datasets: [{
          label: "Tasks",
          data: Object.values(categoryData),
          backgroundColor: "#3498db"
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            precision: 0
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  </script>
</body>
</html>
