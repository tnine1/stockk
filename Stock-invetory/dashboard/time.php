<?php
date_default_timezone_set('Africa/Kigali');

echo "PHP TIME: " . date('Y-m-d H:i:s') . "<br>";

$q = mysqli_query($conn,"SELECT NOW() as t");
$r = mysqli_fetch_assoc($q);

echo "MYSQL TIME: " . $r['t'];
?>