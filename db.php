<?php
$conn = mysqli_connect("localhost", "root", "", "lechic_bms", 3307);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>