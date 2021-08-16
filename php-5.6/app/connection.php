<?php
$servername = "mysql-8.0";
$username = "username";
$password = "password";

// Create connection
$conn = mysql_connect($servername, $username, $password);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>