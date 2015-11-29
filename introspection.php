<!DOCTYPE html>
 <meta charset="UTF-8"> 
<html>
	<head><title>This is the introspection page</title></head>

<body>

	<h3>Creating db dump in S3!!!</h3>
	<a href="index.php">Home Page</a>
</body>


<?php
require 'vendor/autoload.php';
//Creating a dump of the RDS database instance

$db = 'usnehadb';
$username = 'username';
$password = 'password';


$dbclient = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
	$result = $dbclient->describeDBInstances(['DBInstanceIdentifier' => 'usneha']);
	$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
	$link = mysqli_connect($endpoint,"username","password","usnehadb") or die("Error " . mysqli_error($link));

// making a folder for storing backup
mkdir("/tmp/dbDump");
// path for backup folder
$dumpPath = '/tmp/dbDump/';
$fname = uniqid("dbdump", false);
$append = $fname . '.' . sql;
$finalPath = $dumpPath . $fname . '.' . sql;

// found this line in stackoverflow
$sql="mysqldump --user=$username --password=$password --host=$endpoint $db > $finalPath";

exec($sql);
$bucketname = uniqid("dbdump", false);
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucketname,
]);

$key = $fname . '.' . sql;
# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucketname,
   'Key' => $key,
'SourceFile' => $finalPath,
]);


mysql_close($link);
	echo "Create db dump in s3!";
?>
</html>
