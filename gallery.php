<html>
<head><title>Gallery</title>
</head>
<body>

<?php
session_start();
$email = $_POST["useremail"];
echo $email;
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
    print "============\n". $endpoint . "================";

echo "endpoint is available";
echo "Inside Gallery code";

$link = mysqli_connect($endpoint,"username","password","usnehadb",3306);

$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'usneha',
));


/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
$link->real_query("SELECT * FROM items WHERE email = '$email'");
//$link->real_query("SELECT * FROM items");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo "<img src =\" " . $row['s3rawurl'] . "\" /><img src =\"" .$row['s3finishedurl'] . "\"/>";
echo $row['id'] . "Email: " . $row['email'];
}
$link->close();
?>
</body>
</html>
