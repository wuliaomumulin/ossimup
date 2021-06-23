<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$key = ini_get('session.upload_progress.prefix').ini_get('session.upload_progress.name');

	var_dump($_POST);

	move_uploaded_file($_FILES['file1']['tmp_name'],'./upload/'.time().'.txt');
	move_uploaded_file($_FILES['file2']['tmp_name'],'./upload/'.(time()+1).'.txt');
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>grogress.php</title>
</head>
<body>
<form action="progress.php" method="post" enctype="multipart/form-data">
	
	<input type="hidden" name="<?php echo ini_get('session.upload_progress.name') ?>" value="test">
	<input type="file" name="file1">
	<input type="file" name="file2">
	<input type="text" name="age">
	<input type="submit" name="提交" />
</form>
</body>
</html>