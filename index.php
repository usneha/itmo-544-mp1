<?php
session_start();

?>
<!DOCTYPE html>
 
<meta charset="UTF-8">
<html>
<head>
<title> Welcome to Image Uploading page! </title>
<style type="text/css">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	
	#body{
	color:#FFFFCC;
	
	}

	.clearer{
	
	clear: both;
	height: 100px;
	width: 100$;
	margin: 0px 0px auto 0px;
	
	}

	.main-menu{
	list-style-type: none;
	margin: 0;
	padding: 0;
	background-color: #310c0c;
	overflow:hidden;
	}

	.main-menu li{
	float: left;
	}

	.main-menu li a{
	padding: 10px 20px;
	display: block;
	color:white; 
	}
	
	.main-menu li a:hover{

	background-color: #756251;

	}
</style>


</head>
<body>

<div class="clearer">
	<ul class="main-menu">

		<li><a href="index.php">Home</a></li>
		<li><a href='gallery.php?regvalue=1'>Gallery</a></li>
		<li><a href="introspection.php">Create Db Dump!</a></li>
		
	</ul>

</div>

<!--
<form method="get" action="gallery.php">
	
	<a href="gallery.php"/>
	<input type="text" name="varname" value=""/>

</form>

-->
<!--
 $regvalue=1;
 echo "<a href='gallery.php?regvalue=1'>Gallery</a>";
-->


<!--<a href="introspection.php">Create DB Dump!</a>
-->
<form method="post" action="index.php">
	
	<h1> Click here if you wish you go into read only mode!</h1>
	<input type="text" name="confirm1" value="yes"><br>
	<input type="submit" class="btn btn-default" name"confirm">

</form>

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
	
	<?php
		if(empty($_POST)){
	
		echo "You cannot upload pics! You are in read only mode! ";
	
	}else{
	
		echo "<input type="submit" class="btn btn-default">";
	
	}
	?>
</form>

</body>
</html> 
