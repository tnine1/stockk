<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 

date_default_timezone_set('Africa/Kigali');

/* 🔐 ADMIN SECURITY */
if(
  !isset($_SESSION['user_id']) ||
  $_SESSION['role'] != 'admin'
){

  echo "
  <div style='
    font-family:Arial;
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
      border-radius:8px;
      font-weight:bold;
    '>
      🔐 Go to Login
    </a>

  </div>
  ";

  exit;
}

/* 👤 ADMIN INFO */
$user_id = (int) $_SESSION['user_id'];

$admin = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT * FROM users
  WHERE id = $user_id
"));

/* ❌ DELETE USER */
if(isset($_POST['delete_id'])){

  $delete_id = (int) $_POST['delete_id'];

  if($delete_id == $user_id){
    die("⛔ You cannot delete yourself");
  }

  $check = mysqli_query($conn,"
    SELECT role FROM users
    WHERE id = $delete_id
  ");

  $user = mysqli_fetch_assoc($check);

  if($user && $user['role'] == 'admin'){

    $admins = mysqli_query($conn,"
      SELECT COUNT(*) as total
      FROM users
      WHERE role='admin'
    ");

    $count = mysqli_fetch_assoc($admins)['total'];

    if($count <= 1){
      die("⛔ Cannot delete last admin");
    }
  }

  mysqli_query($conn,"
    DELETE FROM users
    WHERE id = $delete_id
  ");

  header("Location: admin.php");
  exit;
}

/* 📊 REALTIME STATS */
$users = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total FROM users
"));

$orders = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total FROM orders
"));

$shifts = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total FROM shifts
"));

$active_shifts = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total
  FROM shifts
  WHERE end_time IS NULL
"));

$ingredients = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total FROM ingredients
"));

$damage = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total FROM damaged_stock
"));

$today_orders = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total
  FROM orders
  WHERE DATE(created_at)=CURDATE()
"));

$low_stock = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT COUNT(*) total
  FROM stock s
  JOIN ingredients i
  ON i.id = s.ingredient_id
  WHERE s.quantity <= i.low_stock_limit
"));

?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  font-family:Arial,sans-serif;
  background:#0f172a;
  color:white;
  padding:15px;
}

/* HEADER */

.header{
  background:#1e293b;
  padding:20px;
  border-radius:15px;
  margin-bottom:20px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:15px;
}

.header-left h2{
  margin-bottom:5px;
}

.header-left p{
  color:#cbd5e1;
}

.clock{
  font-size:18px;
  color:#22c55e;
  font-weight:bold;
}

/* NAVIGATION */

.menu{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-bottom:20px;
}

.menu a{
  background:#1e293b;
  color:white;
  text-decoration:none;
  padding:12px 18px;
  border-radius:10px;
  font-weight:bold;
  transition:0.3s;
}

.menu a:hover{
  background:#22c55e;
}

/* STATS */

.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
  gap:15px;
  margin-bottom:20px;
}

.card-stat{
  background:#1e293b;
  padding:20px;
  border-radius:15px;
  box-shadow:0 5px 15px rgba(0,0,0,0.3);
}

.card-stat h1{
  font-size:32px;
  margin-bottom:10px;
  color:#22c55e;
}

.card-stat p{
  color:#cbd5e1;
  font-size:15px;
}

/* SECTION BUTTONS */

.section-buttons{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-bottom:20px;
}

.section-buttons button{
  border:none;
  background:#334155;
  color:white;
  padding:12px 16px;
  border-radius:10px;
  cursor:pointer;
  font-weight:bold;
  transition:0.3s;
}

.section-buttons button:hover{
  background:#22c55e;
}

/* CONTENT */

.section{
  display:none;
  background:#1e293b;
  padding:20px;
  border-radius:15px;
  margin-bottom:20px;
}

.section h3{
  margin-bottom:15px;
}

/* TABLE */

.table-wrapper{
  overflow-x:auto;
}

table{
  width:100%;
  border-collapse:collapse;
}

th{
  background:#0f172a;
  padding:12px;
  text-align:left;
}

td{
  padding:12px;
  border-bottom:1px solid #334155;
}

tr:hover{
  background:#273449;
}

/* BADGES */

.badge{
  padding:5px 10px;
  border-radius:20px;
  font-size:12px;
  font-weight:bold;
}

.admin{
  background:#22c55e;
}

.barista{
  background:#3b82f6;
}

.kitchen{
  background:#f59e0b;
}

/* STATUS COLORS */

.low{
  color:#ef4444;
  font-weight:bold;
}

.good{
  color:#22c55e;
  font-weight:bold;
}

.warning{
  color:#facc15;
  font-weight:bold;
}

/* BUTTONS */

.delete-btn{
  border:none;
  background:#ef4444;
  color:white;
  padding:8px 12px;
  border-radius:8px;
  cursor:pointer;
}

.delete-btn:hover{
  background:#dc2626;
}

.logout{
  background:#ef4444 !important;
}

.logout:hover{
  background:#dc2626 !important;
}

/* LIVE BOX */

.live-box{
  background:#0f172a;
  padding:15px;
  border-radius:10px;
  margin-bottom:10px;
}

/* MOBILE */

@media(max-width:768px){

  .header{
    flex-direction:column;
    align-items:flex-start;
  }

  .menu{
    flex-direction:column;
  }

  .menu a{
    width:100%;
    text-align:center;
  }

  .section-buttons{
    flex-direction:column;
  }

  .section-buttons button{
    width:100%;
  }
}

</style>

</head>

<body>

<!-- HEADER -->
<div class="header">

  <div class="header-left">

    <h2>👨‍💼 Le Chic Café Admin Dashboard</h2>

    <p>
      Welcome back,
      <b><?php echo htmlspecialchars($admin['name']); ?></b>
    </p>

  </div>

  <div class="clock" id="clock"></div>

</div>

<!-- MENU -->
<div class="menu">

  <a href="../auth/register.php">
    ➕ Register User
  </a>

  <a href="add_item.php">
    ➕ Add Ingredient
  </a>

  <a href="stock.php">
    📦 Manage Stock
  </a>

  <a href="reports.php">
    📊 Reports
  </a>

  <a href="../auth/logout.php" class="logout">
    🚪 Logout
  </a>

</div>

<!-- STATS -->
<div class="grid">

  <div class="card-stat">
    <h1><?php echo $users['total']; ?></h1>
    <p>👥 Total Users</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $orders['total']; ?></h1>
    <p>🧾 Total Orders</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $today_orders['total']; ?></h1>
    <p>📅 Today's Orders</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $active_shifts['total']; ?></h1>
    <p>🟢 Active Shifts</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $ingredients['total']; ?></h1>
    <p>📦 Ingredients</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $low_stock['total']; ?></h1>
    <p>⚠️ Low Stock Items</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $damage['total']; ?></h1>
    <p>❌ Damage Reports</p>
  </div>

  <div class="card-stat">
    <h1><?php echo $shifts['total']; ?></h1>
    <p>🕒 Total Shifts</p>
  </div>

</div>

<!-- SECTION BUTTONS -->
<div class="section-buttons">

  <button onclick="showSection('live')">
    📡 Live Monitor
  </button>

  <button onclick="showSection('users')">
    👥 Users
  </button>

  <button onclick="showSection('stock')">
    📦 Stock
  </button>

  <button onclick="showSection('low')">
    ⚠️ Low Stock
  </button>

  <button onclick="showSection('usage')">
    📊 Usage
  </button>

  <button onclick="showSection('damage')">
    ❌ Damages
  </button>

</div>

<!-- LIVE MONITOR -->
<div id="live" class="section">

  <h3>📡 Realtime Activity</h3>

  <?php

  $live = mysqli_query($conn,"
    SELECT
      s.id,
      u.name,
      u.department,
      s.shift_type,
      s.start_time

    FROM shifts s

    JOIN users u
    ON u.id = s.user_id

    WHERE s.end_time IS NULL

    ORDER BY s.id DESC
  ");

  if(mysqli_num_rows($live) == 0){

    echo "
      <div class='warning'>
        ⚠️ No active staff right now
      </div>
    ";
  }

  while($l = mysqli_fetch_assoc($live)){

    echo "
    <div class='live-box'>

      👨‍🍳 <b>{$l['name']}</b>

      <br>

      🏢 {$l['department']}

      <br>

      🕒 {$l['shift_type']}

      <br>

      ⏰ Started:
      {$l['start_time']}

    </div>
    ";
  }

  ?>

</div>

<!-- USERS -->
<div id="users" class="section">

  <h3>👥 Staff Management</h3>

  <div class="table-wrapper">

  <table>

    <tr>
      <th>Name</th>
      <th>Username</th>
      <th>Role</th>
      <th>Department</th>
      <th>Action</th>
    </tr>

    <?php

    $q = mysqli_query($conn,"
      SELECT *
      FROM users
      ORDER BY id DESC
    ");

    while($u = mysqli_fetch_assoc($q)){

      $role_class = $u['role'];

      echo "
      <tr>

        <td>{$u['name']}</td>

        <td>{$u['username']}</td>

        <td>
          <span class='badge $role_class'>
            {$u['role']}
          </span>
        </td>

        <td>{$u['department']}</td>

        <td>
      ";

      if($u['id'] != $user_id){

        echo "
        <form method='POST'>

          <input
            type='hidden'
            name='delete_id'
            value='{$u['id']}'
          >

          <button
            class='delete-btn'
            onclick=\"return confirm('Delete this user?')\"
          >
            🗑 Delete
          </button>

        </form>
        ";

      } else {

        echo "🔒 Current Admin";
      }

      echo "
        </td>

      </tr>
      ";
    }

    ?>

  </table>

  </div>

</div>

<!-- STOCK -->
<div id="stock" class="section">

  <h3>📦 Current Stock</h3>

  <?php

  $stock = mysqli_query($conn,"
    SELECT
      i.name,
      i.category,
      i.unit,
      s.quantity

    FROM stock s

    JOIN ingredients i
    ON i.id = s.ingredient_id

    ORDER BY i.category ASC, i.name ASC
  ");

  while($s = mysqli_fetch_assoc($stock)){

    $class = ($s['quantity'] <= 5)
      ? "low"
      : "good";

    echo "
    <div class='$class' style='margin-bottom:10px;'>

      📦 <b>{$s['name']}</b>

      ({$s['category']})

      :
      {$s['quantity']} {$s['unit']}

    </div>
    ";
  }

  ?>

</div>

<!-- LOW STOCK -->
<div id="low" class="section">

  <h3>⚠️ Low Stock Alerts</h3>

  <?php

  $low = mysqli_query($conn,"
    SELECT
      i.name,
      i.unit,
      s.quantity,
      i.low_stock_limit

    FROM stock s

    JOIN ingredients i
    ON i.id = s.ingredient_id

    WHERE s.quantity <= i.low_stock_limit

    ORDER BY s.quantity ASC
  ");

  if(mysqli_num_rows($low) == 0){

    echo "
    <div class='good'>
      ✅ All stock levels are healthy
    </div>
    ";
  }

  while($l = mysqli_fetch_assoc($low)){

    echo "
    <div class='low' style='margin-bottom:10px;'>

      ⚠️ {$l['name']}

      :
      {$l['quantity']} {$l['unit']}

      (Limit: {$l['low_stock_limit']})

    </div>
    ";
  }

  ?>

</div>

<!-- USAGE -->
<div id="usage" class="section">

  <h3>📊 Ingredient Usage</h3>

  <?php

  $usage = mysqli_query($conn,"
    SELECT
      i.name,
      SUM(oi.quantity) AS total_used

    FROM order_items oi

    JOIN ingredients i
    ON i.id = oi.ingredient_id

    GROUP BY oi.ingredient_id

    ORDER BY total_used DESC
  ");

  while($u = mysqli_fetch_assoc($usage)){

    echo "
    <div style='margin-bottom:10px;'>

      📦 {$u['name']}

      :
      <b>{$u['total_used']}</b>

    </div>
    ";
  }

  ?>

</div>

<!-- DAMAGE -->
<div id="damage" class="section">

  <h3>❌ Damaged Stock Reports</h3>

  <?php

  $dmg = mysqli_query($conn,"
    SELECT
      i.name,
      d.quantity,
      d.reason,
      d.created_at

    FROM damaged_stock d

    JOIN ingredients i
    ON i.id = d.ingredient_id

    ORDER BY d.id DESC
  ");

  if(mysqli_num_rows($dmg) == 0){

    echo "
    <div class='good'>
      ✅ No damage reports found
    </div>
    ";
  }

  while($d = mysqli_fetch_assoc($dmg)){

    echo "
    <div class='warning' style='margin-bottom:15px;'>

      📦 <b>{$d['name']}</b>

      :
      {$d['quantity']}

      <br>

      📝 {$d['reason']}

      <br>

      🕒 {$d['created_at']}

      <hr style='border-color:#334155;'>

    </div>
    ";
  }

  ?>

</div>

<script>

/* 📌 SHOW SECTION */
function showSection(id){

  const sections =
    document.querySelectorAll('.section');

  sections.forEach(section => {
    section.style.display = 'none';
  });

  const target =
    document.getElementById(id);

  if(target){
    target.style.display = 'block';
  }
}

/* 🚀 DEFAULT OPEN */
window.onload = function(){

  showSection('live');

  updateClock();

  setInterval(updateClock,1000);

  // 🔄 realtime refresh every 30 sec
  setInterval(function(){
    location.reload();
  },30000);
};

/* ⏰ CLOCK */
function updateClock(){

  const now = new Date();

  document.getElementById('clock')
  .innerHTML =
    now.toLocaleString('en-GB', {
      timeZone:'Africa/Kigali'
    });
}

</script>

</body>
</html>include('../assets/header.php'); 
