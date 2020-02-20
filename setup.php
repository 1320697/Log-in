<html>
<head>
    <title>Assessment Setup</title>
</head>

<body>
<?php

$host = "csdm-mysql";
$user = "mjh6876";
$pass = "mjh6876";
$database = "dbmjh6876_cmm007";

$connection  = mysqli_connect($host, $user, $pass, $database)
or die ("Error is " . $mysqli_error ($connection));

$query = "DROP TABLE User";
$ret = $connection->query ($query);

$query = "DROP TABLE AssessmentEntry";
$ret = $connection->query ($query);


$query = "CREATE TABLE User( Username Varchar (100), Password varchar (10), Attempts Int, Timestamp Int)";
$ret = $connection->query ($query);

$query = "CREATE TABLE AssessmentEntry( ID INT NOT NULL AUTO_INCREMENT, Username Varchar (100), Description varchar (1000), Done BOOL, WhenDone INT, WhenDue INT, PRIMARY KEY (id))";
$ret = $connection->query ($query);

$query = "INSERT INTO User(Username, Password) VALUES ('michael', 'rgu')";
$ret = $connection->query ($query);

$query = "INSERT INTO AssessmentEntry (Username, Description, Done) VALUES ('michael', 'Finish Assessment', false)";
$ret = $connection->query ($query);

$query = "INSERT INTO AssessmentEntry (Username, Description, Done) VALUES ('michael', 'Finish Assessment 2', false)";
$ret = $connection->query ($query);

$query = "INSERT INTO AssessmentEntry (Username, Description, Done) VALUES ('michael', 'Finish Assessment 2', false)";
$ret = $connection->query ($query);

if ($ret) {
    echo "<p>Table created!</p>";
}
else {
    echo "<p>Something went wrong: " . mysqli_error($connection); + "</p>";
}
?>
</body>
</html>