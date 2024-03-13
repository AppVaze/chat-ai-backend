<?php
$HOST = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DB_NAME = 'chat_ai';
$conn = mysqli_connect($HOST ,$USERNAME , $PASSWORD , $DB_NAME);
if(!$conn){
    mysqli_connect_errno();
}

?>