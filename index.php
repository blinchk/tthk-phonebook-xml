<?php
$file = "contacts.xml";
$contacts = simplexml_load_file($file);

// Recognize methods from requests parameters in POST request
// Recognize request to convert XML to JSON
if (array_key_exists('convertToJSON', $_POST)) {
    convertToJSON($contacts);
}

// Recognize request to send current data to XML
if (isset($_POST['sendDataToXml'])) {
    sendDataToXml($contacts);
}

// Recognize request to delete contact from XML
if (isset($_GET['delete'])) {
    deleteDataFromXml($contacts);
}

// Recognize request to edit contact in XML
if (isset($_POST['editing'])) {
    editData($contacts);
}

// Convert XML to JSON
function convertToJSON($xml)
{
    $json = json_encode($xml);
    $file = "contacts.json";
    $file_json = fopen($file, "w") or die ("Unable to open file!");
    fwrite($file_json, $json);
    fclose($file_json);
    downloadFile($file);
}

// Download created file by filename
function downloadFile($filename){

    $contents = file_get_contents($filename);
    $newFilePath = "_contacts.json";
    file_put_contents($newFilePath, $contents);
    header("Content-Disposition: attachment; filename=$newFilePath");
    header("Content-Type: application/json");
    // read the file from disk
    readfile($newFilePath);
    exit();
}

// Send data from HTML table to XML
function sendDataToXml($xml)
{
    $xmlDoc = new DOMDocument("1.0", "UTF-8");

    $firstname = $_POST['fName'];
    $lastname = $_POST['lName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $instagram = $_POST['instagram'];

    $contact = $xml->contacts->addChild('contact');
    $contact->addAttribute('id', rand(0, 400));
    $contact->addChild('firstname', $firstname);
    $contact->addChild('lastname', $lastname);
    $contact->addChild('phone', $phone);
    $contact->addChild('email', $email);
    $contact->addChild('instagram', $instagram);
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
    $xmlDoc->formatOutput = true;
    $xmlDoc->save('contacts.xml');
    header('refresh: 0, url=index.php');
}

// Delete data from XML
function deleteDataFromXml($xml)
{
    $id = $_GET['delete'];
    foreach ($xml->xpath('//contact') as $contact) {
        if ($contact->attributes()->id == $id) {
            unset($contact[0]);
            break;
        }
    }
    $xmlDoc = new DOMDocument("1.0", "UTF-8");
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
    $xmlDoc->formatOutput = true;
    $xmlDoc->save('contacts.xml');
    header('refresh: 0, url=index.php');
}

// Edit data in XML
function editData($xml)
{
    $id = $_GET['edit'];
    $firstname = $_POST['fName'];
    $lastname = $_POST['lName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $instagram = $_POST['instagram'];
    foreach ($xml->xpath('//contact') as $contact) {
        if ($contact->attributes()->id == $id) {
            $contact[0]->firstname = $firstname;
            $contact[0]->lastname = $lastname;
            $contact[0]->phone = $phone;
            $contact[0]->email = $email;
            $contact[0]->instagram = $instagram;
            break;
        }
    }
    $xmlDoc = new DOMDocument("1.0", "UTF-8");
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
    $xmlDoc->formatOutput = true;
    $xmlDoc->save('contacts.xml');
    header('refresh: 0, url=index.php');
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <title>Contacts</title>
</head>
<body>
<h1>Contacts</h1>
<table id="contacts-table" border="1">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Instagram</th>
        <th></th>
    </tr>
    <?php
    foreach ($contacts->xpath('//contact') as $contact) {
        $attr = $contact->attributes()->id;
        if (isset($_GET['edit']) and $attr == $_GET['edit']) {
            echo "<form method='POST' action=''>";
            echo "<tr>";
            echo "<td>" . $attr . "</td>";
            echo "<td><input type='text' name='fName' value='" . $contact->firstname . "'></td>";
            echo "<td><input type='text' name='lName' value='" . $contact->lastname . "'></td>";
            echo "<td><input type='number' pattern='+[0-9]' name='phone' value='" . $contact->phone . "'></td>";
            echo "<td><input type='email' name='email' value='" . $contact->email . "'></td>";
            echo "<td><input type='text' name='instagram' value='" . $contact->instagram . "'></td>";
            echo "<td><button type='submit' name='editing' id='editing'><span class='material-icons'>done</span></button></td>";
            echo "</tr>";
            echo "</form>";
        } else {
            echo "<tr>";
            echo "<td>$attr</td>";
            echo "<td>$contact->firstname</td>";
            echo "<td>$contact->lastname</td>";
            echo "<td>$contact->phone</td>";
            echo "<td>$contact->email</td>";
            echo "<td>$contact->instagram</td>";
            echo "<td><a href='$_SERVER[PHP_SELF]?edit=$attr'><span class='material-icons'>edit</span></a>
            <a href='$_SERVER[PHP_SELF]?delete=$attr'><span class='material-icons'>clear</span></a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>

<hr>
<h2>Add new contact</h2>
<div id="newRecordForm">
    <form method="post">
        <div class="formInput">
            <input required type="text" name="fName">
            <label for="fName">First name</label>
        </div>
        <div class="formInput">
            <input required type="text" name="lName">
            <label for="lName">Last name</label>
        </div>
        <div class="formInput">
            <input required type="text" name="phone" pattern="+[0-9]">
            <label for="phone">Phone</label>
        </div>
        <div class="formInput">
            <input required type="email" name="email">
            <label for="email">Email</label>
        </div>
        <div class="formInput">
            <input required type="text" name="instagram">
            <label for="instagram">Instagram</label>
        </div>
        <button class="button" type="submit" name="sendDataToXml">Send data</button>
    </form>
</div>
<hr>
<form method="post">
    <button type="submit" class="button" name="convertToJSON">Convert to JSON</button>
</form>
<link rel="stylesheet" href="index.css">
</body>
</html>
