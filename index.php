<?php
session_start();

// If user already logged in → redirect based on role
if(isset($_SESSION['user_id'])){

  if($_SESSION['role'] == 'admin'){
    header("Location: dashboard/admin.php");
    exit;
  }

  if($_SESSION['role'] == 'barista'){
    header("Location: dashboard/barista.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Le Chic Café System</title>

  <style>
    body {
      font-family: Arial;
      background: #0f172a;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .box {
      background: #1e293b;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      width: 300px;
    }

    h1 {
      margin-bottom: 10px;
    }

    p {
      color: #94a3b8;
    }

    a {
      display: block;
      margin-top: 15px;
      padding: 12px;
      background: #22c55e;
      border-radius: 5px;
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    a.secondary {
      background: #334155;
    }
  </style>
</head>

<body>

<div class="box">

  <h1>☕ Le Chic Café</h1>
  <p>Inventory & Barista System</p>

  <a href="auth/login.php">🔐 Login</a>

  <a href="auth/register.php" class="secondary">➕ Register (Admin only)</a>

</div>

</body>
</html>