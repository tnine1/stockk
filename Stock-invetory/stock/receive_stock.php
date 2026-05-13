<?php
session_start();
include('../config/db.php');

date_default_timezone_set('Africa/Kigali');

/* 🔐 LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$role = $_SESSION['role'];

/* 🚫 ONLY BARISTA OR KITCHEN */
if($role != 'barista' && $role != 'kitchen' && $role != 'admin'){
  die("⛔ Access Denied");
}

/* 📥 SAVE RECEIVED STOCK */
if(isset($_POST['receive_stock'])){

    $ingredient_id = (int) $_POST['ingredient_id'];
    $quantity = (float) $_POST['quantity'];
    $note = mysqli_real_escape_string($conn, $_POST['note'] ?? '');
    $user_id = (int) $_SESSION['user_id'];

    if($ingredient_id > 0 && $quantity > 0){

        /* 🔍 CHECK EXISTING STOCK */
        $check = mysqli_query($conn,"
            SELECT quantity
            FROM stock
            WHERE ingredient_id = $ingredient_id
            LIMIT 1
        ");

        if(mysqli_num_rows($check) > 0){

            /* ➕ UPDATE STOCK */
            mysqli_query($conn,"
                UPDATE stock
                SET quantity = quantity + $quantity
                WHERE ingredient_id = $ingredient_id
            ");

        } else {

            /* ➕ INSERT NEW STOCK */
            mysqli_query($conn,"
                INSERT INTO stock (ingredient_id, quantity)
                VALUES ($ingredient_id, $quantity)
            ");
        }

        /* 📝 LOG RECEIVED STOCK */
        mysqli_query($conn,"
            INSERT INTO stock_received
            (ingredient_id, quantity, note, user_id, created_at)
            VALUES
            ($ingredient_id, $quantity, '$note', $user_id, NOW())
        ");

        echo "<script>alert('✅ Stock Received Successfully');window.location='receive_stock.php';</script>";

    } else {
        echo "<script>alert('⚠️ Invalid Data');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Receive Stock</title>

<style>
body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  padding:20px;
}

.card{
  background:#1e293b;
  padding:20px;
  border-radius:12px;
  max-width:500px;
  margin:auto;
}

input, select, textarea{
  width:100%;
  padding:12px;
  margin-top:10px;
  border:none;
  border-radius:8px;
  background:#0f172a;
  color:white;
}

button{
  width:100%;
  margin-top:15px;
  padding:12px;
  background:#22c55e;
  border:none;
  border-radius:8px;
  color:white;
  font-weight:bold;
  cursor:pointer;
}

button:hover{
  background:#16a34a;
}
</style>

</head>

<body>

<div class="card">

<h2>📦 Receive Stock</h2>

<form method="POST">

  <label>Item</label>

  <select name="ingredient_id" required>
    <option value="">Select Item</option>

    <?php
    $q = mysqli_query($conn,"SELECT * FROM ingredients ORDER BY name ASC");
    while($i = mysqli_fetch_assoc($q)){
      echo "<option value='{$i['id']}'>{$i['name']}</option>";
    }
    ?>
  </select>

  <label>Quantity</label>
  <input type="number" step="0.01" name="quantity" required>

  <label>Note</label>
  <textarea name="note" placeholder="Supplier / comment"></textarea>

  <button type="submit" name="receive_stock">
    ➕ Receive Stock
  </button>

</form>

</div>

</body>
</html>