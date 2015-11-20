<!DOCTYPE html>
<html>
<body>

<?php
# using the argument passed through createdb to this script

$endpoint = $argv[1];
echo "Connecting to mysqli"; 

# connecting to mysql instance
$link = mysqli_connect($endpoint,"username","password","usnehadb",3306) or die("Error " . mysqli_error()); 

# creating table as per the requirement
$sql = "CREATE TABLE items 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
UName VARCHAR(20),
Email Varchar(20),
Phone Varchar(20),
RawS3Url  Varchar(256),
FinalS3Url  Varchar(256),
JpgFileName    Varchar(256),
Status    TinyInt(3),
Ifsubscribed TinyInt(3),
CreationTime  Timestamp DEFAULT CURRENT_TIMESTAMP,
tsubscribe Varchar(5),
subscriptionId Varchar(256)
)";
print($sql);

if ($link->query($sql) === TRUE) {
    echo "Table  created";
} else {
    echo "Error creating table: " . $link->error;
}
$link->close();
echo "done";
?>

</body>
</html>
