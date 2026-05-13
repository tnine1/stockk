<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 
date_default_timezone_set('Africa/Kigali');

/* 🔐 ACCESS CONTROL */
if(
  !isset($_SESSION['role']) ||
  (
    $_SESSION['role'] != 'barista' &&
    $_SESSION['role'] != 'admin'
  )
){
  die("
  <div style='
    font-family:Arial;
    background:#0f172a;
    color:white;
    padding:30px;
    text-align:center;
    min-height:100vh;
  '>

    <h2>⛔ Access Denied</h2>

    <p>You do not have permission to view this page.</p>

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

/* 🔐 LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
  header("Location: ../auth/login.php");
  exit;
}

$user_id = (int) $_SESSION['user_id'];
$shift_id = $_SESSION['shift_id'] ?? 0;

/* 👨‍🍳 USER INFO */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT *
  FROM users
  WHERE id = $user_id
"));

$staff_name = $user['name'] ?? 'Barista';

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

<title>Barista POS</title>

<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0"
>

<style>

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  padding:10px;
}

/* HEADER */
.site-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  background:#111827;
  padding:12px;
  border-radius:12px;
  margin-bottom:15px;
}

.logo img{
  height:55px;
  width:auto;
}

/* TOPBAR */
.topbar{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.btn{
  padding:10px 15px;
  border-radius:8px;
  text-decoration:none;
  color:white;
  font-weight:bold;
  font-size:14px;
}

.start{
  background:#22c55e;
}

.end{
  background:#ef4444;
}

.logout{
  background:#dc2626;
}

.btn:hover{
  opacity:0.9;
}

/* TITLE */
h2{
  text-align:center;
  margin-bottom:15px;
}

/* STATUS */
.status{
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  margin-bottom:15px;
  text-align:center;
  line-height:1.8;
}

.clock{
  color:#22c55e;
  font-weight:bold;
  margin-top:5px;
}

/* QUICK ITEMS */
.grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:10px;
  margin-bottom:15px;
}

.card{
  background:#1e293b;
  padding:18px;
  border-radius:10px;
  text-align:center;
  cursor:pointer;
  transition:0.2s;
  font-weight:bold;
}

.card:hover{
  background:#334155;
}

/* FORMS */
.form{
  background:#1e293b;
  padding:15px;
  border-radius:10px;
  margin-bottom:15px;
}

.form h3,
.form h4{
  margin-bottom:10px;
}

input,
select{
  width:100%;
  padding:12px;
  border:none;
  border-radius:8px;
  background:#0f172a;
  color:white;
  margin-top:10px;
}

button{
  border:none;
  border-radius:8px;
  cursor:pointer;
  padding:12px;
  color:white;
  font-weight:bold;
}

.save-btn{
  width:100%;
  background:#22c55e;
  margin-top:10px;
}

.save-btn:hover{
  background:#16a34a;
}

.add-btn{
  width:100%;
  background:#3b82f6;
  margin-top:10px;
}

.add-btn:hover{
  background:#2563eb;
}

.remove-btn{
  background:#ef4444;
  padding:10px;
}

.remove-btn:hover{
  background:#dc2626;
}

/* INGREDIENT ROW */
.ingredient-row{
  display:flex;
  gap:10px;
  align-items:center;
  margin-top:10px;
}

.ingredient-row select{
  flex:2;
}

.ingredient-row input{
  flex:1;
}

/* STOCK */
.stock-item{
  padding:10px;
  border-bottom:1px solid #334155;
}

.low{
  color:#ef4444;
  font-weight:bold;
}

