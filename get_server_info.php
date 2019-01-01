<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Include config file
    require_once "config.php";
     
    // Define variables and initialize with empty values
    $info = array();
    $row_id = $num_id = $name = $description = $height = "";
    $err = "";
 
    $room_id        = trim($_POST["room_id"]);
    $row_id         = trim($_POST["row_id"]);
    $num_id         = trim($_POST["num_id"]);
    $slot_id        = trim($_POST["slot_id"]);
     
    $filename = "./".$room_id."_".$row_id."_".$num_id."_".$slot_id."_server_info.txt";
    if (file_exists($filename)) {
		$myfile = fopen($filename, "r") or die("Unable to open file!");
		while (!feof($myfile)) {
			$tmp = explode(":",fgets($myfile));
			$info[$tmp[0]] = $tmp[1];
		}
		fclose($myfile);
	}
	else {
			$info['nb_info'] = 0;
	}
}
echo json_encode($info);
?>
