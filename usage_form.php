<!DOCTYPE html>
<html>
<head>
<title>POS</title>
<script>
function setDrink(n){document.getElementById('drink').value=n;}
</script>
</head>
<body>

<h2>Barista POS</h2>

<button onclick="setDrink('Mango Juice')">Mango</button>
<button onclick="setDrink('Cocktail')">Cocktail</button>

<form method="POST" action="save_order.php">

<input id="drink" name="drink_name" placeholder="Drink"><br>

Mango <input type="number" step="0.1" name="mango"><br>
Passion <input type="number" step="0.1" name="passion"><br>
Prunes <input type="number" step="0.1" name="prunes"><br>
Pineapple <input type="number" step="0.1" name="pineapple"><br>
Banana <input type="number" step="0.1" name="banana"><br>
Lemon <input type="number" step="0.1" name="lemon"><br>

<button>Save</button>
</form>

<a href="../shifts/end_shift.php">End Shift</a>

</body>
</html>