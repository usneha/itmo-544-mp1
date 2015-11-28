#!/bin/bash

# running the clenaup script to ensure there are no "already exists" errors

chmod 700 ./itmo-544-mp1/cleanup.sh

./itmo-544-mp1/cleanup.sh

# decalring arrays for further use
declare -a InstanceArray
declare -a InstanceRunningArray


echo "creating instances"
aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --iam-instance-profile Name=phpdeveloperRole --associate-public-ip-address --user-data file://itmo-544-mp1-env/install-env.sh  


# waiting for instances to be available after 2 checks
sleep 30

# getting the instance ids of running instances
mapfile -t InstanceRunningArray < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=0 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 


# if there are no running instances, wait for some more time
if [ ${#InstanceRunninArray[@]} -eq 0 ];then

sleep 30

fi


# creating instance id array to use in the next steps for attaching instances to loadbalancer
mapfile -t InstanceArray < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=16 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 


# using the subnets and security groups entered by user in the launch script

echo "creating load balancer"
aws elb create-load-balancer --load-balancer-name usnehaLb --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5


echo "load balancer health check"
aws elb configure-health-check --load-balancer-name usnehaLb --health-check Target=HTTP:80/index.php,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3


echo "attaching instances to the load balancer"
aws elb register-instances-with-load-balancer --load-balancer-name usnehaLb --instances ${InstanceArray[@]} 


# getting the loadbalancer url to open in the browser
ELBURL=(`aws elb create-load-balancer --load-balancer-name usnehaLb --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets subnet-935e14f6 --security-groups sg-201e9f44  --output=text`);

firefox $ELBURL/index.php 


echo "creating launch configuration"
#aws autoscaling create-launch-configuration --launch-configuration-name usnehaLc --image-id $1 --key-name $4  --security-groups $5 --instance-type $3 --user-data file://itmo-544-mp1-env/install-env.sh --iam-instance-profile phpdeveloperRole

echo "creating auto scaling group"
#aws autoscaling create-auto-scaling-group --auto-scaling-group-name usnehaAsg --launch-configuration-name usnehaLc --load-balancer-names usnehaLb  --health-check-type ELB --min-size 1 --max-size 3 --desired-capacity 2 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier $6 

# creating an sns topic for being notified in case CPU exceeds or scales down

aws sns create-topic --name snsself 

topicArn=(`aws sns create-topic --name snsself`)

echo "Topic Arn is now available $topicArn"

# setting display name for the topic created
#aws sns set-topic-attributes --topic-arn $topicArn --attribute-name DisplayName --attribute-value snsself

#subscribing to the created topic
#aws sns subscribe --topic-arn $topicArn --protocol email --notification-endpoint usneha@hawk.iit.edu

# waiting for two minutes for the user to authenticate

#for var in {0..120}
#do
#echo -ne "."
 # sleep 1
#done

# publishing message
#aws sns publish --topic-arn $topicArn --message "Alarm Trigger"

#chmod 700 ./itmo-544-mp1/snsself.php
#php ./itmo-544-mp1/snsself.php


# creating cloud watch metrics
echo "Cloud metrics when CPU exceeds 30 percent"

#aws cloudwatch put-metric-alarm --alarm-name usneha-CPU30 --alarm-description "Alarm for checking  CPU gt 30 percent" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 30 --comparison-operator GreaterThanOrEqualToThreshold  --dimensions Name=AutoScalingGroupName,Value=usnehaAsg --evaluation-periods 2 --alarm-actions $topicArn --unit Percent


echo "Cloud metrics When CPU scales down to 10"

#aws cloudwatch put-metric-alarm --alarm-name usneha-CPU10 --alarm-description "Alarm for checking cpu lt 10 percent" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 10 --comparison-operator LessThanOrEqualToThreshold  --dimensions Name=AutoScalingGroupName,Value=usnehaAsg --evaluation-periods 2 --alarm-actions $topicArn --unit Percent


# creating db subnet group
aws rds create-db-subnet-group --db-subnet-group-name usnehasg --db-subnet-group-description usneha-subnetgrp --subnet-ids subnet-935e14f6 subnet-3a6b034d


echo "Creating DB instance"

result= aws rds create-db-instance --db-name usnehadb --db-instance-identifier usneha --allocated-storage 20 --db-instance-class db.t1.micro --engine MYSQL --master-username username --master-user-password password --vpc-security-group-ids $5 --availability-zone us-west-2a  --db-subnet-group-name usnehasg --publicly-accessible


# waiting for the DB instance to be available

aws rds wait db-instance-available --db-instance-identifier usneha
 
: '
echo "Creating DB instance read replica"

aws rds create-db-instance-read-replica --db-instance-identifier usnehadbreplica --source-db-instance-identifier usneha --db-instance-class db.t1.micro --availability-zone us-west-2a


# waiting for the read replica to be available

aws rds wait db-instance-available --db-instance-identifier usnehadbreplica

aws rds wait db-instance-available --db-instance-identifier usneha
'
# creating permission for running dbcreate.sh file
chmod 700 ./itmo-544-mp1/dbcreate.sh
./itmo-544-mp1/dbcreate.sh

echo "ALL DONE in  launch script"

