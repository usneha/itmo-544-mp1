<?php
session_start();
?>

<html>
<head>
<title> Welcome to Image Uploading page! </title>
<style>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
</style>

</head>
<body>

<form enctype="multipart/form-data" action="submit.php" method="post" class="form-horizontal" role="form">
	
	<div class="form-group">
		User Name: <input type="text" name="username" value="usneha"><br>
		E-mail: <input type="email" name="email" class-"form-control"  value="****@***.iit.edu"><br>
		Phone : <input type="text" name="phone" value="312-000-0000"><br>
	</div>

	<input type="hidden" name="MAX_FILE_SIZE" value="3000000"><br>
	<div class="form-group">
	Upload File : <input type="file" name="userfile">
	</div>
	<div>
	<input type="submit" class="btn btn-default">
	</div>
</form>

</body>
</html> 

