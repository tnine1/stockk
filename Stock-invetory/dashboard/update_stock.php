<?php
session_start();
include('../config/db.php');
// 🔐 Admin check (SAFE)
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
  die("⛔ Access denied");
}

// 🧾 Validate input
if(!isset($_POST['ingredient_id']) || !isset($_POST['quantity'])){
  die("⛔ Invalid request");
}

$id = (int) $_POST['ingredient_id'];
$qty = (float) $_POST['quantity'];

// 🔍 Check if stock exists
$check = mysqli_query($conn,"
  SELECT * FROM stock
  WHERE ingredient_id = $id
");

if(mysqli_num_rows($check) > 0){

  // ✏️ UPDATE EXISTING STOCK
  mysqli_query($conn,"
    UPDATE stock
    SET quantity = $qty
    WHERE ingredient_id = $id
  ");

} else {

  // ➕ INSERT IF NOT EXISTS
  mysqli_query($conn,"
    INSERT INTO stock (ingredient_id, quantity)
    VALUES ($id, $qty)
  ");
}

header("Location: stock.php");
exit;
?>