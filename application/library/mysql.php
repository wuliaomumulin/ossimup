<?php
$con = mysqli_connect("localhost:3306", "root", 123456, "alienvault");
mysqli_query($con, "set names utf8");
if (!$con) {
    die("Error:" . mysqli_error());
}
?>