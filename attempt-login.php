<?php
// Here we start our session going - this gives us access to the $_SESSION global
// variable.
session_start();
?>

<html>
<head>
    <title>Simple Login</title>
</head>
<body>

<?php

// Check to see if there is a 'username' key in the $_SESSION variable.
// isset() will return true if the key is present and false if it isn't.
if (isset ($_SESSION["username"])) {
    // If we want to handle someone logging in with a second name, we'll
    // need to destroy all the previous session information.  So we cheat
    // and just stop people logging in again if they're already logged in.
    echo "<p>You are already logged in.</p>";
    return;
}

// Get the POST variable with the name 'username', and stick it into the variable
// $username.  This will only work if the variables come from the HTML form over
// POST.  If we're using GET, we'd use $_GET instead.
$username = $_POST["username"];
// Ditto for the password.
$password = $_POST["password"];

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

// This is the string containing our sql query.
$query = "select * from User where Username=\"$username\"";

// This runs the query string against the connection we gained above, and puts what comes
// out of the query in the $results variable.
$results = $connection->query ($query);

// Find out how many records got returned from our query - that then gets stored in
// $num_results.
$num_results = mysqli_num_rows ($results);

// If $num_results is greater than 0, it shows that *something* came back from the
// database query.
if ($num_results > 0) {
    // Get a row from the results recordset.
    $row = mysqli_fetch_array ($results);
    // Get the password from that row.  Store that as $pass.
    $pass = $row["Password"];

    // $password is the value we got from the $_POST array.  $pass is the one we got from
    // the database record.  Check to see if they're the same.
    if ($pass == $password) {
        // They are!  So we register a $_SESSION variable with the name 'username'.  Its
        // value is what came in as $username from $_POST.
        $_SESSION["username"] = $username;
        // Echo an inform saying the login was successful, and give them a link to the main
        // front end.
        echo "<p>Login successful, ". $_SESSION["username"] . ".  Click <a href = \"front_end.php\">here</a> to go to your Assessment list.</p>";
    }
    else {
        // They didn't match, so give them a chance to try again.
        echo "<p>Invalid login</p>";
        echo "<a href = \"example_login.html\">Try again</a>";
    }
}
else {
    // There were no matching records - the $num_results variable was 0, so give them a link to the register page.
    echo "<p>Invalid login</p>";
    echo "<a href = \"example_registration.html\">Register</a>";
}
?>

</body>
</html>

