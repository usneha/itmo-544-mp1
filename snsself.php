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
	$phonee = '13123950502';
	}


// creating sns topic
$snsclient = new Aws\Sns\SnsClient([
	'version' => 'latest',
	'region' => 'us-east-1'
]);
$result = $snsclient->createTopic(array(
    // Name is required
    'Name' => 'CloudTrigger',
));
#echo "Topic Created";
echo $result;
$topicArn = $result['TopicArn'];
// set topic attributes to set a diaply name for topic
$result = $snsclient->setTopicAttributes([
	'AttributeName' => 'DisplayName',
	'AttributeValue' => 'CloudTrigger',
	'TopicArn' => $topicArn,
]);
echo "set display name to topic";
for($i=0;$i<120;$i++){
 echo "=";
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
	'Message' => 'Check AWS',
	'Subject' => 'Cloud Trigger',
	'TopicArn' => $topicArn,
]); 


?>

