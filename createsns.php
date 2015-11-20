<?php

require 'vendor/autoload.php';
use Aws\Sns\SnsClient;

$snsclient = SnsClient::factory(array(
'region' => 'us-east-1'
));

$snstopicArn = $client->createTopic(array(
    // Name is required
    'Name' => 'string',
));

echo $snstopicArn;


$subscriptionArn = $client->subscribe(array(
    // TopicArn is required
    'TopicArn' => $snstopicArn,
    // Protocol is required
    'Protocol' => 'sms',
    'Endpoint' => '1-312-395-0502',
));

echo $subscriptionArn;

//$result = $client->confirmSubscription(array(
    // TopicArn is required
  //  'TopicArn' => $snstopicArn,
    // Token is required
    //'Token' => 'string',
    //'AuthenticateOnUnsubscribe' => 'string',
//));

//echo $result;

?>
