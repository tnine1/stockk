<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 

// 🔐 ADMIN + KITCHEN ACCESS
if(
  !isset($_SESSION['role']) ||
  (
    $_SESSION['role'] != 'admin' &&
    $_SESSION['role'] != 'kitchen'
  )
){
  die("⛔ Access Denied");
}

$message = "";

// ➕ ADD ITEM
if(isset($_POST['add'])){

  $name = mysqli_real_escape_string($conn, trim($_POST['name']));
  $unit = mysqli_real_escape_string($conn, $_POST['unit']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $low_stock_limit = floatval($_POST['minimum_stock']); // ✅ FIXED

  // VALIDATION
  if($name == "" || $category == "" || $unit == ""){

    $message = "⚠️ All fields are required";

  } else {

    // 🔍 CHECK DUPLICATE
    $check = mysqli_query($conn,"
      SELECT * FROM ingredients
      WHERE LOWER(name)=LOWER('$name')
    ");

    if(mysqli_num_rows($check) > 0){

      $message = "⚠️ Item already exists!";

    } else {

      // ✅ INSERT ITEM
      $insert = mysqli_query($conn, "
        INSERT INTO ingredients
        (
          name,
          unit,
          category,
          low_stock_limit
        )
        VALUES
        (
          '$name',
          '$unit',
          '$category',
          '$low_stock_limit'
        )
      ");

      if($insert){

        $ingredient_id = mysqli_insert_id($conn);

        // 📦 INITIALIZE STOCK
        mysqli_query($conn,"
          INSERT INTO stock (ingredient_id, quantity)
          VALUES ($ingredient_id, 0.00)
        ");

        $message = "✅ Item added successfully!";

      } else {

        $message = "❌ Database Error: " . mysqli_error($conn);
      }
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Inventory Item</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
  background:#0f172a;
  color:white;
  font-family:Arial;
  padding:20px;
}

.container{
  max-width:500px;
  margin:auto;
}

.card{
  background:#1e293b;
  padding:25px;
  border-radius:15px;
}

input,select{
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
  padding:12px;
  margin-top:15px;
  background:#22c55e;
  border:none;
  border-radius:8px;
  color:white;
  font-weight:bold;
}

.msg{
  margin-top:10px;
  text-align:center;
}

.success{color:#22c55e;}
.error{color:#ef4444;}

a{
  display:block;
  text-align:center;
  margin-top:10px;
  color:#22c55e;
}
</style>

</head>

<body>

<div class="container">

<h2 style="text-align:center;">➕ Add Item</h2>

<div class="card">

<form method="POST">

  <input type="text" name="name" placeholder="Item Name" required>

  <select name="category" required>
    <option value="">Select Category</option>
    <option value="Fruits">Fruits</option>
    <option value="Soft Drinks">Soft Drinks</option>
    <option value="Dairy">Dairy</option>
    <option value="Add-ons">Add-ons</option>
    <option value="Ice Cream">Ice Cream</option>
    <option value="Takeaways">Takeaways</option>
    <option value="Food">Food</option>
    <option value="Kitchen">Kitchen</option>
  </select>

  <select name="unit" required>
    <option value="">Select Unit</option>
    <option value="pcs">pcs</option>
    <option value="kg">kg</option>
    <option value="l">L</option>
  </select>

  <input type="number" step="0.01" name="minimum_stock" placeholder="Low Stock Limit" required>

  <button type="submit" name="add">➕ Add Item</button>

</form>

<div class="msg">
<?php
if($message != ""){
  $class = (strpos($message,'✅') !== false) ? "success" : "error";
  echo "<div class='$class'>$message</div>";
}
?>
</div>

<a href="admin.php">⬅ Back</a>

</div>

</div>

</body>
</html>