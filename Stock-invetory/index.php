<?php
session_start();
include('../assets/header.php'); 
?>
<!DOCTYPE html>
<html>
<head>
  <title>Le Chic Café System</title>

  <style>
    body{
      margin:0;
      font-family:Arial;
      background:#0f172a;
      color:white;
    }

    .container{
      max-width:900px;
      margin:auto;
      padding:40px 20px;
      text-align:center;
    }

    .logo{
      font-size:40px;
      font-weight:bold;
      color:#22c55e;
      margin-bottom:10px;
    }

    .subtitle{
      color:#94a3b8;
      margin-bottom:30px;
    }

    .card{
      background:#1e293b;
      padding:25px;
      border-radius:12px;
      margin-top:20px;
      text-align:left;
      box-shadow:0 10px 30px rgba(0,0,0,0.3);
    }

    .btn{
      display:inline-block;
      padding:12px 18px;
      margin:10px 5px;
      border-radius:8px;
      text-decoration:none;
      color:white;
      font-weight:bold;
    }

    .login{ background:#22c55e; }
    .register{ background:#334155; }
    .info{ background:#2563eb; }

    .grid{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
      gap:15px;
      margin-top:20px;
    }

    .feature{
      background:#1e293b;
      padding:20px;
      border-radius:10px;
      text-align:left;
    }

    .feature h3{
      color:#22c55e;
      margin-bottom:10px;
    }

  </style>
</head>

<body>

<div class="container">

  <div class="logo">☕ Le Chic Café</div>
  <div class="subtitle">Inventory • Barista • Kitchen • Real-time Stock System</div>

  <!-- ACTION BUTTONS -->
  <a class="btn login" href="auth/login.php">🔐 Login</a>
  <a class="btn register" href="auth/register.php">➕ Register (Admin Only)</a>

  <!-- SYSTEM DESCRIPTION -->
  <div class="card">

    <h2>📊 System Overview</h2>

    <p>
      This system is designed to manage café operations in real-time,
      including stock tracking, shift control, order management, and department separation.
    </p>

    <p>
      Each staff member (Barista or Kitchen) sees only their allowed stock and operations,
      ensuring accuracy, transparency, and security.
    </p>

  </div>

  <!-- FEATURES -->
  <div class="grid">

    <div class="feature">
      <h3>📦 Smart Stock Control</h3>
      Tracks stock in real-time and reduces errors in inventory.
    </div>

    <div class="feature">
      <h3>🕒 Shift Management</h3>
      Opening and closing stock snapshots per shift.
    </div>

    <div class="feature">
      <h3>🍹 Department Separation</h3>
      Barista and Kitchen see only their own stock.
    </div>

    <div class="feature">
      <h3>📊 Live Reports</h3>
      Admin can monitor usage, damage, and stock instantly.
    </div>

  </div>

</div>

</body>
</html>