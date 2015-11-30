<?php

session_start();
?>

<!DOCTYPE html>
 <meta charset="UTF-8"> 
<html>
	<head><title>This is the introspection page</title>

<style type="text/css">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        #body{

        color: #FFFFCC;

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

// reference: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putbucketlifecycleconfiguration

$result = $s3->putBucketLifecycleConfiguration([
    'Bucket' => $bucketname, // REQUIRED
    'LifecycleConfiguration' => [
        'Rules' => [ // REQUIRED
            [
                'Expiration' => [
           
                    'Days' => 1,
                ],
                
                'NoncurrentVersionExpiration' => [
                    'NoncurrentDays' => 1,
                ],
                
                'Prefix' => ' ', // REQUIRED
                'Status' => 'Enabled', // REQUIRED
        
            ],
            // ...
        ],
    ],
]);



mysql_close($link);
	echo "Create db dump in s3!";
?>
</html>
