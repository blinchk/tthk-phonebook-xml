<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contacts</title>
</head>
<body>
    <h1>Contacts</h1>
    <?php
    $file = "contacts.xml";
    $contacts = simplexml_load_file($file) or die ('Cannot parse your XML');
    ?>
    <table id="contacts-table" border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Instagram</th>
        </tr>
    <?php
    foreach($contacts as $contact) {
        echo "<tr>";
        echo "<td>$contact->id</td>";
        echo "<td>$contact->firstname</td>";
        echo "<td>$contact->lastname</td>";
        echo "<td>$contact->phone</td>";
        echo "<td>$contact->email</td>";
        echo "<td>$contact->instagram</td>";
        echo "</tr>";
    }
    ?>
    </table>
    <link rel="stylesheet" href="index.css">
</body>
</html>