<?php
header('Content-Type: application/json');

require_once("../php/cbsd.php");

$cmd='';
$status = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$path=realpath('').'/media/';
	if(isset($_POST['uplace'])){
		$res=strpos($_POST['uplace'],'jailscontainers');
		if($res!==false){
			$path=$clonos->media_import;
			$cmd='import';
		}
		$res=strpos($_POST['uplace'],'imported');
		if($res!==false){
			$path=$clonos->media_import;
			$cmd='import';
		}
	}

	// https://www.php.net/manual/en/features.file-upload.php
	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])) {
		echo json_encode( array( 'status'=>'Upload Fail: An error occurred!'));
		exit;
	}

	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		$basename = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_BASENAME));

		if (move_uploaded_file($_FILES['file']['tmp_name'], $path.$basename)){
			$status = 'ok';	//'Successfully uploaded!';
			if($cmd=='import'){
				$res=CBSD::run('task owner=%s mode=new /usr/local/bin/cbsd jimport jname=%s inter=0', [$clonos->getUserName(), $path.$basename]);
			}
		} else {
			$status = 'Upload Fail: Unknown error occurred!';
		}
	}
}

if($status!='ok'){
	echo json_encode(array('status' => $status));
	exit;
}
return; # TODO ?!

$valid_exts = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'); // valid extensions
$max_size = 30000 * 1024; // max file size in bytes

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
	for($i=0;$i<count($_FILES['file']['tmp_name']);$i++){
		$path="/media/";

		if(is_uploaded_file($_FILES['file']['tmp_name'][$i]) ){
			// get uploaded file extension
			$ext = strtolower(pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION));
			// looking for format and size validity
			if (in_array($ext, $valid_exts) AND $_FILES['file']['size'][$i] < $max_size){
				// unique file path
				$uid = uniqid();
				$date = date('Y-m-d-H-i-s');
				$path = $path ."image_" .$date. '_' . $uid . "." .$ext;

				$filename = "image_" . $date . "_" .$uid . "." . $ext;
				//$this->createthumb($i,$filename);

				// move uploaded file from temp to uploads directory
				if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $path)){
					$status = 'ok';	//'Successfully uploaded!';
					//perform sql updates here
				} else {
					$status = 'Upload Fail: Unknown error occurred!';
				}
			} else {
				$status = 'Upload Fail: Unsupported file format or It is too large to upload!';
			}
		} else {
			$status = 'Upload Fail: File not uploaded!';
		}
	}
} else {
	$status = 'Bad request!';
}

echo json_encode(array('status' => $status));