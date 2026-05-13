<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 
date_default_timezone_set('Africa/Kigali');

/* 🔐 LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

/* 🔐 ROLE CHECK */
if(
  !isset($_SESSION['role']) ||
  (
    $_SESSION['role'] != 'kitchen' &&
    $_SESSION['role'] != 'admin'
  )
){
  die("
  <div style='
    font-family:Arial;
    background:#0f172a;
    color:white;
    text-align:center;
    padding:60px;
    height:100vh;
  '>

    <h1>⛔ Access Denied</h1>

    <p>
      You do not have permission
      to view this page.
    </p>

    <a href='../auth/login.php' style='
      display:inline-block;
      margin-top:20px;
      padding:12px 25px;
      background:#22c55e;
      color:white;
      text-decoration:none;
      border-radius:8px;
      font-weight:bold;
    '>
      🔐 Go to Login
    </a>

  </div>
  ");
}

$user_id = (int) $_SESSION['user_id'];
$shift_id = (int) ($_SESSION['shift_id'] ?? 0);

/* 👨‍🍳 USER INFO */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT *
  FROM users
  WHERE id = $user_id
"));

$staff_name = $user['name'] ?? 'Kitchen Staff';

/* 📌 SHIFT INFO */
$shift = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT *
  FROM shifts
  WHERE id = $shift_id
"));

$shift_type = $shift['shift_type'] ?? 'No Shift';
?>

<!DOCTYPE html>
<html>
<head>

<title>Kitchen Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  margin:0;
  padding:10px;
}

h2{
  text-align:center;
  margin-bottom:10px;
}

.topbar{
  display:flex;
  gap:10px;
  margin-bottom:10px;
}

.btn{
  flex:1;
  padding:12px;
  border-radius:8px;
  text-align:center;
  text-decoration:none;
  color:white;
  font-weight:bold;
}

.start{
  background:#22c55e;
}

.end{
  background:#ef4444;
}

.status{
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  margin-bottom:10px;
  text-align:center;
}

.clock{
  margin-top:5px;
  color:#22c55e;
  font-weight:bold;
}

.grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:10px;
  margin-bottom:10px;
}

.card{
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  text-align:center;
  cursor:pointer;
  transition:0.2s;
}

.card:hover{
  background:#334155;
}

.form{
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  margin-top:10px;
}

input,
select{
  width:100%;
  padding:12px;
  margin-top:10px;
  border:none;
  border-radius:8px;
  background:#0f172a;
  color:white;
  box-sizing:border-box;
}

.ingredient-row{
  display:flex;
  gap:10px;
  margin-top:10px;
  align-items:center;
}

.ingredient-row select{
  flex:2;
}

.ingredient-row input{
  flex:1;
}

button{
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.add-btn{
  background:#3b82f6;
  color:white;
  width:100%;
  padding:12px;
  margin-top:10px;
}

.save-btn{
  background:#22c55e;
  color:white;
  width:100%;
  padding:12px;
  margin-top:10px;
  font-weight:bold;
}

.remove-btn{
  background:#ef4444;
  color:white;
  padding:10px;
}

.stock-table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}

.stock-table th,
.stock-table td{
  padding:10px;
  border-bottom:1px solid #334155;
  text-align:left;
}

.low{
  color:#ef4444;
  font-weight:bold;
}

.ok{
  color:#22c55e;
  font-weight:bold;
}

.section{
  display:none;
}

</style>

</head>

<body>

<h2>🍳 Kitchen Dashboard</h2>

<!-- TOPBAR -->
<div class="topbar">

  <a href="../shifts/start_shift.php" class="btn start">
    ▶ Start Shift
  </a>

  <a href="../shifts/end_shift.php" class="btn end">
    ⏹ End Shift
  </a>
<a href="../stock/receive_stock.php" 
   class="btn"
   style="
      background:#22c55e;
   ">
   📦 Receive Stock
</a>

</div>

<!-- STATUS -->
<div class="status">

  👨‍🍳 <b><?php echo htmlspecialchars($staff_name); ?></b>

  <br>

  📌 Shift:
  <b><?php echo htmlspecialchars($shift_type); ?></b>

  <div class="clock" id="clock"></div>

</div>

<!-- QUICK ITEMS -->
<div class="grid">

<?php

