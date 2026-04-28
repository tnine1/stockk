<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
  die("⛔ Access Denied");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reports</title>

  <style>
    body{
      font-family:Arial;
      background:#0f172a;
      color:white;
      padding:15px;
    }

    .card{
      background:#1e293b;
      padding:15px;
      border-radius:10px;
      margin-bottom:15px;
    }

    table{
      width:100%;
      border-collapse:collapse;
    }

    th,td{
      padding:10px;
      border-bottom:1px solid #334155;
      text-align:left;
    }

    input,button{
      padding:10px;
      border:none;
      border-radius:5px;
      margin-top:5px;
    }

    button{
      background:#22c55e;
      color:white;
      width:100%;
    }
  </style>
</head>

<body>

<h2>📊 Reports Dashboard</h2>

<!-- 📅 FILTER -->
<div class="card">
  <form method="GET">
    From: <input type="date" name="from">
    To: <input type="date" name="to">
    <button>Filter</button>
  </form>
</div>

<?php
$from = $_GET['from'] ?? date('Y-m-d');
$to = $_GET['to'] ?? date('Y-m-d');
?>

<!-- 🧾 DAILY ORDERS -->
<div class="card">
  <h3>🧾 Orders Report</h3>

  <?php
  $orders = mysqli_query($conn,"
    SELECT o.drink_name, u.name, o.created_at
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE DATE(o.created_at) BETWEEN '$from' AND '$to'
    ORDER BY o.id DESC
  ");

  while($o = mysqli_fetch_assoc($orders)){
    echo $o['drink_name']." - ".$o['name']." - ".$o['created_at']."<br>";
  }
  ?>
</div>

<!-- 👨‍🍳 BARISTA PERFORMANCE -->
<div class="card">
  <h3>👨‍🍳 Barista Performance</h3>

  <?php
  $perf = mysqli_query($conn,"
    SELECT u.name, COUNT(o.id) as total_orders
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE u.role='barista'
    AND DATE(o.created_at) BETWEEN '$from' AND '$to'
    GROUP BY u.id
  ");

  echo "<table>
    <tr><th>Barista</th><th>Orders</th></tr>";

  while($p = mysqli_fetch_assoc($perf)){
    echo "<tr>
      <td>{$p['name']}</td>
      <td>{$p['total_orders']}</td>
    </tr>";
  }

  echo "</table>";
  ?>
</div>

<!-- 📦 INGREDIENT USAGE -->
<div class="card">
  <h3>📦 Ingredient Usage</h3>

  <?php
  $usage = mysqli_query($conn,"
    SELECT i.name, SUM(oi.quantity) as total_used
    FROM order_items oi
    JOIN ingredients i ON i.id = oi.ingredient_id
    JOIN orders o ON o.id = oi.order_id
    WHERE DATE(o.created_at) BETWEEN '$from' AND '$to'
    GROUP BY i.id
  ");

  while($u = mysqli_fetch_assoc($usage)){
    echo $u['name']." : ".$u['total_used']."<br>";
  }
  ?>
</div>

</body>
</html>