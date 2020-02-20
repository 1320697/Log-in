<?php
// Start our session up so we have access to what's in the $_SESSION array.
session_start();

// Here we tell the browser that what's coming back from this is pure XML.
header('Content-Type: text/xml; charset=utf-8');

// Check to see if the session variable "username" has been set.
if (!isset ($_SESSION["username"])) {
    // If it hasn't, redirect to the login page.
    header( 'Location: example_login.html' ) ;
    return;
}
else {
    $username = $_SESSION["username"];
}

// This variable may or may not be set by a GET variable later.
$action = 0;

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

// Check to see if we got an action that came across via GET.
if (isset ($_GET["action"])) {
    // It did, so set the variable to be what's in the GET parameter.  This lets us
    // have our script do different things depending on what we tell it over GET.
    $action = $_GET["action"];
}

// Check to see if we have a date to filter against.
if (isset ($_GET["date"])) {
    // We do, so set that here/
    $date = $_GET["date"];
}

// Now we create our query strings based on whether or not we got anything sent to us
// via GET parameters.
if (!$action) {
    // This queries based on the currently logged in user.
    $query = "SELECT * from AssessmentEntry where Username ='$username'";
}
else {
    // This queries based on the currently logged in user and the date provided via GET.
    $query = "SELECT * from AssessmentEntry where Username ='$username' and Done=0 and WhenDue > $date";
}

// Run the query.
$ret = $connection->query ($query);

// get the results it gave us and count them.
$num_results = mysqli_num_rows ($ret);

// If there aren't any results
if ($num_results == 0) {
    return "Oh no!";
}

// Here we turn the data we got from the database into XML for inclusion into the main frontend.

// First we create our DOM tree.  It's empty at this point.
$doc = new DOMDocument();
$doc->formatOutput = true;

// Now we create the root element - the one that's going to act as a container for all the other
// elements
$root = $doc->createElement( "Assessment_entry" );

// And we add that to our DOM document.
$doc->appendChild( $root );

// Now, for each record we got out of our query...
for ($i = 0; $i < $num_results; $i++) {
    // Get the next row from the recordset.
    $row = mysqli_fetch_array ($ret);

    // We create a new XML node here - this one called entry.  That'll hold each of the
    // fields from the record.
    $node = $doc->createElement( "entry" );

    // The process we go through here is the same for each field in the record.  We create a
    // node that will hold the data, giving it whatever name we want.  The name will be translated
    // into an XML tag, so what we're doing here is creating <ID></ID>
    $id = $doc->createElement( "ID" );
    // Next, we're putting something between those tags.  Here, we're pulling what's in the $row with the
    // name $ID, wrapping that up in a 'text node' and then appending that to our ID node.  At this point
    // it contains <ID>4</ID>, with 4 being whatever the information that came out of the database is.
    $id->appendChild($doc->createTextNode($row["ID"]));
    // Finally, we append our ID node to the main 'entry' node.
    $node->appendChild( $id);

    // Repeat for the description
    $description = $doc->createElement( "Description" );
    $description->appendChild($doc->createTextNode($row["Description"]));
    $node->appendChild( $description );

    // Repeat for Done
    $done = $doc->createElement( "Done" );
    $done->appendChild($doc->createTextNode($row["Done"]));
    $node->appendChild( $done);

    // Repeat for whendone
    $whendone = $doc->createElement( "WhenDone" );
    $whendone->appendChild($doc->createTextNode($row["WhenDone"]));
    $node->appendChild( $whendone);

    // Repeat for whendue
    $whendue = $doc->createElement( "WhenDue" );
    $whendue->appendChild($doc->createTextNode($row["WhenDue"]));
    $node->appendChild( $whendue);

    // Finally, add our fully configured 'entry' node to the main root node, which is 'Assessment_entry'.
    $root->appendChild ($node);

}

// Close our mysql connection
mysqli_close($connection);

// Send our DOM document out as a string of XML.
echo $doc->saveXML();


?>
