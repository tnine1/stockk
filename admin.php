<?php
session_start();
include('../config/db.php');


// 🔐 Security: only admin allowed
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){

  echo "
  <div style='
    font-family: Arial;
    background:#0f172a;
    color:white;
    text-align:center;
    padding:60px;
    height:100vh;
  '>
    <h1>⛔ Access Denied</h1>
    <p>You do not have permission to view this page.</p>

    <a href='../auth/login.php' style='
      display:inline-block;
      margin-top:20px;
      padding:12px 25px;
      background:#22c55e;
      color:white;
      text-decoration:none;
      border-radius:6px;
      font-weight:bold;
    '>
      🔐 Go to Login
    </a>
  </div>
  ";

  exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>

  <style>
    body {
      font-family: Arial;
      background: #0f172a;
      color: white;
      margin: 0;
      padding: 15px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .menu {
      background: #1e293b;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .menu a {
      display: inline-block;
      margin-right: 15px;
      color: #22c55e;
      text-decoration: none;
      font-weight: bold;
    }

    .card {
      background: #1e293b;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(2,1fr);
      gap: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #334155;
      text-align: left;
    }

    .warning {
      color: #facc15;
      font-weight: bold;
    }

    .danger {
      color: #ef4444;
      font-weight: bold;
    }
  </style>
</head>

<body>

<h2>👨‍💼 Admin Dashboard</h2>

<!-- 🔗 MENU -->
<div class="menu">
  <a href="../auth/register.php">➕ Register User</a>
  <a href="add_item.php">➕ Add Ingredient</a>
  <a href="stock.php">📦 Manage Stock</a>
  <a href="../auth/logout.php">🚪 Logout</a>
</div>
<a href="reports.php">📊 Reports</a>
<!-- 📊 STATS -->
<div class="card grid">
  <?php
  $users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM users"));
  $orders = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM orders"));
  $shifts = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM shifts"));

  echo "<div>👤 Users<br><strong>{$users['total']}</strong></div>";
  echo "<div>🧾 Orders<br><strong>{$orders['total']}</strong></div>";
  echo "<div>⏰ Shifts<br><strong>{$shifts['total']}</strong></div>";
  ?>
</div>

<!-- 👥 USERS -->
<div class="card">
  <h3>👥 All Users</h3>

  <table>
    <tr>
      <th>Name</th>
      <th>Username</th>
      <th>Role</th>
    </tr>

    <?php
    $q = mysqli_query($conn,"SELECT * FROM users");

    while($u = mysqli_fetch_assoc($q)){
      echo "<tr>
        <td>{$u['name']}</td>
        <td>{$u['username']}</td>
        <td>{$u['role']}</td>
      </tr>";
    }
    ?>
  </table>
</div>

<!-- 📦 STOCK -->
<div class="card">
  <h3>📦 Current Stock</h3>

  <?php
  $stock = mysqli_query($conn,"
    SELECT i.name, s.quantity
    FROM stock s
    JOIN ingredients i ON i.id = s.ingredient_id
  ");

  while($s = mysqli_fetch_assoc($stock)){
    echo "{$s['name']} : {$s['quantity']}<br>";
  }
  ?>
</div>

<!-- ⚠️ LOW STOCK -->
<div class="card">
  <h3>⚠️ Low Stock Alerts</h3>

  <?php
  $low = mysqli_query($conn,"
    SELECT i.name, s.quantity
    FROM stock s
    JOIN ingredients i ON i.id = s.ingredient_id
    WHERE s.quantity < 5
  ");

  if(mysqli_num_rows($low) == 0){
    echo "✅ All stock levels are good";
  }

  while($l = mysqli_fetch_assoc($low)){
    echo "<div class='danger'>⚠ {$l['name']} (Remaining: {$l['quantity']})</div>";
  }
  ?>
</div>

<!-- 📊 INGREDIENT USAGE -->
<div class="card">
  <h3>📊 Ingredient Usage</h3>

  <?php
  $usage = mysqli_query($conn,"
    SELECT i.name, SUM(oi.quantity) as total_used
    FROM order_items oi
    JOIN ingredients i ON i.id = oi.ingredient_id
    GROUP BY i.name
  ");

  while($u = mysqli_fetch_assoc($usage)){
    echo "{$u['name']} used: {$u['total_used']}<br>";
  }
  ?>
</div>

</body>
</html>