#!/bin/bash

# running the clenaup script to ensure there are no "already exists" errors

chmod 700 ./itmo-544-mp1/cleanup.sh

./itmo-544-mp1/cleanup.sh

declare -a InstanceArray
declare -a InstanceRunningArray


echo "creating instances"
aws ec2 run-instances --image-id $1 --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --iam-instance-profile Name=phpdeveloperRole --associate-public-ip-address --user-data file://itmo-544-mp1-env/install-env.sh  


# waiting for instances to be available after 2 checks
sleep 30


mapfile -t InstanceRunningArray < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=0 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 


# if there are no running instances, wait for some more time
if [ ${#InstanceRunninArray[@]} -eq 0 ];then

sleep 30

fi

# waiting for instances to be available
#aws ec2 wait --region us-west-2b instance-running --instance-ids ${InstanceRunningArray[@]}

# creating instance id array to use in the next steps for attaching instances to loadbalancer
mapfile -t InstanceArray < <(aws ec2 describe-instances --filter Name=instance-state-code,Values=16 --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g") 

#echo "Instance IDs are ${InstanceArray[@]}"

# using the subnets and security groups entered by user in the launch script

echo "creating load balancer"
aws elb create-load-balancer --load-balancer-name usnehaLb --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --subnets $6 --security-groups $5


echo "load balancer health check"
aws elb configure-health-check --load-balancer-name usnehaLb --health-check Target=HTTP:80/index.php,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3


echo "attaching instances to the load balancer"
aws elb register-instances-with-load-balancer --load-balancer-name usnehaLb --instances ${InstanceArray[@]} 


echo "creating launch configuration"
#aws autoscaling create-launch-configuration --launch-configuration-name usnehaLc --image-id $1 --key-name $4  --security-groups $5 --instance-type $3 --user-data file://itmo-544-mp1-env/install-env.sh --iam-instance-profile phpdeveloperRole

echo "creating auto scaling group"
#aws autoscaling create-auto-scaling-group --auto-scaling-group-name usnehaAsg --launch-configuration-name usnehaLc --load-balancer-names usnehaLb  --health-check-type ELB --min-size 1 --max-size 3 --desired-capacity 2 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier $6 



#mapfile -t VPCSubnetArr< <(aws ec2 describe-subnets --filters "Name=vpc-id,Values=vpc-861d43e3" --output table |grep SubnetId | sed "s/|//g" | tr -d ' ' | sed "s/SubnetId//g")

aws rds create-db-subnet-group --db-subnet-group-name usnehasg --db-subnet-group-description usneha-subnetgrp --subnet-ids subnet-935e14f6 subnet-3a6b034d


echo "Creating DB instance"
result= aws rds create-db-instance --db-name usnehadb --db-instance-identifier usneha --allocated-storage 20 --db-instance-class db.t1.micro --engine MYSQL --master-username username --master-user-password password --vpc-security-group-ids $5 --availability-zone us-west-2a  --db-subnet-group-name usnehasg --publicly-accessible

# waiting for the DB instance to be available

aws rds wait db-instance-available --db-instance-identifier usneha
 
chmod 700 ./itmo-544-mp1/dbcreate.sh
./itmo-544-mp1/dbcreate.sh

echo "ALL DONE in  launch script"

