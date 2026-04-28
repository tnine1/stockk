<?php
session_start();
include('../config/db.php');

$error = "";

if(isset($_POST['login'])){

  // Clean inputs
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // Validate empty fields
  if(empty($username) || empty($password)){
    $error = "All fields are required!";
  } else {

    // Convert password to md5 (KEEPING YOUR LOGIC)
    $password = md5($password);

    // Escape inputs (basic protection)
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $q = mysqli_query($conn, $sql);

    if(!$q){
      die("Query Error: " . mysqli_error($conn)); // helps debugging
    }

    if(mysqli_num_rows($q) > 0){

      $user = mysqli_fetch_assoc($q);

      // Sessions
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];

      // Redirect
      if($user['role'] == 'admin'){
        header("Location: ../dashboard/admin.php");
      } else {
        header("Location: ../dashboard/barista.php");
      }
      exit();

    } else {
      $error = "Wrong username or password!";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
    body {
      font-family: Arial;
      background: linear-gradient(135deg, #0f172a, #1e293b);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    .box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 320px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    input:focus {
      border-color: #2563eb;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background: #1d4ed8;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

<div class="box">
  <h2>Login</h2>

  <?php if(!empty($error)): ?>
    <div class="error"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="login">Login</button>
  </form>
</div>

</body>
</html>