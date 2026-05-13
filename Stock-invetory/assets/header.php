<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header style="
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:10px 20px;
  background:#111827;
  border-bottom:1px solid #1f2937;
">

  <!-- LOGO -->
  <div>
    <img src="/Stock-invetory/assets/images/t9logo.png"
         style="height:55px;"
         alt="Tnine&Ciero Logo">
  </div>

  <!-- NAV -->
  <nav style="display:flex; gap:10px;">

    <a href="/Stock-invetory/index.php"
       style="color:white;text-decoration:none;">
      Home
    </a>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
      <a href="/Stock-invetory/dashboard/admin.php"
         style="color:#22c55e;text-decoration:none;">
        Admin
      </a>
    <?php } ?>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'barista'){ ?>
      <a href="/Stock-invetory/dashboard/barista.php"
         style="color:#22c55e;text-decoration:none;">
        POS
      </a>
    <?php } ?>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'kitchen'){ ?>
      <a href="/Stock-invetory/dashboard/kitchen.php"
         style="color:#22c55e;text-decoration:none;">
        Kitchen
      </a>
    <?php } ?>

    <a href="/Stock-invetory/auth/logout.php"
       style="color:#ef4444;text-decoration:none;">
      Logout
    </a>

  </nav>

</header> 