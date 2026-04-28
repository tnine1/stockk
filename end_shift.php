<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$shift_id = $_SESSION['shift_id'] ?? 0;
// 👨‍🍳 Get barista name
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT name FROM users WHERE id = $user_id
"));

$barista_name = $user['name'];

if($shift_id == 0){
  echo "⚠️ No active shift!";
  exit;
}

// ✅ End shift
mysqli_query($conn,"
  UPDATE shifts 
  SET end_time = NOW()
  WHERE id = $shift_id
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Shift Ended</title>

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
      max-width: 400px;
      margin: auto;
    }

    .low { color: #ef4444; }
    .ok { color: #22c55e; }

    a {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 20px;
      background: #22c55e;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .logout {
      background: #ef4444;
    }
  </style>

  <script>
    // ⏱ Auto logout after 30 seconds
    setTimeout(function(){
      window.location.href = "../auth/logout.php";
    }, 30000);
  </script>

</head>

<body>

<div class="card">
  <h2>✅ Shift Ended</h2>
  <div class="header">
    👨‍🍳 Barista: <b><?php echo $barista_name; ?></b><br>
    🕒 Ended at: <?php echo date("Y-m-d H:i:s"); ?>
  </div>
  <p>📦 Closing Stock</p>

  <?php
  $stock = mysqli_query($conn,"
    SELECT i.name, s.quantity
    FROM stock s
    JOIN ingredients i ON i.id = s.ingredient_id
  ");

  while($s = mysqli_fetch_assoc($stock)){
    $class = ($s['quantity'] < 5) ? "low" : "ok";
    echo "<div class='$class'>{$s['name']} : {$s['quantity']}</div>";
  }
  ?>

  <!-- PDF BUTTON -->
  <a href="download_stock_pdf.php" target="_blank">
    📄 Download PDF
  </a>

  <!-- MANUAL LOGOUT -->
  <a href="../auth/logout.php" class="logout">
    🚪 Logout Now
  </a>

  <p style="margin-top:15px; color:#facc15;">
    ⏱ You will be logged out automatically in 30 seconds...
  </p>
</div>

</body>
</html>

<?php
// remove shift session AFTER display
unset($_SESSION['shift_id']);
?>