<?php

// $mysqli = new mysqli("localhost","my_user","my_password","my_db");
$con = mysqli_connect('localhost', 'root', '', 'dbthesis');

if(!$con){
    die("Connection failed: " . mysqli_connect_error());
}

?>