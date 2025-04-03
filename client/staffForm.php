<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Staff Form</title>
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
            <h3>Add New Staff Member</h3>
            <form method="POST" action="">
                <label for="staffNo">Staff Number:</label>
                <input type="text" id="staffNo" name="staffNo">

                <label for="fName">First Name:</label>
                <input type="text" id="fName" name="fName">

                <label for="lName">Last Name:</label>
                <input type="text" id="lName" name="lName">

                <label for="position">Position:</label>
                <input type="text" id="position" name="position">

                <label for="sex">Sex:</label>
                <input type="text" id="sex" name="sex">

                <label for="branchNo">Branch Number:</label>
                <input type="text" id="branchNo" name="branchNo">

                <label for="dob">Date of Birth (YYYY-MM-DD):</label>
                <input type="text" id="dob" name="dob">

                <label for="salary">Salary:</label>
                <input type="text" id="salary" name="salary">

                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone">

                <label for="mobile">Mobile:</label>
                <input type="text" id="mobile" name="mobile">

                <label for="email">Email:</label>
                <input type="text" id="email" name="email">

                <input type="submit" value="Add Staff Member">

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
                $staffNo = $_POST['staffNo'];
                $fName = $_POST['fName'];
                $lName = $_POST['lName'];
                $position = $_POST['position'];
                $sex = $_POST['sex'];
                $branchNo = $_POST['branchNo'];
                $dob = $_POST['dob'];
                $salary = $_POST['salary'];
                $telephone = $_POST['telephone'];
                $mobile = $_POST['mobile'];
                $email = $_POST['email'];

                // PL/SQL query to insert a new staff member
                $sql = "BEGIN
                          INSERT INTO DH_STAFF (staffNo, fName, lName, position, sex, dob, salary, branchNo, telephone, mobile, email)
                          VALUES (:staffNo, :fName, :lName, :position, :sex, TO_DATE(:dob, 'YYYY-MM-DD'), :salary, :branchNo, :telephone, :mobile, :email);
                          COMMIT;
                        END;";

                echo "";
                echo "<pre>";
                echo htmlentities($sql);
                echo "</pre>";

                // Prepare the statement
                $stmt = oci_parse($conn, $sql);

                // Bind parameters
                oci_bind_by_name($stmt, ':staffNo', $staffNo);
                oci_bind_by_name($stmt, ':fName', $fName);
                oci_bind_by_name($stmt, ':lName', $lName);
                oci_bind_by_name($stmt, ':position', $position);
                oci_bind_by_name($stmt, ':sex', $sex);
                oci_bind_by_name($stmt, ':dob', $dob);
                oci_bind_by_name($stmt, ':salary', $salary);
                oci_bind_by_name($stmt, ':branchNo', $branchNo);
                oci_bind_by_name($stmt, ':telephone', $telephone);
                oci_bind_by_name($stmt, ':mobile', $mobile);
                oci_bind_by_name($stmt, ':email', $email);

                // Execute the statement
                $result = oci_execute($stmt);

                if ($result) {
                    echo "<p>New staff member added successfully!</p>";
                } else {
                    $e = oci_error($stmt);
                    echo "<p>Error adding staff member: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
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
