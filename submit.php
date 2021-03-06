<?php
	session_start();
	require 'vendor/autoload.php';

	echo "Inside submit  page";

	if(!empty($_POST)){
		echo $_POST['email'];
	echo $_POST['phone'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	}
	else {

		echo "No data has been posted";
	}
	print_r ($_POST);

	echo $_FILES['userfile'];

	print_r ($_FILES);

	if (isset ($_FILES['userfile'])){
		$uploaddir = '/tmp/';
		$uploadfile = $uploaddir. basename($_FILES['userfile']['name']);
		$fname = $_FILES['userfile']['name'];
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		print "File is valid, and was successfully uploaded.\n";
	} else {
	print "Couldnt upload file\n";
	}

	print 'Here is some more debugging info:';
print_r($_FILES);

//<!-- use Aws\S3\S3Client; -->
	$s3=new Aws\S3\S3Client([
		'version' => 'latest',
   		 'region'  => 'us-west-2'
	]);
	$bucket = uniqid("usnehas3", false);
	print "Creating bucket named {$bucket}\n";
	$result = $s3->createBucket([
    		'ACL' => 'public-read',
    		'Bucket' => $bucket
	]);
print 'outside the create bucket command';

# waiting for the s3 bucket to be available
	$result = $s3->waitUntil('BucketExists',array('Bucket' => $bucket));

	echo "bucket creation done";

	$result = $s3->putObject([
    		'ACL' => 'public-read',
    		'Bucket' => $bucket,
   		'Key' => "uploads".$uploadfile,
	//   'SourceFile' => "uploads".$uploadfile,
		'ContentType' => $_FILES['userfile']['type'],
    		'Body'   => fopen($uploadfile, 'r+')
	]);  

	$url = $result['ObjectURL'];


	echo $url;
	echo "s3 file uploaded";


// reference: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putbucketlifecycleconfiguration

$result = $s3->putBucketLifecycleConfiguration([
    'Bucket' => $bucket, // REQUIRED
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




//--------------------------------------------------------------------------------------------------------------


// trying framed image .... yet to implement thumbnail
// reference: http://php.net/manual/en/imagick.writeimage.php
// reference: stackoverflow
	$imgpath = new Imagick($uploadfile);
	$imgpath->frameImage('#a00000',20,20,5,5);
	mkdir("/tmp/Image");
$ext = end(explode('.', $fname));

echo "file type is $ext";
$path = '/tmp/Image/';
$imageid = uniqid("Id");
// concatenating name and type
$imglocation = $imageid . '.' . $ext;
$finalImgPath = $path . $imglocation;
echo $ImgPath;

$imgpath->writeImage($finalImgPath); 

//creating bucket to upload framed image
$frames3bucket = uniqid("frameimage",false);
echo $frames3bucket;

$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $frames3bucket,
]);
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $frames3bucket,
   'Key' => "flipped".$imglocation,
'SourceFile' => $finalImgPath,
]);

$finishedimgurl=$result['ObjectURL'];

echo "processed image uploaded to s3";
// ------------------------------------------------------------------------------------------------------------------

	$rds = new Aws\Rds\RdsClient([
    		'version' => 'latest',
    		'region'  => 'us-west-2'
	]);
	$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'usneha']);

	echo "fetching result from describe db instance";

//print_r($result);

	$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    		print "============\n". $endpoint . "================";
	echo "endpoint is available";

	$link = mysqli_connect($endpoint,"username","password","usnehadb",3306) or die("Error " . mysqli_error());

	print_r($link);
	if (mysqli_connect_errno()) {
    		printf("Connect failed: %s\n", mysqli_connect_error());
    	exit();
	}

	echo "Connected to RDS";
# prepared statement to insert data into items of usnehadb
	$sql_insert = "INSERT INTO items (UName,Email,Phone,RawS3Url,FinalS3Url,JpgFileName,status,Ifsubscribed) VALUES (?,?,?,?,?,?,?,?)";

	if (!($stmt = $link->prepare($sql_insert))) {
    		echo "Prepare failed: (" . $link->errno . ") " . $link->error;
	}
	else
	{
		echo "No error with prepared statement";
	}
	$uname = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$s3rawurl = $url; 

$s3finishedurl = $finishedimgurl;
$filename = basename($_FILES['userfile']['name']);
$status =0;
$ifsubscribed=0;
	$stmt->bind_param("ssssssii",$uname,$email,$phone,$s3rawurl,$s3finishedurl,$filename,$status,$ifsubscribed);
	if (!$stmt->execute()) {
    		print "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	printf("%d Row inserted.\n", $stmt->affected_rows);
	$stmt->close();
	$sql1 = "SELECT ID, JpgFileName, RawS3URL FROM items ";
	$output = mysqli_query($link, $sql1);
	$imgPath = array();
	print "Result set order...\n";
	if (mysqli_num_rows($output) > 0) {
    // output data of each row
    		while($row = mysqli_fetch_assoc($output)) {
        //passing image path to an imgPath array
        		$imgPath[$row["JpgFileName"]] = $row["RawS3URL"];
        		echo "id: " . $row["ID"]."- RawS3URL" . $row["RawS3URL"]. "<br>";
    		}

	}	 
	else {
    		echo "No output!";
	}

/*
// creating sns topic

$snsclient = new Aws\Sns\SnsClient([
	'version' => 'latest',
	'region' => 'us-east-1'
]);

$result = $snsclient->createTopic(array(
    // Name is required
    'Name' => 's3upload',
));

echo "Topic Created";
echo $result;

$topicArn = $result['TopicArn'];

// set topic attributes to set a diaply name for topic

$result = $snsclient->setTopicAttributes([
	'AttributeName' => 'DisplayName',
	'AttributeValue' => 's3upload',
	'TopicArn' => $topicArn,
]);

echo "set display name to topic";

for($i=0;$i<120;$i++){
 echo $i;
}


// returns the subscription arn
	$result = $snsclient->subscribe([
    		// TopicArn is required
    		'TopicArn' => $topicArn,
    		// Protocol is required
    		'Protocol' => 'sms',
    		'Endpoint' => $phone,
	]);
	
	echo "Subcribed to topic for $phone";
	echo $result;
for($i=0;$i<120;$i++){
 echo "=";
}

$result = $snsclient->publish([
	'Message' => 'Image uploaded to S3',
	'Subject' => 'S3 Image Upload',
	'TopicArn' => $topicArn,
]); 

*/
$link->close();


function redirect()
{
   echo "inside redirect";
   header('Location: gallery.php', true, 303);
   die();
}
redirect();
}
else
{
echo "Invalid file";
}

?>

