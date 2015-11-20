#! /bin/bash

#aws Set-DefaultAWSRegion -Region us-east-2

# creating a topic
ARN=(`aws sns create-topic --name s3upload`)

echo "Topic ARN is $ARN"

aws sns set-topic-attributes --topic-arn $ARN --attribute-name DisplayName --attribute-value s3upload

# subscibing to the topic created above

aws sns subscribe --topic-arn $ARN --protocol sms --notification-endpoint 13123950502

