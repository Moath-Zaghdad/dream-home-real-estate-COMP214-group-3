<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Client Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div>
            <h1>Dream Home Real Estate Group 4</h1>
            <h2>Comp 214 (Sec 401)</h2>
        </div>
    </header>
    <nav>
        <div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="staff.php">Staff Main Menu</a></li>
                <li><a href="branch.php">Branch Main Menu</a></li>
                <li><a href="client.php">Client Main Menu</a></li>
            </ul>
        </div>
    </nav>
    <main>
        <div>
            <h3>Add New Client</h3>
            <form method="POST" action="">
                <label for="clientNo">Client Number:</label>
                <input type="text" id="clientNo" name="clientNo">

                <label for="fName">First Name:</label>
                <input type="text" id="fName" name="fName">

                <label for="lName">Last Name:</label>
                <input type="text" id="lName" name="lName">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address">

                <label for="city">City:</label>
                <input type="text" id="city" name="city">

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode">

                <label for="telephone">Telephone:</label>
                <input type="text" id="Telephone" name="Telephone">

                <label for="email">Email:</label>
                <input type="text" id="email" name="email">


                <input type="submit" value="Add Client">

            </form>

            <?php
            // Include database credentials
            include 'db_credentials.php';

            // Establish connection to Oracle
            $conn = oci_connect($db_user, $db_password, $connection_string);

            if (!$conn) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Retrieve form data
                $clientNo = $_POST['clientNo'];
                $fName = $_POST['fName'];
                $lName = $_POST['lName'];
                $address = $_POST['address'];
                $city = $_POST['city'];
                $postalCode = $_POST['postalCode'];
                $telephone = $_POST['Telephone'];
                $email = $_POST['email'];

                // PL/SQL query to insert a new client
                $sql = "BEGIN
                          INSERT INTO DH_CLIENT (clientNo, fName, lName, address, city, postcode, telNo, email)
                          VALUES (:clientNo, :fName, :lName, :address, :city, :postalCode, :telephone, :email);
                          COMMIT;
                        END;";

                echo "";
                echo "<pre>";
                echo htmlentities($sql);
                echo "</pre>";

                // Prepare the statement
                $stmt = oci_parse($conn, $sql);

                // Bind parameters
                oci_bind_by_name($stmt, ':clientNo', $clientNo);
                oci_bind_by_name($stmt, ':fName', $fName);
                oci_bind_by_name($stmt, ':lName', $lName);
                oci_bind_by_name($stmt, ':address', $address);
                oci_bind_by_name($stmt, ':city', $city);
                oci_bind_by_name($stmt, ':postalCode', $postalCode);
                oci_bind_by_name($stmt, ':telephone', $telephone);
                oci_bind_by_name($stmt, ':email', $email);

                // Execute the statement
                $result = oci_execute($stmt);

                if ($result) {
                    echo "<p>New client added successfully!</p>";
                } else {
                    $e = oci_error($stmt);
                    echo "<p>Error adding client: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
                }

                // Clean up
                oci_free_statement($stmt);
            }

            // Close the connection
            oci_close($conn);
            ?>

        </div>
    </main>
    <aside>
        <div></div>
    </aside>
    <footer>
        <ul>
            <li>Julian Quan Fun</li>
            <li>Serhat Tasdelen</li>
            <li>Moath Zaghdad</li>
        </ul>
    </footer>
</body>
</html>
