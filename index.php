<?php
session_start();
?>

<html>
<body>

<form enctype="multipart/form-data" action="submit.php" method="post">
	
	// including form validation

	User Name: <input type="text" name="username" value="usneha"><br>
	E-mail: <input type="text" name="email" value="****@***.iit.edu"><br>
	Phone : <input type="text" name="phone" value="312-000-0000"><br>
	
	<input type="hidden" name="MAX_FILE_SIZE" value="3000000"><br>
	
	Upload File : <input type="file" name="userfile">

	<input type="submit">

</form>

</body>
</html> 

