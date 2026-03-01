<?php
$db_server="localhost";
$db_user="root";
$db_password="";
$databaseName="Socialdb";

$conn=mysqli_connect($db_server, $db_user,$db_password);

if(!$conn){
    die("connection failed".mysqli_connect_error($conn));

};

    


?>