$items = mysqli_query($conn,"
  SELECT *
  FROM ingredients
  WHERE category='Kitchen'
  ORDER BY name ASC
");

while($i = mysqli_fetch_assoc($items)){

  $food_name = htmlspecialchars($i['name']);

  echo "
  <div class='card'
    onclick=\"setItem('$food_name')\">

    🍽 $food_name

  </div>";
}

?>

</div>

<!-- FORM -->
<div class="form">

<h3>🍳 Record Preparation</h3>

<form method="POST" action="../usage/save_order.php">

  <input
    id="food_item"
    name="food_name"
    placeholder="Food Name"
    required
  >

  <div id="wrapper">

    <div class="ingredient-row">

      <select name="ingredient_id[]" required>

        <option value="">
          Select Ingredient
        </option>

        <?php

        $q = mysqli_query($conn,"
          SELECT *
          FROM ingredients
          WHERE category='Kitchen'
          ORDER BY name ASC
        ");

        while($i = mysqli_fetch_assoc($q)){

          echo "
          <option value='{$i['id']}'>
            {$i['name']}
          </option>";
        }

        ?>

      </select>

      <input
        type="number"
        step="0.01"
        name="quantity[]"
        placeholder="Qty"
        required
      >

      <button
        type="button"
        class="remove-btn"
        onclick="removeRow(this)">
        ❌
      </button>

    </div>

  </div>

  <button
    type="button"
    class="add-btn"
    onclick="addRow()">
    ➕ Add More
  </button>

  <button
    type="submit"
    class="save-btn">
    💾 Save Preparation
  </button>

</form>

</div>

<!-- 📦 STOCK BUTTON -->
<button
  type="button"
  class="add-btn"
  onclick="showSection('stock')">
  📦 Current Kitchen Stock
</button>

<!-- 📦 STOCK SECTION -->
<div id="stock" class="form section">

  <h3>📦 Current Kitchen Stock</h3>

  <table class="stock-table">

    <tr>
      <th>Item</th>
      <th>Quantity</th>
      <th>Unit</th>
      <th>Status</th>
    </tr>

    <?php

    $stock = mysqli_query($conn,"
      SELECT
        i.name,
        i.unit,
        s.quantity

      FROM stock s

      JOIN ingredients i
      ON i.id = s.ingredient_id

      WHERE i.category = 'Kitchen'

      ORDER BY i.name ASC
    ");

    while($s = mysqli_fetch_assoc($stock)){

      $qty = (float) $s['quantity'];

      $status =
        ($qty <= 5)
        ? "<span class='low'>LOW</span>"
        : "<span class='ok'>OK</span>";

      echo "
      <tr>

        <td>{$s['name']}</td>

        <td>{$qty}</td>

        <td>{$s['unit']}</td>

        <td>$status</td>

      </tr>
      ";
    }

    ?>

  </table>

</div>

<script>

/* 🍽 QUICK SELECT */
function setItem(name){

  document.getElementById('food_item').value = name;
}

/* ➕ ADD ROW */
function addRow(){

  const wrapper =
    document.getElementById('wrapper');

  const row =
    document.createElement('div');

  row.classList.add('ingredient-row');

  row.innerHTML = `

    <select name="ingredient_id[]" required>

      <option value="">
        Select Ingredient
      </option>

      <?php

      $q = mysqli_query($conn,"
        SELECT *
        FROM ingredients
        WHERE category='Kitchen'
        ORDER BY name ASC
      ");

      while($i = mysqli_fetch_assoc($q)){

        echo "
        <option value='{$i['id']}'>
          {$i['name']}
        </option>";
      }

      ?>

    </select>

    <input
      type="number"
      step="0.01"
      name="quantity[]"
      placeholder="Qty"
      required
    >

    <button
      type="button"
      class="remove-btn"
      onclick="removeRow(this)">
      ❌
    </button>
  `;

  wrapper.appendChild(row);
}

/* ❌ REMOVE ROW */
function removeRow(btn){

  btn.parentElement.remove();
}

/* ⏰ CLOCK */
function updateClock(){

  const now = new Date();

  const clock =
    document.getElementById('clock');

  if(clock){

    clock.innerHTML =
      now.toLocaleString('en-GB', {
        timeZone:'Africa/Kigali'
      });
  }
}

setInterval(updateClock,1000);

updateClock();

/* 📦 SHOW STOCK */
function showSection(id){

  document
    .querySelectorAll('.section')
    .forEach(sec => {
      sec.style.display = 'none';
    });

  const target =
    document.getElementById(id);

  if(target){

    target.style.display = 'block';
  }
}

</script>

</body>
</html>