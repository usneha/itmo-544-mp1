#! /bin/bash

echo "Creating DB instance"
mapfile -t dbresult < <(aws rds describe-db-instances --db-instance-identifier usneha --output table | grep Address | sed "s/|//g" | tr -d ' ' | sed "s/Address//g")

echo "waiting for the RDS database instance created in launch scirpt  to be available"
#aws rds wait db-instance-available --db-instance-identifier usneha
 
echo "============\n". $dbresult . "================";

#installing php5-mysql in all instances
sudo apt-get install php5-mysql

# passing the db instances to the setup.php script

chmod 700 ./itmo-544-mp1/setup.php

php ./itmo-544-mp1/setup.php $dbresult

echo "executed createdb script successfully"
