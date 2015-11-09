<?php
	session_start();
	require 'vendor/autoload.php';

	echo "Inside submit  page";

if(!empty($_POST)){
echo $_POST['email'];
echo $_POST['phone'];
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
$s3finishedurl = "none";
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

