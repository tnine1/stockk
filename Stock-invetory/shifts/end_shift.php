<?php
session_start();

include('../config/db.php');
include('../assets/header.php');

date_default_timezone_set('Africa/Kigali');

/* 🔐 LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$shift_id = (int)($_SESSION['shift_id'] ?? 0);

if($shift_id == 0){
  die("
    <div style='
      font-family:Arial;
      background:#0f172a;
      color:white;
      height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
    '>

      <h2>⚠️ No Active Shift Found</h2>

      <a href='../dashboard/barista.php'
      style='
        margin-top:15px;
        padding:12px 20px;
        background:#22c55e;
        color:white;
        text-decoration:none;
        border-radius:8px;
      '>
        ⬅ Back Dashboard
      </a>

    </div>
  ");
}

/* 👨‍🍳 USER INFO */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT
    name,
    department

  FROM users

  WHERE id = $user_id
"));

$name = $user['name'] ?? 'Unknown';
$department = strtolower($user['department'] ?? 'barista');

$end_time = date('Y-m-d H:i:s');

/* 📦 SHIFT TABLE */
if($department == 'kitchen'){

  $shift_table = "kitchen_shift_stock";

  $category_filter = "
    WHERE i.category = 'Kitchen'
  ";

} else {

  $shift_table = "barista_shift_stock";

  $category_filter = "
    WHERE i.category != 'Kitchen'
  ";
}

/* ✅ END SHIFT */
mysqli_query($conn,"
  UPDATE shifts

  SET
    end_time = '$end_time',
    status = 'ended'

  WHERE id = $shift_id
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Shift Ended</title>

<style>

body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  margin:0;
  min-height:100vh;
  display:flex;
  flex-direction:column;
}

.container{
  flex:1;
  padding:20px;
}

.card{
  background:#1e293b;
  border-radius:15px;
  padding:25px;
  max-width:1000px;
  margin:auto;
  box-shadow:0 4px 20px rgba(0,0,0,0.3);
}

.title{
  text-align:center;
  margin-bottom:20px;
}

.title h2{
  margin:0;
  color:#22c55e;
}

.info-box{
  background:#0f172a;
  padding:15px;
  border-radius:10px;
  line-height:1.9;
  margin-bottom:20px;
}

.section-title{
  margin-top:30px;
  margin-bottom:10px;
  color:#f8fafc;
}

table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
  overflow:hidden;
  border-radius:10px;
}

table th{
  background:#111827;
  color:#22c55e;
}

table th,
table td{
  padding:12px;
  border-bottom:1px solid #334155;
  text-align:center;
}

tr:hover{
  background:#273449;
}

.ok{
  color:#22c55e;
  font-weight:bold;
}

.low{
  color:#ef4444;
  font-weight:bold;
}

.actions{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  margin-top:25px;
  justify-content:center;
}

.btn{
  padding:12px 18px;
  border:none;
  border-radius:10px;
  text-decoration:none;
  color:white;
  font-weight:bold;
  cursor:pointer;
  transition:0.2s;
}

.btn:hover{
  transform:translateY(-2px);
  opacity:0.9;
}

.print-btn{
  background:#3b82f6;
}

.dashboard-btn{
  background:#22c55e;
}

.logout-btn{
  background:#ef4444;
}

.footer{
  margin-top:auto;
  background:#111827;
  text-align:center;
  padding:15px;
  color:#94a3b8;
  border-top:2px solid #22c55e;
}

.footer b{
  color:#22c55e;
}

@media print{

  body{
    background:white;
    color:black;
  }

  .actions,
  .footer{
    display:none;
  }

  .card{
    box-shadow:none;
    border:1px solid #ccc;
  }

  table th{
    background:#e2e8f0;
    color:black;
  }
}

</style>

</head>

<body>

<div class="container">

