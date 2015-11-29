<html>
<head><title>Gallery</title>


<link rel="stylesheet" href="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/css/magnific-popup.css" />

<style>
.magnific-gallery{
	float: relative;
        width:75px;
        border: 1px;
	color: #545454;
}

#body{
	color:#545454;
}


/*.gallery-item{
	
  enabled: false, // set to true to enable gallery

  preload: [0,2], // read about this option in next Lazy-loading section

  navigateByImgClick: true,

  arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>', // markup of an arrow button

  tPrev: 'Previous (Left arrow key)', // title for left button
  tNext: 'Next (Right arrow key)', // title for right button
  tCounter: '<span class="mfp-counter">%curr% of %total%</span>' // markup of counter
}*/

</style>
<script>
$('.gallery-item').magnificPopup({
  type: 'image',
  gallery:{
    enabled:true
  }
});

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.min.js"></script>


</head>
<body>
<h3> Welcome to your Gallery! </h3>
<div class="gallery-item">
<?php
session_start();

$regvalue = $_GET['regvalue'];


#$email = $_POST["useremail"];
#echo $email;
require 'vendor/autoload.php';


#create RDSclient using the us-west-2 
#$rds = new Aws\Rds\RdsClient([
 #   'version' => 'latest',
  #  'region'  => 'us-east-2'
		#]);
use Aws\Rds\RdsClient;
if($regvalue==1){

	echo "var is 1";
	print "var is 1";
	//use Aws\Rds\RdsClient;
	$client = RdsClient::factory(array(
		'version' => 'latest',
		'region'  => 'us-west-2'
	));

	$result = $client->describeDBInstances(array(
    		'DBInstanceIdentifier' => 'usneha',
	));

	$endpoint = "";


#fetch the DB instance
#$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'usneha']);


#get the end point to the instance
	$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
   # print "============\n". $endpoint . "================";

//echo "endpoint is available";
//echo "Inside Gallery code";

	$link = mysqli_connect($endpoint,"username","password","usnehadb",3306);



/* check connection */
	if (mysqli_connect_errno()) {
    		printf("Connect failed: %s\n", mysqli_connect_error());
    		exit();
	}
//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
	$link->real_query("SELECT * FROM items");
//$link->real_query("SELECT * FROM items");
	$res = $link->use_result();
	echo "Result set order...\n";

/*
while($row = $res->fetch_assoc()){
	$image = new Imagick($row['RawS3Url']);
	$image->thumbnailImage(80,0);
	
	print "before going to s3final.php";

	include 's3final.php';

	print "out of s3final.php";
}
*/



	while ($row = $res->fetch_assoc()) {
#   		 echo "<img src =\" " . $row['RawS3Url'] . "\" /><img src =\"" .$row['FinishedS3Url'] . "\"/>";
                 echo "<img src =\" " . $row['RawS3Url'] . "\" />";
 
 //  echo $row['RawS3Url'];
#echo $row['id'] . "Email: " . $row['email'];
#$image = new Imagick($row['RawS3Url']);
#$image->thumbnailImage(80,0);


	}
	$link->close();
	} 
else {

	//	use Aws\Rds\RdsClient;
	$client = RdsClient::factory(array(
		'version' => 'latest',
		'region'  => 'us-west-2'
	));
	$result = $client->describeDBInstances(array(
    		'DBInstanceIdentifier' => 'usneha',
	));
	$endpoint = "";
#fetch the DB instance
#$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'usneha']);
#get the end point to the instance
	$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
   # print "============\n". $endpoint . "================";
//echo "endpoint is available";
//echo "Inside Gallery code";
	$link = mysqli_connect($endpoint,"username","password","usnehadb",3306);
/* check connection */
	if (mysqli_connect_errno()) {
    		printf("Connect failed: %s\n", mysqli_connect_error());
    		exit();
	}
//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
	$link->real_query("SELECT * FROM items");
//$link->real_query("SELECT * FROM items");
	$res = $link->use_result();
	echo "Result set order...\n";
/*
while($row = $res->fetch_assoc()){
	$image = new Imagick($row['RawS3Url']);
	$image->thumbnailImage(80,0);
	
	print "before going to s3final.php";
	include 's3final.php';
	print "out of s3final.php";
}
*/
	while ($row = $res->fetch_assoc()) {
              echo "<img src =\" " . $row['RawS3Url'] . "\" /><img src =\"" .$row['FinalS3Url'] . "\"/>";

   	
#	 echo "<img src =\" " . $row['RawS3Url'] . "\" />";
  //  echo $row['RawS3Url'];
#echo $row['id'] . "Email: " . $row['email'];
#$image = new Imagick($row['RawS3Url']);
#$image->thumbnailImage(80,0);
#$thumbnail=$image->getImageBlob();
#echo $thumbnail;
#<img src=<?php $thumbnail
#echo $image;
	}
	$link->close();


}
?>
</div>
</body>
</html>
