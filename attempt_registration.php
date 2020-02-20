<html>
<head>
    <title>Simple Login</title>
</head>
<body>

<?php

// Check to see if we got a username variable sent to us over POST.
if (isset ($_POST["username"])) {
    // If we did, put its value into the variable $username.
    $username = $_POST["username"];
}

// Check to see if we got a password variable sent to us over POST.
if (isset ($_POST["password"])) {
    // If we did, put its value into the variable $password.
    $password= $_POST["password"];
}

// Set the host information for our database query - this is the server where
// it's held.  This will be the same for your own data.
$host = "csdm-mysql";

// Username, password and the specific database will be different - your username
// and password are your matriculation number.  You can find the database name
// via Toad or phpmyadmin.

$user = "mjh6876";
$pass = "mjh6876";
$database = "dbmjh6876_cmm007";

// Try to make the connection using the details we provided.  Note the 'or die' here -
// if the attempt fails, this will give us the error message that causes the problem.
// We can use this 'or die' format on any mysql function call to find out why it failed.
$connection  = mysqli_connect($host, $user, $pass, $database)
or die ("Error is " . $mysqli_error ($connection));

// This is the first of our queries against the database.  Here, we want to see if there
// is a record with the indicated username already present.
$query_check = "select * from User where Username=\"$username\"";

// This runs the query string against the connection we gained above, and puts what comes
// out of the query in the $results variable.
$results = $connection->query ($query_check);

// If we didn't get anything back, print out an error message.
if (!$results) {
    echo "<p>" . mysqli_error($connection) . "</p>";
}

// Count the number of records that were returned.
$num_results = mysqli_num_rows ($results);

// If the number of records is not equal to 0, then there was a record with that username
// in the database.  So we can't proceed with the registration, because someone has that
// username already.
if ($num_results != 0) {
    echo "<p>That username already exists</p>";
    echo "<a href = \"example_login.html\">login</a>";
    exit;
}

// Okay, the username didn't exist so we create our second query string here.  This one puts
// the provided username and password *into* the database.
$query = "insert into User (Username, Password) values (\"$username\"
    , \"$password\")";

// Run that query against the database connection - put the results in the variable $ret.
$ret = $connection->query ($query);


if (!$ret) {
    // Something went wrong, so show the failed registration error message.
    echo "<p>Failed registration: " . mysqli_error($connection) . "</p>";
}

// It all went okay, so provide a link to the login page.
echo "<p>Registration successful</p>";
echo "<a href = \"example_login.html\">login</a>";


?>

</body>
</html>
