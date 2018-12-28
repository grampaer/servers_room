<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Include config file
    require_once "config.php";
     
    // Define variables and initialize with empty values
    $status = false;
    $id = $key = $val;
    $id_err = $key_err = $val_err = "";
 
    $id        = trim($_POST["id"]);
    $field     = trim($_POST["field"]);
    $val       = trim($_POST["val"]);
     
    // Prepare a select statement
    $sql_servers = "UPDATE servers SET ".$field." = \"".$val."\" where id = ".$id;

    if($stmt_servers = mysqli_prepare($link, $sql_servers)){

        // Attempt to execute the prepared statement
        $status = mysqli_stmt_execute($stmt_servers);
    }
}

echo json_encode($status);
?>