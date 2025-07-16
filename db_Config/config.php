<?php 

$Server = "localhost";
$Username = "root";
$Password = "";
$Database = "PharmacyX_DB";

//connection with Database
$Connection = mysqli_connect($Server, $Username, $Password, $Database);

//Check Connection
if(!$Connection){
    die("Connection Failed: ".mysqli_connect_error());
}

?>