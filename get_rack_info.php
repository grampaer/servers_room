<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Include config file
    require_once "config.php";
     
    // Define variables and initialize with empty values
    $rack_info = array();
    $row_id = $num_id = $name = $description = $height = "";
    $room_id_err = $row_id_err = $num_id_err =  $name_err = $description_err = $height_err = "";
 
    $room_id        = trim($_POST["room_id"]);
    $row_id         = trim($_POST["row_id"]);
    $num_id         = trim($_POST["num_id"]);
         
    // Prepare a select statement
    $sql = "SELECT id, name, description, height FROM racks where room_id = ? and row_id = ? and num_id = ? ";

    if($stmt = mysqli_prepare($link, $sql)){

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "iii", $room_id, $row_id, $num_id);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){

            // Store result
            mysqli_stmt_store_result($stmt);
    
            if(mysqli_stmt_num_rows($stmt) > 0){                    
                            
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $name, $description, $height);
                while(mysqli_stmt_fetch($stmt)){
                    $rack_info[$id] = array();
                    $rack_info[$id]['id'] = $id;
                    $rack_info[$id]['name'] = $name;
                    $rack_info[$id]['description'] = $description;
                    $rack_info[$id]['height'] = $height;
                    $rack_info[$id]['servers_info'] = array();
    
                    // Prepare a select statement
                    $sql_servers = "SELECT id, name, description, height, slot_id FROM servers where id_rack = ? ";

                    if($stmt_servers = mysqli_prepare($link, $sql_servers)){

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt_servers, "i", $id);

                        // Attempt to execute the prepared statement
                        if(mysqli_stmt_execute($stmt_servers)){

                            // Store result
                            mysqli_stmt_store_result($stmt_servers);
                            
                            if(mysqli_stmt_num_rows($stmt_servers) > 0){                    
                                            
                                // Bind result variables
                                mysqli_stmt_bind_result($stmt_servers, $id_server, $name_server, $description_server, $height_server, $slot_id_server);
                                
                                while(mysqli_stmt_fetch($stmt_servers)){
                                    $rack_info[$id]['last'] = $id_server;
                                    $rack_info[$id]['servers_info'][$slot_id_server] = array();
                                    $rack_info[$id]['servers_info'][$slot_id_server]['id']           = $id_server;
                                    $rack_info[$id]['servers_info'][$slot_id_server]['name']         = $name_server;
                                    $rack_info[$id]['servers_info'][$slot_id_server]['description']  = $description_server;
                                    $rack_info[$id]['servers_info'][$slot_id_server]['height']       = $height_server;
                                    for ($i = 1; $i < $height_server; $i++) {
                                        $rack_info[$id]['servers_info'][$slot_id_server+$i] = $rack_info[$id]['servers_info'][$slot_id_server];
                                    }
                                }
                            }
                        }
                    }
                }
            }                
        }
    }
}

echo json_encode($rack_info);
?>
