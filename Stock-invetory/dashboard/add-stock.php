<?php
session_start();
include('../config/db.php');

if($_SESSION['role'] != 'admin'){
  die("Access denied");
}

$id = $_POST['ingredient_id'];
$qty = $_POST['quantity'];

// check if exists
$check = mysqli_query($conn,"SELECT * FROM stock WHERE ingredient_id=$id");

if(mysqli_num_rows($check) > 0){
  mysqli_query($conn,"
    UPDATE stock 
    SET quantity = quantity + $qty 
    WHERE ingredient_id=$id
  ");
} else {
  mysqli_query($conn,"
    INSERT INTO stock (ingredient_id, quantity)
    VALUES ($id, $qty)
  ");
}

header("Location: stock.php");
?>