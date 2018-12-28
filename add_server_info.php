<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Include config file
    require_once "config.php";
     
    // Define variables and initialize with empty values
    $status = false;
    $rack_id = $row_id = $num_id = $name = $description = $height = "";
    $room_id_err = $row_id_err = $num_id_err =  $name_err = $description_err = $height_err = "";
 
    $room_id        = trim($_POST["room_id"]);
    $row_id         = trim($_POST["row_id"]);
    $num_id         = trim($_POST["num_id"]);
    $slot_id        = trim($_POST["slot_id"]);
    $name           = trim($_POST["name"]);
    $descritpion    = trim($_POST["descritpion"]);
    $height         = trim($_POST["height"]);
    $brand          = trim($_POST["brand"]);
    $model          = trim($_POST["model"]);
    $serial_num     = trim($_POST["serial_num"]);
     
    // Prepare a select statement
    $sql = "SELECT id FROM racks where room_id = ? and row_id = ? and num_id = ? ";

    if($stmt = mysqli_prepare($link, $sql)){

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "iii", $room_id, $row_id, $num_id);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){

            // Store result
            mysqli_stmt_store_result($stmt);
    
            if(mysqli_stmt_num_rows($stmt) == 1){                    
                            
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $rack_id);
                if (mysqli_stmt_fetch($stmt)){
                    
                    // Prepare a select statement
                    $sql_servers = "INSERT INTO servers (slot_id , id_rack , name , height , description, brand, model, serial_num ) VALUES (? , ? , ? , ? , ? , ? , ? , ?)";

                    if($stmt_servers = mysqli_prepare($link, $sql_servers)){

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt_servers, "iisissss", $slot_id, $rack_id, $name, $height, $descritpion, $brand, $mdoel, $serial_num);

                        // Attempt to execute the prepared statement
                        $status = mysqli_stmt_execute($stmt_servers);
                    }
                }
            }                
        }
    }
}

echo json_encode($status);
?>