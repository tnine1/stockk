<?php
session_start();

// clear all session data
$_SESSION = array();

// destroy session
session_destroy();

// redirect to login page
header("Location: login.php");
exit;
?>