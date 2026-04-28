<?php
session_start();
include('../config/db.php');

// 🔐 Admin only
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
  die("⛔ Access Denied");
}

$message = "";

// ➕ ADD ITEM
if(isset($_POST['add'])){

  $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
  $unit = $_POST['unit'];

  if($name == ""){
    $message = "⚠️ Item name required";
  } else {

    // 🔍 check duplicate
    $check = mysqli_query($conn,"
      SELECT * FROM ingredients 
      WHERE LOWER(name) = LOWER('$name')
    ");

    if(mysqli_num_rows($check) > 0){
      $message = "⚠️ Item already exists!";
    } else {

      // insert ingredient
      mysqli_query($conn,"
        INSERT INTO ingredients (name, unit)
        VALUES ('$name', '$unit')
      ");

      $id = mysqli_insert_id($conn);

      // initialize stock with 0.00
      mysqli_query($conn,"
        INSERT INTO stock (ingredient_id, quantity)
        VALUES ($id, 0.00)
      ");

      $message = "✅ Item added successfully!";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Item</title>

  <style>
    body {
      font-family: Arial;
      background: #0f172a;
      color: white;
      padding: 15px;
    }

    h2 {
      text-align: center;
    }

    .card {
      background: #1e293b;
      padding: 20px;
      border-radius: 10px;
      max-width: 400px;
      margin: auto;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
      border: none;
    }

    button {
      margin-top: 15px;
      width: 100%;
      padding: 12px;
      background: #22c55e;
      border: none;
      border-radius: 5px;
      color: white;
      font-size: 16px;
    }

    .msg {
      margin-top: 10px;
      text-align: center;
      font-weight: bold;
    }

    a {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #22c55e;
      text-decoration: none;
    }
  </style>
</head>

<body>

<h2>➕ Add New Item</h2>

<div class="card">

  <form method="POST">

    <input name="name" placeholder="Item Name (e.g. Mango)" required>

    <select name="unit" required>
      <option value="">Select Unit</option>
      <option value="pcs">Pieces (pcs)</option>
      <option value="ml">Milliliters (ml)</option>
      <option value="g">Grams (g)</option>
      <option value="kg">Kilograms (kg)</option>
      <option value="bottle">Bottle</option>
      <option value="cup">Cup</option>
    </select>

    <button name="add">Add Item</button>

  </form>

  <div class="msg"><?php echo $message; ?></div>

  <a href="admin.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>