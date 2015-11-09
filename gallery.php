<html>
<head><title>Gallery</title>


<link rel="stylesheet" href="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/css/magnific-popup.css" />

<style>
.magnific-gallery img{
	float: relative;
        width:75px;
        border: 1px;
}

#body{
	color:#545454;
}


</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.js"></script>

<script src="https://raw.githubusercontent.com/usneha/itmo-544-mp1/master/js/jquery.magnific-popup.min.js"></script>




</head>
<body>

<?php
session_start();
#$email = $_POST["useremail"];
#echo $email;
require 'vendor/autoload.php';


#create RDSclient using the us-west-2 
#$rds = new Aws\Rds\RdsClient([
 #   'version' => 'latest',
  #  'region'  => 'us-west-2'
#]);

use Aws\Rds\RdsClient;
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
    print "============\n". $endpoint . "================";

echo "endpoint is available";
echo "Inside Gallery code";

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
while ($row = $res->fetch_assoc()) {
    echo "<img class="magnific-gallery" src =\" " . $row['RawS3Url'] . "\" /><img src =\"" .$row['FinishedS3Url'] . "\"/>";
  //  echo $row['RawS3Url'];
#echo $row['id'] . "Email: " . $row['email'];
}
$link->close();
?>
</body>
</html>
