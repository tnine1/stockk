<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// 👨‍🍳 Get barista name
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT name FROM users WHERE id = $user_id
"));

$barista_name = $user['name'];

// 🔍 Check active shift
$check = mysqli_query($conn,"
  SELECT * FROM shifts 
  WHERE user_id = $user_id AND end_time IS NULL
");

if(mysqli_num_rows($check) > 0){

  // ♻️ Restore shift if already exists
  $shift = mysqli_fetch_assoc($check);
  $_SESSION['shift_id'] = $shift['id'];

  header("Location: ../dashboard/barista.php");
  exit;
}

// 🆕 Create new shift
mysqli_query($conn,"
  INSERT INTO shifts (user_id, start_time)
  VALUES ($user_id, NOW())
");

$shift_id = mysqli_insert_id($conn);

// Save session
$_SESSION['shift_id'] = $shift_id;

// 📦 Copy current stock → opening stock snapshot
$stock = mysqli_query($conn,"
  SELECT ingredient_id, quantity FROM stock
");

while($s = mysqli_fetch_assoc($stock)){
  $ingredient_id = $s['ingredient_id'];
  $qty = $s['quantity'];

  mysqli_query($conn,"
    INSERT INTO shift_stock (shift_id, ingredient_id, opening_quantity)
    VALUES ($shift_id, $ingredient_id, $qty)
  ");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Shift Started</title>

  <style>
    body {
      font-family: Arial;
      background: #0f172a;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .card {
      background: #1e293b;
      padding: 20px;
      border-radius: 10px;
      max-width: 450px;
      margin: auto;
    }

    .ok { color: #22c55e; }
    .low { color: #ef4444; }

    a {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #22c55e;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .header {
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

<div class="card">

  <h2>✅ Shift Started</h2>

  <div class="header">
    👨‍🍳 Barista: <b><?php echo $barista_name; ?></b><br>
    🕒 Started at: <?php echo date("Y-m-d H:i:s"); ?>
  </div>

  <p>📦 Opening Stock Snapshot</p>

  <?php
  $open = mysqli_query($conn,"
    SELECT i.name, ss.opening_quantity
    FROM shift_stock ss
    JOIN ingredients i ON i.id = ss.ingredient_id
    WHERE ss.shift_id = $shift_id
  ");

  while($o = mysqli_fetch_assoc($open)){
    $class = ($o['opening_quantity'] < 5) ? "low" : "ok";

    echo "<div class='$class'>
      {$o['name']} : {$o['opening_quantity']}
    </div>";
  }
  ?>

  <a href="../dashboard/barista.php">⬅ Go to Dashboard</a>

</div>

</body>
</html>