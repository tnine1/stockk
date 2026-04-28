<?php
session_start();
include('../config/db.php');

// 🔐 check login
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$shift_id = $_SESSION['shift_id'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Barista POS</title>

  <style>
    body {
      font-family: Arial;
      background: #0f172a;
      color: white;
      margin: 0;
      padding: 10px;
    }

    h2 {
      text-align: center;
      margin-bottom: 10px;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .btn {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      color: white;
      text-decoration: none;
    }

    .start { background: #22c55e; }
    .end { background: #ef4444; }

    .status {
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(2,1fr);
      gap: 10px;
      margin-bottom: 10px;
    }

    .card {
      background: #1e293b;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
    }

    .card:hover {
      background: #334155;
    }

   .form {
  background: #1e293b;
  padding: 20px;
  border-radius: 12px;
  margin-top: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.form h4 {
  margin-bottom: 10px;
  color: #f1f5f9;
  font-size: 16px;
}

.form form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.form input[type="text"],
.form input[type="number"] {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: none;
  outline: none;
  background: #0f172a;
  color: white;
  font-size: 14px;
}

.form input::placeholder {
  color: #94a3b8;
}

.form label {
  font-size: 14px;
  margin-top: 5px;
  color: #cbd5e1;
}

.form .ingredient-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.form button {
  margin-top: 10px;
  padding: 12px;
  border: none;
  border-radius: 8px;
  background: #22c55e;
  color: white;
  font-size: 16px;
  cursor: pointer;
  transition: 0.2s;
}

.form button:hover {
  background: #16a34a;
}

.form br {
  display: none; /* removes ugly line breaks */
}


    .stock {
      background: #1e293b;
      padding: 10px;
      border-radius: 10px;
      margin-top: 10px;
    }

    .low {
      color: #ef4444;
      font-weight: bold;
    }
  </style>

  <script>
    function setDrink(name){
      document.getElementById('drink').value = name;
    }
  </script>
</head>

<body>

<h2>☕ Barista POS</h2>

<!-- TOP BAR -->
<div class="topbar">
  <a href="../shifts/start_shift.php" class="btn start">▶ Start</a>
  <a href="../shifts/end_shift.php" class="btn end">⏹ End</a>
</div>

<!-- SHIFT STATUS -->
<div class="status">
  <?php
  if($shift_id == 0){
    echo "⚠️ No active shift";
  } else {
    echo "✅ Shift Active (ID: $shift_id)";
  }
  ?>
</div>

<!-- QUICK DRINKS -->
<div class="grid">
  <div class="card" onclick="setDrink('Mango Juice')">🥭 Mango</div>
  <div class="card" onclick="setDrink('Cocktail')">🍹 Cocktail</div>
  <div class="card" onclick="setDrink('Smoothie')">🥤 Smoothie</div>
  <div class="card" onclick="setDrink('Custom Drink')">➕ Custom</div>
</div>

<!-- ORDER FORM -->
<div class="form">
  <form method="POST" action="../usage/save_order.php">

    <input id="drink" name="drink_name" placeholder="Drink Name" required>

   <h4>Ingredients Used</h4>
<div id="ingredients-wrapper">

  <div class="ingredient-row">
    <select onchange="setIngredient(this)">
      <option value="">Select Item</option>

      <?php
      $q = mysqli_query($conn,"SELECT * FROM ingredients");
      while($i = mysqli_fetch_assoc($q)){
        echo "<option value='{$i['id']}'>{$i['name']}</option>";
      }
      ?>
    </select>

    <input type="number" step="0.01" placeholder="Qty" disabled>

    <!-- ❌ remove button -->
    <button type="button" class="remove-btn" onclick="removeRow(this)">❌</button>
  </div>

</div>

<button type="button" onclick="addRow()">➕ Add More</button>
    <button>💾 Save Order</button>

  </form>
</div>

<!-- TODAY ORDERS -->
<div class="form">
  <h4>🧾 Today Orders</h4>

  <?php
  $orders = mysqli_query($conn,"
    SELECT drink_name, created_at 
    FROM orders 
    WHERE DATE(created_at)=CURDATE()
    ORDER BY id DESC LIMIT 5
  ");

  while($o = mysqli_fetch_assoc($orders)){
    echo "{$o['drink_name']} <small>({$o['created_at']})</small><br>";
  }
  ?>
</div>

<!-- STOCK VIEW -->
<div class="stock">
  <h4>📦 Current Stock</h4>

  <?php
  $stock = mysqli_query($conn,"
    SELECT i.name, s.quantity 
    FROM stock s
    JOIN ingredients i ON i.id=s.ingredient_id
  ");

  while($s = mysqli_fetch_assoc($stock)){

    $class = ($s['quantity'] < 5) ? "low" : "";

    echo "<div class='$class'>{$s['name']} : {$s['quantity']}</div>";
  }
  ?>
</div>
<script>
function setIngredient(select){
  const row = select.parentElement;
  const input = row.querySelector('input');

  const ingredientId = select.value;

  if(ingredientId){
    input.disabled = false;
    input.name = "item_" + ingredientId;
  } else {
    input.disabled = true;
    input.name = "";
    input.value = "";
  }
}

function addRow(){
  const wrapper = document.getElementById('ingredients-wrapper');

  const row = document.createElement('div');
  row.classList.add('ingredient-row');

  row.innerHTML = `
    <select onchange="setIngredient(this)">
      <option value="">Select Item</option>
      <?php
      $q = mysqli_query($conn,"SELECT * FROM ingredients");
      while($i = mysqli_fetch_assoc($q)){
        echo "<option value='{$i['id']}'>{$i['name']}</option>";
      }
      ?>
    </select>

    <input type="number" step="0.01" placeholder="Qty" disabled>

    <button type="button" class="remove-btn" onclick="removeRow(this)">❌</button>
  `;

  wrapper.appendChild(row);
}

function removeRow(btn){
  const wrapper = document.getElementById('ingredients-wrapper');

  // ❗ Prevent deleting the last row
  if(wrapper.children.length > 1){
    btn.parentElement.remove();
  }
}
</script>
</body>
</html>