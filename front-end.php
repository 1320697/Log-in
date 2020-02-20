<?php
// Start our session so we can get access to the $_SESSION variable.   This has
// to happen *before* any other output from the script, including spaces.  This is
// the only PHP we use in this page, but it still requires us to save the file with a
// .php extension.
session_start();

// Check if there is no session variable with the name 'username' set
if (!isset ($_SESSION["username"])) {
    // There is no session variable, so redirect them to the example_login.html page.
    header( 'Location: example_login.html' ) ;
    exit;
}
?>

<html>

<head>
    <title>Assessment Calendar</title>
</head>
<body onLoad="setupAjax()">
<h1>Assessment List</h1>

<script language="JavaScript">

    // The front end of this program is a blend of technologies - a little bit of PHP, some
    // javascript, and some XML parsing.

    // This is the validate function - it checks to see if the provided HTML control has a
    // value, returning false if it doesn't or true if it does.
    function validate (txt) {
        // Go through the DOM and find the element that has the ID we were provided as a
        // parameter.
        var ele = document.getElementById (txt);

        // Check to see if we found something.
        if (!ele) {
            // We didn't, so return false.
            return false;
        }

        // Now check to see its value - check to see if its something other than an empty
        // string.
        if (ele.value != "") {
            // It is, so return true.
            return true;
        }
    }


    // This is the Ajax handling function that triggerswhen we press the 'add' button.
    function validateDate (form) {
        var bing;
        var desc;
        // Run the validate function against the HTML element with the id of 'due'
        if (!validate ("due")) {
            // If it fails to validate, provide an alert and return from the function.
            alert ("Please enter a date");
            return;
        }

        // If we get here, the HTML control validated so we're into the Ajax setup.  First
        // thing we have to do is get hold of the object that does the work for is.  The way
        // this is done differs based on browser.

        // First we try to get the XMLHttpRequest object which is present on most modern
        // browsers.  We check to see if it exists at all.
        if (window.XMLHttpRequest) {
            // It does, so we make our request variable equal to a new instance of that object.
            request=new XMLHttpRequest();
        }
        else {
            // We didn't have the XMLHttpPRequest object available, so we have to put in a
            // workaround for old versions of internet explorer.  Other than that, it should
            // work the same way.
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }

        // Here we set the callback for the request object we created.  We're saying 'when anything
        // changes in the communication between the client and the server, call the function we
        // define here.
        request.onreadystatechange=function() {
            // We're only interested in this when both the ready state is 4 and the status is 200.  This
            // basically means 'when the communication between the client and the server has been fully
            // handled.
            if (request.readyState==4 && request.status==200) {
                // When that occurs, call the setupAjax function.  This is a second Ajax function that populates
                // the table of information from the database.
                setupAjax();
            }
        }

        // Get the HTML element with the id of 'description'
        desc = document.getElementById ("description");
        // Get the HTML element with the id of 'due'
        due  = document.getElementById ("due");

        // Note here that we're putting the value of two HTML components into a URL string.  This means we're building
        // a GET access call for the update_content.php page.  It'll have three $_GET parameters available in the
        // php script - an 'action' (which will be set to "new"), a 'description' (which will be whatever is in the desc
        // value) and a 'due' which will be whatever comes out of the 'due' HTML element.
        bing = "update_content.php?action=new&description=" + desc.value + "&due=" + due.value;

        // Actually send our request across, using the URL we constructed above.  First we open up the connection between
        // the client and the server.
        request.open ("GET", bing, true);

        // Then we send it.  When communication comes back, it'll trigger the callback we set above.
        request.send();
    }


    // This is the function that triggers when we click a checkbox.
    function updateDone (num) {
        var val;
        var url;

        // We already saw a description of that above - it's exactly the same.
        if (window.XMLHttpRequest) {
            // Code for modern browsers
            request=new XMLHttpRequest();
        }
        else {
            // code for older versions of Internet Explorer
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }

        request.onreadystatechange=function() {
            if (request.readyState==4 && request.status==200) {
                setupAjax();
            }
        }

        // We check to see which checkbox was changed and we get its checked property.
        if (document.getElementById("chk" + num).checked) {
            val = 1;
        }
        else {
            val = 0;
        }

        // We construct a GET query (as above) to change the state of that specific 'done' value in the database/
        url = "update_content.php?action=done&id=" + num + "&done=" + val;

        // Send the Ajax query across.
        request.open ("GET", url, true);
        request.send();

    }

    // This is probably the most complicated function in the program, and it's what turns the XML we get back from the
    // PHP ajax scripts into a table.  You don't need to do anything this complicated for your assessments - any kind of
    // front-end would be fine.
    function createTable (XML) {
        var table;
        var elements;
        var id, description, done, wdone, wdue;
        var check, idNum;
        // First, we get all the XML entities that have the tag 'entry' - that's not the root element, but each of the entries
        // it contains.
        elements = XML.documentElement.getElementsByTagName("entry");

        // We create the start of our table.  We're building our HTML as a string here, and then we'll inject it into the DOM
        // later.
        table = "<table border = \"1\">";

        // Set up the headers.
        table += "<tr>";
        table += "<th>ID</th>";
        table += "<th>Done</th>";
        table += "<th>Description</th>";
        table += "<th>When Due</th>";
        table += "<th>When Done</th>";
        table += "</tr>";

        // Here, we step through the array of elements we gained above - each element of this array contains an 'entry' from the
        // XML.  XML is a little awkward to work with because of the way it contains both data and metadata, so we need to extract
        // the only bits we're intrested in - the bits *between* the tags.
        for (i = 0; i < elements.length; i++) {
            // First, we go over each of the possible parts of the XML entry, and pull them into their own variables.
            // Each of these is an array of its own because there's nothing to stop an XML entry having multiple descriptions,
            // id tags or such.
            id = elements[i].getElementsByTagName ("ID");
            done = elements[i].getElementsByTagName ("Done");
            description = elements[i].getElementsByTagName ("Description");
            wdue = elements[i].getElementsByTagName ("WhenDue");
            wdone = elements[i].getElementsByTagName ("WhenDone");

            // Start a new row of the table.
            table +=  "<tr>";

            // Here, we say 'get the first element of the array we set up above'.  There is only one element in the array because
            // the XML format that comes back from query_content.php is set up like that.
            //
            // Next we say 'Give me the first child of that node' - remember XML can be nested, so what we're saying is 'give me
            // the first thing between the tags'.  We know, because the XML comes from our program, that there won't be a nested
            // tag in here.  All that will be between the tags is the text content - that's held in nodeValue.  At the end of this
            // process, we've got the text content extracted from the ID tag from our Entry tag.
            idNum = id[0].firstChild.nodeValue;

            // Put that in a cell of the table.
            table +=  "<td>" + idNum + "</td>";

            // And repeat for the check value.
            check = done[0].firstChild.nodeValue;

            // Here, we have two possibilities - if check is 1 it means the checkbox should be checked.
            if (check == 1) {
                // Here we put a checkbox into the table cell.  It comes with a click handler (updateDone) and its starting state checked.
                table +=  "<td><input type=\"checkbox\" id=\"chk" + idNum + "\" onClick=\"updateDone(" + idNum + ")\" checked/></td>";
            }
            else {
                // Here we put a checkbox into the table cell.  It comes with a click handler (updateDone) and its starting state unchecked.
                table +=  "<td><input type=\"checkbox\" id=\"chk" + idNum + "\" onClick=\"updateDone(" + idNum + ")\"/></td>";
            }

            // Put the description into a cell of the table/
            table +=  "<td>" + description[0].firstChild.nodeValue + "</td>";

            // If wdue has been set
            if (wdue && wdue[0].firstChild) {
                // Put its value into the table.
                table +=  "<td>" + wdue[0].firstChild.nodeValue + "</td>";
            }
            else {
                // Otherwise put the value 'unset'
                table +=  "<td>Unset</td>";
            }

            // Ditto for wdone.
            if (wdone && wdone[0].firstChild) {
                table +=  "<td>" + wdone[0].firstChild.nodeValue + "</td>";
            }
            else {
                table +=  "<td>Unset</td>";
            }

            // Close off our table row.
            table += "</tr>";
        }

        // Close off our table.
        table += "</table>";

        // Return the string containing this HTML table.
        return table;
    }

    // This is the function that updates the table - all the other ajax functions call this when they're done so that the
    // application updates 'in real time'.
    function setupAjax() {
        var url;

        // See above for a description of how this works.
        if (window.XMLHttpRequest) {
            // Code for modern browsers
            request=new XMLHttpRequest();
        }
        else {
            // code for older versions of Internet Explorer
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }

        request.onreadystatechange=function() {
            if (request.readyState==4 && request.status==200) {
                if (request.responseXML) {
                    // Here, we say 'Take the XML that comes out of this ajax call, throw it through the createTable function, and put
                    // the result into the innerHTML of the 'results' HTML element.  We use request.responseXML for this because our PHP
                    // script is returning XML.  If it's just returning text or html, we'd use request.responseText.
                    document.getElementById("results").innerHTML= createTable (request.responseXML);
                }
            }
        }

        // Send the query across.
        request.open ("GET", "query_content.php", true);
        request.send();

    }

    function query_due () {
        var due = document.getElementById ("query");
        // Validation, as discussed above.
        if (!validate ("query")) {
            alert ("Please enter a date");
            return;
        }

        if (window.XMLHttpRequest) {
            // Code for modern browsers
            request=new XMLHttpRequest();
        }
        else {
            // code for older versions of Internet Explorer
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }

        request.onreadystatechange=function() {
            if (request.readyState==4 && request.status==200) {
                // Creating the table, as discussed above.
                document.getElementById("results").innerHTML= createTable (request.responseXML);
            }
        }

        // This works like the normal query_content.php page, except it passes some GET parameters across so that the PHP script
        // can filter appropriately.
        bing = "query_content.php?action=bydate&date=" + due.value;

        // Send the request across.
        request.open ("GET", bing, true);
        request.send();
    }


</script>

<p id = "results"></p>

<hr />

<form name = "newAssessment">

    <h2>Add New Assessment</h2>

    <p>Enter Description</p>
    <input type = "text" id = "description">
    <p>When Due</p>
    <input type = "text" id = "due">

    <input type = "button" value = "add" onClick="validateDate()">
</form>

<h2>Query Due Assessments</h2>

<form name = "querybydate">
    <p>Date After</p>
    <input type = "text" id = "query">

    <input type = "button" value = "query" onClick="query_due()">
</form>
|

</body>
</html>