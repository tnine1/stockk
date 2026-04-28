<?php
session_start();
include('../config/db.php');

if($_SESSION['role'] != 'admin'){
  die("Access denied");
}

$id = $_POST['ingredient_id'];
$qty = $_POST['quantity'];

mysqli_query($conn,"
  UPDATE stock 
  SET quantity = $qty 
  WHERE ingredient_id = $id
");

header("Location: stock.php");
?>