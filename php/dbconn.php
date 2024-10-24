<?php

$config = include('C:\xampp\htdocs\hearthub\config.php');

 $servername=$config['servername'];
 $username =$config['username'];
 $password =$config['password'];
 $dbname =$config['database'];

 $conn = new mysqli($servername, $username, $password, $dbname);


  if(!$conn){

      die("Connection failed: " . $conn->connect_error);
  }

  echo "Connected successfully to Database";

  
?>