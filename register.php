<?php
include('../config/db.php');

if(isset($_POST['register'])){

  $name = $_POST['name'];
  $username = $_POST['username'];
  $password = md5($_POST['password']);
  $role = $_POST['role']; // admin or barista

  mysqli_query($conn, "
    INSERT INTO users (name, username, password, role)
    VALUES ('$name', '$username', '$password', '$role')
  ");

  echo "User Registered Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
</head>
<body>

<h2>Register User</h2>

<form method="POST">

  <input name="name" placeholder="Full Name" required><br><br>

  <input name="username" placeholder="Username" required><br><br>

  <input type="password" name="password" placeholder="Password" required><br><br>

  <select name="role" required>
    <option value="barista">Barista</option>
    <option value="admin">Admin</option>
  </select><br><br>

  <button name="register">Register</button>

</form>

</body>
</html>