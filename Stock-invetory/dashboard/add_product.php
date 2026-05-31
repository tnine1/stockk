<?php
session_start();
include('../config/db.php');

if(
 !isset($_SESSION['user_id'])
){
 die("Access denied");
}

$msg="";

if(isset($_POST['save'])){

$product_name=mysqli_real_escape_string(
$conn,
$_POST['product_name']
);

$category=mysqli_real_escape_string(
$conn,
$_POST['category']
);

$department=mysqli_real_escape_string(
$conn,
$_POST['department']
);

$price=$_POST['price'];

$cost_price=$_POST['cost_price'];

$stock_qty=$_POST['stock_qty'];

mysqli_query($conn,"
INSERT INTO products(
product_name,
category,
department,
price,
cost_price,
stock_qty
)
VALUES(
'$product_name',
'$category',
'$department',
'$price',
'$cost_price',
'$stock_qty'
)
");

$msg="Product Added";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>

<style>

body{
background:#0f172a;
color:white;
font-family:Arial;
padding:20px;
}

.card{
max-width:700px;
margin:auto;
background:#1e293b;
padding:25px;
border-radius:15px;
}

input,select{
width:100%;
padding:12px;
margin-top:10px;
margin-bottom:10px;
border:none;
border-radius:10px;
}

button{
width:100%;
padding:12px;
background:#22c55e;
border:none;
color:white;
font-weight:bold;
border-radius:10px;
cursor:pointer;
}

</style>

</head>

<body>

<div class="card">

<h2>Add Product</h2>

<form method="POST">

<input
name="product_name"
placeholder="Product Name"
required>

<input
name="category"
placeholder="Category"
required>

<select name="department">

<option value="barista">
Barista
</option>

<option value="kitchen">
Kitchen
</option>

<option value="snacks">
Snacks
</option>

<option value="retail">
Retail
</option>

</select>

<input
type="number"
step="0.01"
name="price"
placeholder="Selling Price"
required>

<input
type="number"
step="0.01"
name="cost_price"
placeholder="Cost Price">

<input
type="number"
step="0.01"
name="stock_qty"
placeholder="Opening Stock">

<button name="save">
Save Product
</button>

</form>

<?php echo $msg; ?>

</div>

</body>
</html>