<div class="card">

  <div class="title">

    <h2>✅ Shift Ended Successfully</h2>

    <p>
      Stock Closing Report & Shift Summary
    </p>

  </div>

  <!-- INFO -->
  <div class="info-box">

    👨‍🍳 Staff:
    <b><?php echo htmlspecialchars($name); ?></b>

    <br>

    🏢 Department:
    <b><?php echo ucfirst($department); ?></b>

    <br>

    🆔 Shift ID:
    <b>#<?php echo $shift_id; ?></b>

    <br>

    🕒 End Time:
    <b><?php echo $end_time; ?></b>

  </div>

  <!-- OPENING VS CLOSING -->
  <h3 class="section-title">
    📊 Opening vs Closing Stock
  </h3>

  <table>

    <tr>
      <th>#</th>
      <th>Item</th>
      <th>Opening</th>
      <th>Closing</th>
      <th>Difference</th>
    </tr>

    <?php

    $number = 1;

    $query = mysqli_query($conn,"
      SELECT
        i.name,
        i.unit,
        ss.opening_quantity,
        st.quantity AS closing_quantity

      FROM $shift_table ss

      JOIN stock st
      ON ss.ingredient_id = st.ingredient_id

      JOIN ingredients i
      ON i.id = ss.ingredient_id

      WHERE ss.shift_id = $shift_id

      ORDER BY i.name ASC
    ");

    while($row = mysqli_fetch_assoc($query)){

      $opening = (float)$row['opening_quantity'];
      $closing = (float)$row['closing_quantity'];

      $diff = $closing - $opening;

      $class = ($closing <= 5)
      ? "low"
      : "ok";

      echo "
      <tr class='$class'>

        <td>$number</td>

        <td>{$row['name']}</td>

        <td>$opening {$row['unit']}</td>

        <td>$closing {$row['unit']}</td>

        <td>$diff</td>

      </tr>
      ";

      $number++;
    }

    ?>

  </table>

  <!-- CURRENT STOCK -->
  <h3 class="section-title">
    📦 Current Closing Stock
  </h3>

  <table>

    <tr>
      <th>#</th>
      <th>Item</th>
      <th>Quantity</th>
      <th>Status</th>
    </tr>

    <?php

    $number = 1;

    $stock = mysqli_query($conn,"
      SELECT
        i.name,
        i.unit,
        st.quantity

      FROM stock st

      JOIN ingredients i
      ON i.id = st.ingredient_id

      $category_filter

      ORDER BY i.name ASC
    ");

    while($s = mysqli_fetch_assoc($stock)){

      $qty = (float)$s['quantity'];

      $status = ($qty <= 5)
      ? "<span class='low'>LOW STOCK</span>"
      : "<span class='ok'>AVAILABLE</span>";

      echo "
      <tr>

        <td>$number</td>

        <td>{$s['name']}</td>

        <td>$qty {$s['unit']}</td>

        <td>$status</td>

      </tr>
      ";

      $number++;
    }

    ?>

  </table>

  <!-- ACTIONS -->
  <div class="actions">

    <button
      onclick="window.print()"
      class="btn print-btn"
    >
      🖨 Print Report
    </button>

    <?php if($department == 'kitchen'){ ?>

      <a
        href="../dashboard/kitchen.php"
        class="btn dashboard-btn"
      >
        🍳 Kitchen Dashboard
      </a>

    <?php } else { ?>

      <a
        href="../dashboard/barista.php"
        class="btn dashboard-btn"
      >
        ☕ Barista Dashboard
      </a>

    <?php } ?>

    <a
      href="../auth/logout.php"
      class="btn logout-btn"
    >
      🚪 Logout
    </a>

  </div>

</div>

</div>

<!-- FOOTER -->
<div class="footer">

  ☕ <b>Le Chic Café Management System</b>

  <br>

  Powered by Tnine & Ciero Tec

</div>

</body>
</html>

<?php

/* 🧹 CLEAR SHIFT SESSION */
unset($_SESSION['shift_id']);

?>