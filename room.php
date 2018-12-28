<?php

//Check secure
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../album/login.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$room_info = array();
$room_id = $row_id = $num_id = $name = $description = $height = "";
$room_id_err = $row_id_err = $num_id_err =  $name_err = $description_err = $height_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
        
    if ($_POST["action"] == "Create Rack") {
        
        if(empty(trim($_POST["name"]))){
          $name_err = "Please enter a name.";     
        } else{
            $name = trim($_POST["name"]);
        }

        if(empty(trim($_POST["height"]))){
          $height_err = "Please enter a height.";     
        } elseif(is_int(trim($_POST["height"]))) {
            $height_err = "Height must be an integer.";
        } else{
            $height = trim($_POST["height"]);
        }

        $description    = trim($_POST["description"]);
        $room_id        = 1;
        $row_id         = trim($_POST["row_id"]);
        $num_id         = trim($_POST["num_id"]);
 
        // Check input errors before inserting in database
        if(empty($name_err) && empty($description_err) && empty($height_err)) {

            // Prepare a select statement
            $sql = "INSERT INTO racks (room_id, row_id , num_id , name , height , description ) VALUES (1, ?, ?, ? , ? , ?)";

           if($stmt = mysqli_prepare($link, $sql)){
                
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "iisis", $row_id , $num_id, $name, $height , $description );
                            
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    $status = "Inserted.";

                } else{
                    $status = "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
    elseif ($_POST["action"] == "Create_Server") {

        if(empty(trim($_POST["name"]))){
          $name_err = "Please enter a name.";     
        } else{
            $name = trim($_POST["name"]);
        }

        if(empty(trim($_POST["height"]))){
          $height_err = "Please enter a height.";     
        } elseif(is_int(trim($_POST["height"]))) {
            $height_err = "Height must be an integer.";
        } else{
            $height = trim($_POST["height"]);
        }

        $description    = trim($_POST["description"]);
        $rack_id        = trim($_POST["rack_id"]);
 
        // Check input errors before inserting in database
        if(empty($name_err) && empty($description_err) && empty($height_err)) {

            // Prepare a select statement
            $sql = "INSERT INTO servers (rack_id, name , height , description , slot_id ) VALUES ( ?, ?, ? , ? , ?)";

           if($stmt = mysqli_prepare($link, $sql)){
                
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "isisi", $rack_id , $name, $height , $description, $slot_id );
                            
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    $status = "Inserted.";

                } else{
                    $status = "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

// Prepare a select statement
$sql = "SELECT id, name, description, room_id, row_id, num_id, height FROM racks";

if($stmt = mysqli_prepare($link, $sql)){

    // Bind variables to the prepared statement as parameters
    //mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){

        // Store result
        mysqli_stmt_store_result($stmt);
                
        if(mysqli_stmt_num_rows($stmt) > 0){                    
    
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $description, $room_id , $row_id, $num_id , $height);
            while(mysqli_stmt_fetch($stmt)){
                if (!is_array($room_info[$room_id.' - '.$row_id.' - '.$num_id])) {
                    $room_info[$room_id.' - '.$row_id.' - '.$num_id.' - '] = array();
                }
                $room_info[$room_id.' - '.$row_id.' - '.$num_id][$id] = array();
                $room_info[$room_id.' - '.$row_id.' - '.$num_id][$id]['name'] = $name;
                $room_info[$room_id.' - '.$row_id.' - '.$num_id][$id]['description'] = $description;
                $room_info[$room_id.' - '.$row_id.' - '.$num_id][$id]['height'] = $height;
            }
        }                
    }
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="room.css"> 
    <script src="jquery-3.3.1.min.js"></script>
    <script src="room.js"></script>
 </head>
<body>
    <div class="page-header">
        <h1>Welcome to room management page.</h1>
    </div>
    <div class="page-menu">
        <a href="../album/welcome.php" class="btn btn-default">welcome</a>
        <a href="../album/album.php" class="btn btn-default">Album</a>
        <a href="room.php" class="btn btn-default">room</a>
        <a href="../album/users_management.php" class="btn btn-default">Users</a>
    </div>
    
    <div id="curseur" class="infobulle"></div>

    <div class="page-main">
        <div class="page-room" style="width: 80%; float:left">
            <h2>Servers room</h2>
            <table id="room" border="1">
            <?php
                for ($i = 0; $i <= 3; $i++) {
                    echo '<tr height="100">';
                    for ($j = 0; $j <= 9; $j++) {
                        if (isset($room_info['1 - '.$i.' - '.$j])) {
                            echo '<td class="rack-present" width="80">';                            
                            
                            foreach ($room_info['1 - '.$i.' - '.$j] as $key => $value) {
                                    echo '<div class="rack-info">'.$value['name'].'</div>';
                                }
                        }
                        else {
                            echo '<td class="rack-empty" width="80">';
                            
                        }
                        echo '</td>';
                    }
                    echo '</tr>';
                }
            ?>
            </table>
        </div>
        
        <div class="page-rack" style="width: 20%; float:right">
            <h2>Rack</h2>
            <div id="page-rack-present" >
                <table id="table-rack-present" border="1">
                </table>         
            </div>
        </div>
    
        <div id="page-rack-create" >
            <h2>Form to create a rack</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <table>
						<tr>
							<td>Name:</td>
							<td><input type="text" name="name" value="<?php echo $name; ?>"></td>
							<td><span><?php echo $name_err; ?></span></td>
						</tr>
						<tr>
							<td>Description:</td>
							<td><input type="text" name="description" value="<?php echo $description; ?>"></td>
							<td><span><?php echo $description_err; ?></span></td>
						</tr>
						<tr>
							<td>height</td>
							<td><input type="text" name="height" value="<?php echo $height; ?>"></td>
							<td><span><?php echo $height_err; ?></span></td>
						</tr>
						<tr>
						<td><input id="rack-create-row_id" type="hidden" name="row_id" value="">
							<input id="rack-create-num_id" type="hidden" name="num_id" value=""></td>
						<td><input type="submit" class="btn btn-xs" name="action" value="Create Rack"></td>
						</tr>
                    </table>
                </form>
        </div>
        
        <div id="page-rack-info" >
        </div>

        <div id="page-server-create" >
            <h2>Form to add a server into rack</h2>
            <form>
				<table>
					<tr>
						<td>Name:</td> 
						<td><input id="server-name" type="text" name="name" value=""></td>
					</tr>
					<tr>
						<td>Description:</td>
						<td><input id="server-description" type="text" name="description" value=""></td>
					</tr>
					<tr>
						<td>height:</td>
						<td><input id="server-height" type="text" name="height" value=""></td>
					</tr>
					<tr>
						<td></td>
						<td><input id="submit-server-info" type="button" class="btn btn-xs" name="action" value="Add server"></td>
					</tr>
				</table>
            </form>
        </div>
        
        <div id="page-server-info" >
        </div>

    </div> 

    <div class="page-bottom">
        <p>
            <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        </p>
    <div>
</body>
</html>
