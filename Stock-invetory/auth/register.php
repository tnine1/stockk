<?php
session_start();
include('../config/db.php');
include('../assets/header.php'); 

date_default_timezone_set('Africa/Kigali');

/* 🔐 STRICT ADMIN ONLY ACCESS */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
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
    <p>Only administrators can register new users.</p>

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

$message = "";

/* ➕ REGISTER USER */
if(isset($_POST['register'])){

  $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
  $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
  $password = $_POST['password'];
  $role = mysqli_real_escape_string($conn, $_POST['role']);
  $department = mysqli_real_escape_string($conn, $_POST['department']);

  /* ⚠️ VALIDATION */
  if($name == "" || $username == "" || $password == "" || $role == "" || $department == ""){

    $message = "⚠️ All fields are required";

  } else {

    /* 🔍 CHECK DUPLICATE USERNAME */
    $check = mysqli_query($conn,"
      SELECT id FROM users
      WHERE username='$username'
      LIMIT 1
    ");

    if(mysqli_num_rows($check) > 0){

      $message = "⚠️ Username already exists";

    } else {

      /* 🔒 HASH PASSWORD */
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      /* ➕ INSERT USER */
      $insert = mysqli_query($conn,"
        INSERT INTO users
        (name, username, password, role, department)
        VALUES
        ('$name', '$username', '$hashed_password', '$role', '$department')
      ");

      if($insert){
        $message = "✅ User Registered Successfully!";
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

<title>Register User</title>

<style>

body{
  font-family:Arial;
  background:#0f172a;
  color:white;
  padding:20px;
}

h2{
  text-align:center;
  margin-bottom:15px;
}

.card{
  background:#1e293b;
  padding:20px;
  border-radius:12px;
  max-width:450px;
  margin:auto;
  box-shadow:0 10px 25px rgba(0,0,0,0.3);
}

input, select{
  width:100%;
  padding:12px;
  margin-top:10px;
  border:none;
  border-radius:8px;
  background:#0f172a;
  color:white;
  box-sizing:border-box;
  outline:none;
}

button{
  width:100%;
  padding:12px;
  margin-top:15px;
  border:none;
  border-radius:8px;
  background:#22c55e;
  color:white;
  font-size:16px;
  cursor:pointer;
  font-weight:bold;
}

button:hover{
  background:#16a34a;
}

.msg{
  text-align:center;
  margin-top:15px;
  font-weight:bold;
}

.success{ color:#22c55e; }
.error{ color:#ef4444; }

.back{
  display:block;
  margin-top:15px;
  text-align:center;
  color:#94a3b8;
  text-decoration:none;
}

.back:hover{
  color:white;
}

</style>

</head>

<body>

<h2>👤 Register User (Admin Only)</h2>

<div class="card">

<form method="POST">

  <input name="name" placeholder="Full Name" required>

  <input name="username" placeholder="Username" required>

  <input type="password" name="password" placeholder="Password" required>

  <select name="role" required>
    <option value="">Select Role</option>
    <option value="barista">Barista</option>
    <option value="kitchen">Kitchen</option>
    <option value="admin">Admin</option>
  </select>

  <select name="department" required>
    <option value="">Select Department</option>
    <option value="barista">Barista Department</option>
    <option value="kitchen">Kitchen Department</option>
    <option value="management">Management</option>
  </select>

  <button name="register">➕ Register User</button>

</form>

<div class="msg">
<?php
if($message != ""){
  $class = (strpos($message,'✅') !== false) ? "success" : "error";
  echo "<div class='$class'>$message</div>";
}
?>
</div>

<a class="back" href="../dashboard/admin.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>