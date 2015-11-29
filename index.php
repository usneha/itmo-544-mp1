<?php
session_start();
$regvalue=1;
$_SESSION['var']=$regvalue;
?>

<!DOCTYPE html>
 <meta charset="UTF-8">

<html>
<head>
<title> Welcome to Image Uploading page! </title>
<style>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
</style>

</head>
<body>

<form method="get" action="gallery.php">
	
	<a href="gallery.php"/>
	<input type="text" name="varname" value=""/>

</form>

 <a href="introspection.php">Create DB Dump!</a>


<form enctype="multipart/form-data" action="submit.php" method="post" class="form-horizontal" role="form">
	
	<div class="form-group">
		User Name: <input type="text" name="username" value="usneha"><br>
		E-mail: <input type="email" name="email" class-"form-control"  value=""><br>
		<h4>Phone Number format: 13334445555 </h4>
		Phone : <input type="text" name="phone" value="13123950502"><br>
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

