<?php
 $servername="localhost";
 $username="root";
 $password="";
 $dbName="hearthub";

 //make database connection

 $conn = new mysqli($servername, $username, $password, $dbName);
  //check connection_aborted

  if(!$conn){

      die("connection failed ". mysqli_connect_error());
  }

  else echo ("connection success")


?>