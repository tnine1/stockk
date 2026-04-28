<?php
session_start();
include('../config/db.php');

// 🔐 Admin only
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
  die("⛔ Access Denied");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stock Management</title>

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
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: none;
    }

    button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
      background: #22c55e;
      border: none;
      border-radius: 5px;
      color: white;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #334155;
      text-align: left;
    }

    .low {
      color: #ef4444;
      font-weight: bold;
    }

    .ok {
      color: #22c55e;
    }
  </style>
</head>

<body>

<h2>📦 Stock Management</h2>

<!-- ➕ ADD STOCK -->
<div class="card">
  <h3>➕ Add Stock</h3>

  <form method="POST" action="add_stock.php">
    <select name="ingredient_id" required>
      <option value="">Select Item</option>

      <?php
      $q = mysqli_query($conn,"SELECT * FROM ingredients");
      while($i = mysqli_fetch_assoc($q)){
        echo "<option value='{$i['id']}'>{$i['name']}</option>";
      }
      ?>
    </select>

   <input type="number" step="0.01" name="quantity" placeholder="New Quantity" required>

    <button>Add Stock</button>
  </form>
</div>

<!-- ✏️ UPDATE STOCK -->
<div class="card">
  <h3>✏️ Update Stock (Set Exact Value)</h3>

  <form method="POST" action="update_stock.php">
    <select name="ingredient_id" required>
      <option value="">Select Item</option>

      <?php
      $q = mysqli_query($conn,"SELECT * FROM ingredients");
      while($i = mysqli_fetch_assoc($q)){
        echo "<option value='{$i['id']}'>{$i['name']}</option>";
      }
      ?>
    </select>

    <input type="number" step="0.01" name="quantity" placeholder="New Quantity" required>

    <button>Update Stock</button>
  </form>
</div>

<!-- 📊 STOCK TABLE -->
<div class="card">
  <h3>📊 Current Stock</h3>

  <table>
    <tr>
      <th>Item</th>
      <th>Quantity</th>
      <th>Status</th>
    </tr>

    <?php
    $stock = mysqli_query($conn,"
      SELECT i.name, s.quantity
      FROM stock s
      JOIN ingredients i ON i.id = s.ingredient_id
    ");

    while($s = mysqli_fetch_assoc($stock)){

      $status = ($s['quantity'] < 5) 
        ? "<span class='low'>LOW</span>" 
        : "<span class='ok'>OK</span>";

      echo "<tr>
        <td>{$s['name']}</td>
        <td>{$s['quantity']}</td>
        <td>$status</td>
      </tr>";
    }
    ?>
  </table>
</div>

<!-- 🔙 BACK -->
<div class="card">
  <a href="admin.php" style="color:#22c55e;">⬅ Back to Dashboard</a>
</div>

</body>
</html>