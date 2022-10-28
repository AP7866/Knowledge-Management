<?php 
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: *');
	header('Access-Control-Allow-Methods: GET, POST');
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if( isset($_POST["content"]) && isset($_POST["new_title"]) && isset($_POST["old_title"]) ){
			$data = $_POST['content'];
			$name = "docs/" . $_POST['new_title'] . ".txt";
			
			if( file_exists($name) ){
				if( $_POST["old_title"] == "" ){
					echo json_encode(array("message"=>"title exists"));
					exit;
				}
			}
			
			if( $_POST["old_title"] != "" ){
				rename("docs/" . $_POST['old_title'] . ".txt", $name);
			}
			
			$myfile = fopen($name, "w") or die("Unable to open file!");
			fwrite($myfile, $data);
			fclose($myfile);
			
			echo json_encode(array("message"=>"OK"));
			exit;
		}
		
		else if( isset($_POST["name"]) ){
			$name = $_POST["name"];
			$file = "docs/" . $name . ".txt";
			if( file_exists($file) ){
				echo json_encode(array("message"=>unlink($file)));
			}
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		if( isset($_GET["name"]) ){
			$filename = "docs/" . $_GET["name"] . ".txt";
			$myfile = fopen($filename, "r") or die("Unable to open file!");
			echo fread($myfile, filesize($filename));
			fclose($myfile);
		}else{
			$mydir = 'docs'; 
			$myfiles = array_diff(scandir($mydir), array('.', '..'));
			$resp = array();
			foreach( array_values($myfiles) as $myfile ){
				$filename = "docs/" . $myfile;
				$tempObj = array();
				$tempObj["name"] = $myfile;
				$tempObj["date_created"] = date("F d, Y H:i:s", filectime($filename));
				$tempObj["date_modified"] = date("F d, Y H:i:s", filemtime($filename));
				array_push($resp, $tempObj);
			}
			echo json_encode(array("files"=>$resp));
		}
	}
?>
