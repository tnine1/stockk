<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['user_id']) || !isset($_SESSION['shift_id'])){
  die("⚠️ Start shift first!");
}

$user_id = $_SESSION['user_id'];
$shift_id = $_SESSION['shift_id'];

if(!isset($_POST['drink_name']) || empty($_POST['drink_name'])){
  die("⚠️ Drink name required!");
}

$drink = mysqli_real_escape_string($conn, $_POST['drink_name']);

// 🧾 Insert order
mysqli_query($conn,"
  INSERT INTO orders (user_id, shift_id, drink_name, created_at)
  VALUES ($user_id, $shift_id, '$drink', NOW())
");

$order_id = mysqli_insert_id($conn);

// 📦 Process ingredients
$ingredients = mysqli_query($conn,"SELECT * FROM ingredients");

while($i = mysqli_fetch_assoc($ingredients)){

  $field = "item_" . $i['id'];

  if(isset($_POST[$field]) && $_POST[$field] > 0){

    $qty = (float) $_POST[$field];
    $ingredient_id = $i['id'];

    // 🔍 Get current stock first
    $stockQuery = mysqli_query($conn,"
      SELECT quantity FROM stock 
      WHERE ingredient_id = $ingredient_id
    ");

    $stockRow = mysqli_fetch_assoc($stockQuery);
    $currentStock = $stockRow['quantity'];

    // 🚫 Prevent negative stock
    if($currentStock < $qty){
      die("⚠️ Not enough stock for " . $i['name']);
    }

    // 💾 Save usage
    mysqli_query($conn,"
      INSERT INTO order_items (order_id, ingredient_id, quantity)
      VALUES ($order_id, $ingredient_id, $qty)
    ");

    // 📉 Deduct stock safely
    mysqli_query($conn,"
      UPDATE stock 
      SET quantity = quantity - $qty
      WHERE ingredient_id = $ingredient_id
    ");
  }
}

echo "
<h3>✅ Order Saved Successfully</h3>
<a href='../dashboard/barista.php'>⬅ Back</a>
";
?>