<?php
session_start();
include('../config/db.php');

$user = $_SESSION['user_id'];
$shift = $_SESSION['shift_id'];
$drink = $_POST['drink_name'];

mysqli_query($conn,"
INSERT INTO orders (shift_id,user_id,drink_name)
VALUES ($shift,$user,'$drink')
");

$order_id = mysqli_insert_id($conn);

$ingredients = [
  'mango'=>1,'passion'=>2,'prunes'=>3,
  'pineapple'=>4,'banana'=>5,'lemon'=>6
];

foreach($ingredients as $name=>$id){
  if($_POST[$name] > 0){
    $q = $_POST[$name];

    mysqli_query($conn,"
      INSERT INTO order_items (order_id,ingredient_id,quantity)
      VALUES ($order_id,$id,$q)
    ");

    mysqli_query($conn,"
      UPDATE stock SET quantity=quantity-$q WHERE ingredient_id=$id
    ");
  }
}

echo "Saved <a href='usage_form.php'>Back</a>";
?>