.ok{
  color:#22c55e;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}

th, td{
  padding:10px;
  border-bottom:1px solid #334155;
  text-align:left;
}

th{
  background:#0f172a;
}

/* MOBILE */
@media(max-width:768px){

  .site-header{
    flex-direction:column;
    gap:10px;
  }

  .grid{
    grid-template-columns:1fr 1fr;
  }

  .ingredient-row{
    flex-direction:column;
    align-items:stretch;
  }
}

</style>

</head>

<body>

<!-- HEADER -->
<header class="site-header">

 

  <div class="topbar">

    <a href="../shifts/start_shift.php" class="btn start">
      ▶ Start Shift
    </a>

    <a href="../shifts/end_shift.php" class="btn end">
      ⏹ End Shift
    </a>
<a href="../stock/receive_stock.php" 
   style="
      display:inline-block;
      padding:12px 18px;
      background:#22c55e;
      color:white;
      text-decoration:none;
      border-radius:8px;
      font-weight:bold;
      margin-top:10px;
   ">
   📦 Receive Stock
</a>
    <a href="../auth/logout.php" class="btn logout">
      🚪 Logout
    </a>

  </div>

</header>

<h2>☕ Barista POS</h2>

<!-- STATUS -->
<div class="status">

  👨‍🍳 <b><?php echo $staff_name; ?></b>

  <br>

  📌 Shift:
  <b><?php echo $shift_type; ?></b>

  <br>

  <?php
  if($shift_id == 0){
    echo "⚠️ No Active Shift";
  } else {
    echo "✅ Shift Active";
  }
  ?>

  <div class="clock" id="clock"></div>

</div>

<!-- QUICK DRINKS -->
<div class="grid">

  <div class="card" onclick="setDrink('Mango Juice')">
    🥭 Mango Juice
  </div>

  <div class="card" onclick="setDrink('Cocktail')">
    🍹 Cocktail
  </div>

  <div class="card" onclick="setDrink('Smoothie')">
    🥤 Smoothie
  </div>

  <div class="card" onclick="setDrink('Coffee')">
    ☕ Coffee
  </div>

</div>

<!-- ORDER FORM -->
<div class="form">

<h3>🧾 Save Drink Order</h3>

<form method="POST" action="../usage/save_order.php">

  <input
    type="text"
    id="drink"
    name="drink_name"
    placeholder="Drink Name"
    required
  >

  <div id="ingredients-wrapper">

    <div class="ingredient-row">

      <select onchange="setIngredient(this)">

        <option value="">
          Select Ingredient
        </option>

        <?php

        $ingredients = mysqli_query($conn,"
          SELECT *
          FROM ingredients
          WHERE category != 'Kitchen'
          ORDER BY name ASC
        ");

        while($i = mysqli_fetch_assoc($ingredients)){

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
        placeholder="Qty"
        disabled
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
    💾 Save Order
  </button>

</form>

</div>

<!-- DAMAGED STOCK -->
<div class="form">

<h3>⚠️ Report Damaged Stock</h3>

<form method="POST" action="../stock/damage_stock.php">

  <select name="ingredient_id" required>

    <option value="">
      Select Item
    </option>

    <?php

    $damage_items = mysqli_query($conn,"
      SELECT *
      FROM ingredients
      WHERE category != 'Kitchen'
      ORDER BY name ASC
    ");

    while($i = mysqli_fetch_assoc($damage_items)){

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
    name="quantity"
    placeholder="Damaged Quantity"
    required
  >

  <button
    type="submit"
    class="save-btn"
    style="background:#ef4444;">
    🚨 Report Damage
  </button>

</form>

</div>

<!-- TODAY ORDERS -->
<div class="form">

<h3>🧾 Today's Orders</h3>

<?php

$orders = mysqli_query($conn,"
  SELECT drink_name, created_at
  FROM orders
  WHERE DATE(created_at)=CURDATE()
  ORDER BY id DESC
  LIMIT 5
");

if(mysqli_num_rows($orders) == 0){

  echo "No orders yet.";

} else {

  while($o = mysqli_fetch_assoc($orders)){

    echo "
    <div class='stock-item'>
      ☕ {$o['drink_name']}
      <br>
      <small>{$o['created_at']}</small>
    </div>
    ";
  }
}

?>

</div>

<!-- CURRENT STOCK -->
<div class="form">

<h3>📦 Current Barista Stock</h3>

<table>

<tr>
  <th>Item</th>
  <th>Qty</th>
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
  WHERE i.category != 'Kitchen'
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
    <td>{$qty} {$s['unit']}</td>
    <td>$status</td>
  </tr>
  ";
}

?>

</table>

</div>

<script>

/* ☕ QUICK DRINK */
function setDrink(name){

  document.getElementById('drink').value = name;
}

/* 📦 SET INGREDIENT */
function setIngredient(select){

  const row = select.parentElement;

  const input =
    row.querySelector('input');

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

/* ➕ ADD ROW */
function addRow(){

  const wrapper =
    document.getElementById('ingredients-wrapper');

  const row =
    document.createElement('div');

  row.classList.add('ingredient-row');

  row.innerHTML = `

    <select onchange="setIngredient(this)">

      <option value="">
        Select Ingredient
      </option>

      <?php

      $ingredients2 = mysqli_query($conn,"
        SELECT *
        FROM ingredients
        WHERE category != 'Kitchen'
        ORDER BY name ASC
      ");

      while($i = mysqli_fetch_assoc($ingredients2)){

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
      placeholder="Qty"
      disabled
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

  const wrapper =
    document.getElementById('ingredients-wrapper');

  if(wrapper.children.length > 1){

    btn.parentElement.remove();
  }
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

</script>

</body>
</html>