<?php
session_start();
require 'vendor/autoload.php';

#create RDSclient using the us-west-2 
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

#fetch the DB instance
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'usneha']);


#get the end point to the instance
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
  //  print "============\n". $endpoint . "================";
//echo "endpoint is available";
//echo "Inside Gallery code";
$link = mysqli_connect($endpoint,"username","password","usnehadb",3306);

?>

<!DOCTYPE html>
<html><head>
<!-- adding jquery gallery as per graduate requirement -->

<style>

.magnific-gallery li{
	float: relative;
	height: 80px;
}

.magnific-gallery img{
	width:75px;
	border: 1px;
}

<link rel="stylesheet" href="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/css/magnific-popup.css" />
</style>


</head>
<body>
<h3> Welcome to your Gallery! </h3>
<?php 
if (!$link)
{
die("connection failed". mysqli_connect_error());
}

else
{
if(isset($_POST['email'])){
$useremail = $_POST['email'];
$sqlstat= "SELECT * FROM items WHERE Email='$useremail'";
}
else
{
$sqlstat= "SELECT * FROM items";
}

$output = mysqli_query($link, $sqlstat);

$imgPath = array();
print "Result set order...\n";

if (mysqli_num_rows($output) > 0) {
    while($row = mysqli_fetch_assoc($output)) {
        
        $imgPath[$row["JpgFileName"]] = $row["RawS3URL"];
        echo "id: " . $row["ID"]."- RawS3URL" . $row["RawS3URL"]. "<br>";
    }
} 
else {
    echo "no rows in the output";
}

$link->close();
}
?>
<ul class="magnific-gallery">
  <?php foreach ($imgPath as $key => $value) {
  ?>
  <li>	

  <img src="<?php echo $value ?>"></img>

  </li>
  <?php }?>
</ul>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.min.js"></script>

</html>
