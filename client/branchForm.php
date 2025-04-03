<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Branch Form</title>
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
            <h3>Add New Branch</h3>
            <form method="POST" action="">
                <label for="branchNo">Branch Number:</label>
                <input type="text" id="branchNo" name="branchNo">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address">

                <label for="city">City:</label>
                <input type="text" id="city" name="city">

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode">


                <input type="submit" value="Add Branch">

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
                $branchNo = $_POST['branchNo'];
                $address = $_POST['address'];
                $city = $_POST['city'];
                $postalCode = $_POST['postalCode'];

                // PL/SQL query to insert a new branch
                $sql = "BEGIN
                          INSERT INTO DH_BRANCH (branchNo, street, city, postcode)
                          VALUES (:branchNo, :address, :city, :postalCode);
                          COMMIT;
                        END;";

                echo "";
                echo "<pre>";
                echo htmlentities($sql);
                echo "</pre>";

                // Prepare the statement
                $stmt = oci_parse($conn, $sql);

                // Bind parameters
                oci_bind_by_name($stmt, ':branchNo', $branchNo);
                oci_bind_by_name($stmt, ':address', $address);
                oci_bind_by_name($stmt, ':city', $city);
                oci_bind_by_name($stmt, ':postalCode', $postalCode);

                // Execute the statement
                $result = oci_execute($stmt);

                if ($result) {
                    echo "<p>New branch added successfully!</p>";
                } else {
                    $e = oci_error($stmt);
                    echo "<p>Error adding branch: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
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
