<?php
session_start();

// Setup the database information - you'll find the full details of this in other php files.
$host = "csdm-mysql";
$user = "mjh6876";
$pass = "mjh6876";
$database = "dbmjh6876_cmm007";
$action = 0;

// Check the username is set, as discussed in query_content.php.
if (!isset ($_SESSION["username"])) {
    // If it's not set, redirect them to login.
    header( 'Location: example_login.html' ) ;
    return;
}
else {
    // Otherwise, set the $username variable based on what was set in the session.
    $username = $_SESSION["username"];
}

// connect to the database.
$connection  = mysqli_connect($host, $user, $pass, $database)
or die ("Error is " . $mysqli_error ($connection));

// Different combinations of data might come through here, so we check to see each of the GET parameters we
// may or may not support and set our internal variables accordingly.
if (isset ($_GET["description"])) {
    $description = $_GET["description"];
}

if (isset ($_GET["due"])) {
    $due= $_GET["due"];
}

if (isset ($_GET["action"])) {
    $action = $_GET["action"];
}

if (isset ($_GET["done"])) {
    $done = $_GET["done"];
}

if (isset ($_GET["id"])) {
    $id= $_GET["id"];
}


// We use a switch here to choose between different courses of action - that allows us to have one script that handles both new
// entries and updating existing entries.
switch ($action) {
    case "new":
        $query = "INSERT INTO AssessmentEntry (Username, Description, WhenDue, Done) VALUES ('$username', '$description', '$due', false)";

        $ret = $connection->query ($query) or die (mysqli_error ($connection));

        break;
    case "done":
        $wdone=time();
        echo "<p>Done is $id $done $wdone</p>";
        $query = "UPDATE AssessmentEntry SET Done = '$done', WhenDone = '$wdone' WHERE Username='$username' AND ID='$id'";
        $ret = $connection->query ($query);

        break;
}

?>
