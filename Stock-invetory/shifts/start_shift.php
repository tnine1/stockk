<?php<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 

date_default_timezone_set('Africa/Kigali');

/* 🔐 LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = (int) $_SESSION['user_id'];

/* 👨‍🍳 USER INFO */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT name, department
  FROM users
  WHERE id = $user_id
"));

$name = $user['name'] ?? 'Unknown';
$department = strtolower($user['department'] ?? 'barista');

/* 🔍 CHECK ACTIVE SHIFT */
$check = mysqli_query($conn,"
  SELECT id
  FROM shifts
  WHERE user_id = $user_id
  AND end_time IS NULL
");

if(mysqli_num_rows($check) > 0){

  $shift = mysqli_fetch_assoc($check);

  $_SESSION['shift_id'] = $shift['id'];

  if($department == 'kitchen'){
    header("Location: ../dashboard/kitchen.php");
  } else {
    header("Location: ../dashboard/barista.php");
  }

  exit;
}

/* 🕒 SHIFT TIME */
$start_time = date('Y-m-d H:i:s');
$hour = date('H');

if($hour >= 7 && $hour < 15){

  $shift_type = "Morning";

}
elseif($hour >= 15 && $hour < 23){

  $shift_type = "Evening";

}
else{

  $shift_type = "Night";
}

/* 📦 SELECT SHIFT TABLE */
if($department == 'kitchen'){

  $shift_table = "kitchen_shift_stock";

} else {

  $shift_table = "barista_shift_stock";
}

/* 🆕 CREATE SHIFT */
mysqli_query($conn,"
  INSERT INTO shifts
  (
    user_id,
    start_time,
    shift_type
  )
  VALUES
  (
    $user_id,
    '$start_time',
    '$shift_type'
  )
");

$shift_id = mysqli_insert_id($conn);

$_SESSION['shift_id'] = $shift_id;

/* 📦 SAVE OPENING STOCK SNAPSHOT */
$stock = mysqli_query($conn,"
  SELECT
    s.ingredient_id,
    s.quantity

  FROM stock s

  INNER JOIN ingredients i
  ON i.id = s.ingredient_id
");

/* ✅ INSERT SNAPSHOT */
while($s = mysqli_fetch_assoc($stock)){

  $ingredient_id = (int)$s['ingredient_id'];
  $qty = (float)$s['quantity'];

  mysqli_query($conn,"
    INSERT INTO $shift_table
    (
      shift_id,
      ingredient_id,
      opening_quantity
    )
    VALUES
    (
      $shift_id,
      $ingredient_id,
      $qty
    )
  ");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Shift Started</title>

<style>

body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  padding:20px;
  margin:0;
}

.header{
  background:#111827;
  padding:15px 20px;
  border-radius:10px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:20px;
}

.logo img{
  height:60px;
}

.system-name{
  font-size:22px;
  font-weight:bold;
  color:#22c55e;
}

.card{
  background:#1e293b;
  padding:20px;
  border-radius:12px;
  max-width:900px;
  margin:auto;
}

.title{
  text-align:center;
  margin-bottom:20px;
}

.info{
  background:#0f172a;
  padding:15px;
  border-radius:10px;
  line-height:1.8;
  margin-bottom:20px;
}

.ok{
  color:#22c55e;
  font-weight:bold;
}

.low{
  color:#ef4444;
  font-weight:bold;
}

table{
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
}

table th,
table td{
  border-bottom:1px solid #334155;
  padding:12px;
  text-align:left;
}

table th{
  background:#0f172a;
}

.actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  margin-top:20px;
}

a,
button{
  padding:12px 18px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  text-decoration:none;
  color:white;
  font-weight:bold;
}

.dashboard-btn{
  background:#22c55e;
}

.print-btn{
  background:#3b82f6;
}

.screenshot-btn{
  background:#f59e0b;
}

button:hover,
a:hover{
  opacity:0.9;
}

.footer{
  margin-top:20px;
  text-align:center;
  color:#94a3b8;
  font-size:14px;
}

@media print{

  .actions{
    display:none;
  }

  body{
    background:white;
    color:black;
  }

  .card{
    box-shadow:none;
    border:1px solid #ccc;
  }

  table th{
    background:#f1f5f9;
    color:black;
  }
}

</style>

</head>

<body>

<!-- HEADER -->
<div class="header">

  <div class="logo">
    <img src="../assets/images/logo.png" alt="Le Chic Café Logo">
  </div>

  <div class="system-name">
    ☕ Le Chic Café Management System
  </div>

</div>

<div class="card">

  <div class="title">
    <h2>✅ Shift Started Successfully</h2>
  </div>

  <!-- SHIFT INFO -->
  <div class="info">

    👨‍🍳 Staff:
    <b><?php echo htmlspecialchars($name); ?></b>

    <br>

    🏢 Department:
    <b><?php echo ucfirst($department); ?></b>

    <br>

    🕒 Start Time:
    <b><?php echo $start_time; ?></b>

    <br>

    📌 Shift Type:
    <b><?php echo $shift_type; ?></b>

    <br>

    🆔 Shift ID:
    <b>#<?php echo $shift_id; ?></b>

  </div>

  <h3>📦 Opening Stock Snapshot</h3>

  <table>

    <tr>
      <th>#</th>
      <th>Item</th>
      <th>Quantity</th>
      <th>Status</th>
    </tr>

    <?php

    $number = 1;

    $open = mysqli_query($conn,"
      SELECT
        i.name,
        i.unit,
        s.quantity

      FROM stock s

      INNER JOIN ingredients i
      ON i.id = s.ingredient_id

      ORDER BY i.name ASC
    ");

    while($o = mysqli_fetch_assoc($open)){

      $item = htmlspecialchars($o['name']);
      $qty = (float)$o['quantity'];
      $unit = htmlspecialchars($o['unit']);

      $status =
      ($qty <= 5)
      ? "<span class='low'>LOW STOCK</span>"
      : "<span class='ok'>AVAILABLE</span>";

      echo "
      <tr>

        <td>$number</td>

        <td>$item</td>

        <td>$qty $unit</td>

        <td>$status</td>

      </tr>
      ";

      $number++;
    }

    ?>

  </table>
   
  <!-- ACTION BUTTONS -->
  <div class="actions">

    <button
      class="print-btn"
      onclick="window.print()"
    >
      🖨 Print Opening Stock
    </button>

    <button
      class="screenshot-btn"
      onclick="takeScreenshot()"
    >
      📸 Save Screenshot
    </button>

    <?php if($department == 'kitchen'){ ?>

      <a
        href="../dashboard/kitchen.php"
        class="dashboard-btn"
      >
        ⬅ Kitchen Dashboard
      </a>

    <?php } else { ?>

      <a
        href="../dashboard/barista.php"
        class="dashboard-btn"
      >
        ⬅ Barista Dashboard
      </a>

    <?php } ?>

  </div>

  <div class="footer">
    Generated by Le Chic Café Inventory & POS System
  </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>

function takeScreenshot(){

  html2canvas(document.body,{
    scale:2,
    useCORS:true
  }).then(canvas => {

    let link = document.createElement('a');

    let date =
    new Date()
    .toISOString()
    .slice(0,19)
    .replace(/:/g,'-');

    link.download =
    'opening-stock-' + date + '.png';

    link.href =
    canvas.toDataURL('image/png');

    link.click();

  });
}

</script>

</body>
</html>