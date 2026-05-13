<?php
session_start();
include('../config/db.php');

date_default_timezone_set('Africa/Kigali');

$error = "";

/* 🔐 LOGIN */
if(isset($_POST['login'])){

    $username = trim(mysqli_real_escape_string(
        $conn,
        $_POST['username']
    ));

    $password = trim($_POST['password']);

    if(empty($username) || empty($password)){

        $error = "⚠️ All fields are required";

    } else {

        // 🔍 FIND USER
        $query = mysqli_query($conn,"
            SELECT *
            FROM users
            WHERE username='$username'
            LIMIT 1
        ");

        if(mysqli_num_rows($query) > 0){

            $user = mysqli_fetch_assoc($query);

            $storedPassword = $user['password'];

            $loginSuccess = false;

            // ✅ HASH PASSWORD
            if(password_verify(
                $password,
                $storedPassword
            )){
                $loginSuccess = true;
            }

            // ✅ OLD MD5 SUPPORT
            elseif(md5($password) == $storedPassword){
                $loginSuccess = true;
            }

            // ✅ SUCCESS LOGIN
            if($loginSuccess){

                session_regenerate_id(true);

                $_SESSION['user_id']    = $user['id'];
                $_SESSION['name']       = $user['name'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['role']       = $user['role'];
                $_SESSION['department'] = $user['department'];

                // 🔀 REDIRECT
                if($user['role'] == 'admin'){

                    header("Location: ../dashboard/admin.php");

                } elseif($user['role'] == 'kitchen') {

                    header("Location: ../dashboard/kitchen.php");

                } else {

                    header("Location: ../dashboard/barista.php");
                }

                exit();

            } else {

                $error = "❌ Wrong password";
            }

        } else {

            $error = "❌ User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Le Chic Café | Login</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, sans-serif;
    min-height:100vh;
    background:
    linear-gradient(
        135deg,
        #0f172a,
        #111827,
        #1e293b
    );

    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
    color:white;
}

/* MAIN CONTAINER */
.container{
    width:100%;
    max-width:420px;
}

/* CARD */
.card{
    background:rgba(30,41,59,0.95);
    border:1px solid rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:35px 30px;
    box-shadow:
    0 10px 35px rgba(0,0,0,0.4);
}

/* LOGO */
.logo{
    text-align:center;
    margin-bottom:20px;
}

.logo img{
    width:110px;
    height:auto;
    object-fit:contain;
}

/* TITLE */
h1{
    text-align:center;
    font-size:28px;
    margin-bottom:8px;
    color:#22c55e;
}

.subtitle{
    text-align:center;
    color:#cbd5e1;
    font-size:14px;
    margin-bottom:25px;
    line-height:1.5;
}

/* ERROR */
.error{
    background:#7f1d1d;
    border:1px solid #ef4444;
    color:#fecaca;
    padding:12px;
    border-radius:10px;
    margin-bottom:18px;
    text-align:center;
    font-size:14px;
}

/* FORM */
.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    color:#e2e8f0;
    font-size:14px;
    font-weight:bold;
}

input{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#0f172a;
    color:white;
    font-size:15px;
    outline:none;
    border:1px solid #334155;
    transition:0.3s;
}

input:focus{
    border-color:#22c55e;
    box-shadow:0 0 0 3px rgba(34,197,94,0.2);
}

input::placeholder{
    color:#94a3b8;
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#22c55e;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
    margin-top:5px;
}

button:hover{
    background:#16a34a;
    transform:translateY(-2px);
}

/* FOOTER */
.footer{
    text-align:center;
    margin-top:22px;
    color:#94a3b8;
    font-size:13px;
    line-height:1.6;
}

/* MOBILE */
@media(max-width:480px){

    .card{
        padding:25px 20px;
    }

    h1{
        font-size:24px;
    }

    .logo img{
        width:90px;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="card">

        <!-- LOGO -->
        <div class="logo">
            <img
                src="../assets/images/t9logo.png"
                alt="Le Chic Café Logo"
            >
        </div>

        <!-- HEADING -->
        <h1>☕ Le Chic Café</h1>

        <div class="subtitle">
            Smart Inventory, POS & Staff Management System
        </div>

        <!-- ERROR -->
        <?php if($error != ""){ ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <!-- FORM -->
        <form method="POST">

            <div class="form-group">

                <label>
                    👤 Username
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    🔒 Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <button
                type="submit"
                name="login"
            >
                🔐 Login to System
            </button>

        </form>

        <!-- FOOTER -->
        <div class="footer">

            © <?php echo date('Y'); ?>
            Le Chic Café Management System

            <br>

            Powered by Tnine & Ciero Tec

        </div>

    </div>

</div>

</body>
</html>