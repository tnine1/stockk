<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 

$user_id = $_SESSION['user_id'];
$shift_id = $_SESSION['shift_id'];

$id = (int)$_POST['ingredient_id'];
$qty = (float)$_POST['quantity'];

// 📉 Deduct from stock
mysqli_query($conn,"
  UPDATE stock 
  SET quantity = quantity - $qty
  WHERE ingredient_id = $id
");

// 💾 Save damage record
mysqli_query($conn,"
  INSERT INTO damaged_stock (ingredient_id, quantity, user_id, shift_id)
  VALUES ($id, $qty, $user_id, $shift_id)
");

header("Location: ../dashboard/barista.php");
?>