<?php
	session_start();
	require 'vendor/autoload.php';
	#echo "Inside submit  page";
	if(!empty($_POST)){
	echo $_POST['email'];
	echo $_POST['phone'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	}
	else {
		echo "Subscribing for:13123950502";
	$phone = '13123950502';
	}


// creating sns topic
$snsclient = new Aws\Sns\SnsClient([
	'version' => 'latest',
	'region' => 'us-east-1'
]);
#$result = $snsclient->createTopic(array(
    // Name is required
 #   'Name' => 'CloudTrigger',
#));
#echo "Topic Created";
#echo $result;
#$topicArn = $result['TopicArn'];
// set topic attributes to set a diaply name for topic
#$result = $snsclient->setTopicAttributes([
#	'AttributeName' => 'DisplayName',
#	'AttributeValue' => 'CloudTrigger',
#	'TopicArn' => $topicArn,
#]);
echo "set display name to topic";
for($i=0;$i<120;$i++){
 echo "=";
}
// returns the subscription arn
	$result = $snsclient->subscribe([
    		// TopicArn is required
    		'TopicArn' => 'arn:aws:sns:us-east-1:311615471368:snsself',
    		// Protocol is required
    		'Protocol' => 'sms',
    		'Endpoint' => '13123950502',
	]);
	
	echo "Subcribed to topic for 3123950502";
	echo $result;
for($i=0;$i<120;$i++){
 echo "=";
}
$result = $snsclient->publish([
	'Message' => 'Check AWS',
	'Subject' => 'Cloud Trigger',
	'TopicArn' => 'arn:aws:sns:us-east-1:311615471368:snsself',
]); 


?>

