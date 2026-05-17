
<?php
date_default_timezone_set('Africa/Kigali');

$conn = mysqli_connect("localhost", "root", "", "lechic_bms");

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
mysqli_query($conn, "SET time_zone = '+02:00'");
?>
