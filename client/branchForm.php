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

                <?php
                include 'db_credentials.php';

                $conn = oci_connect($db_user, $db_password, $connection_string);
                if (!$conn) {
                    $e = oci_error();
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                }

                $branchNo = '';
                $address = '';
                $city = '';
                $postalCode = '';

                // --- EDITING: Load branch details if GET param is present ---
                if (isset($_GET['branchNo']) && !empty($_GET['branchNo'])) {
                    $branchNo = $_GET['branchNo'];

                    // Enable DBMS_OUTPUT
                    $stmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
                    oci_execute($stmt);

                    // PL/SQL block to fetch data
                    $plsql = "
                    DECLARE
                        v_street VARCHAR2(100);
                        v_city VARCHAR2(100);
                        v_postcode VARCHAR2(20);
                    BEGIN
                        SELECT street, city, postcode
                        INTO v_street, v_city, v_postcode
                        FROM DH_BRANCH
                        WHERE branchNo = :branch_no;

                        DBMS_OUTPUT.PUT_LINE(v_street || ',' || v_city || ',' || v_postcode);
                    EXCEPTION
                        WHEN NO_DATA_FOUND THEN
                            DBMS_OUTPUT.PUT_LINE(',,,');
                    END;
                    ";

                    $stmt = oci_parse($conn, $plsql);
                    oci_bind_by_name($stmt, ':branch_no', $branchNo);
                    oci_execute($stmt);

                    $output_stmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
                    oci_bind_by_name($output_stmt, ":line", $line, 32767);
                    oci_bind_by_name($output_stmt, ":status", $status);
                    oci_execute($output_stmt);

                    if ($status == 0 && !empty($line)) {
                        list($address, $city, $postalCode) = explode(',', $line);
                    }

                    oci_free_statement($stmt);
                    oci_free_statement($output_stmt);
                                }
                                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $branchNo = $_POST['branchNo'];
                    $address = $_POST['address'];
                    $city = $_POST['city'];
                    $postalCode = $_POST['postalCode'];

                    $plsql = "
                    BEGIN
                        MERGE INTO DH_BRANCH b
                        USING (SELECT :branchNo AS branchNo FROM dual) d
                        ON (b.branchNo = d.branchNo)
                        WHEN MATCHED THEN
                            UPDATE SET b.street = :address, b.city = :city, b.postcode = :postalCode
                        WHEN NOT MATCHED THEN
                            INSERT (branchNo, street, city, postcode)
                            VALUES (:branchNo, :address, :city, :postalCode);
                        COMMIT;
                    END;
                    ";

                    $stmt = oci_parse($conn, $plsql);
                    oci_bind_by_name($stmt, ':branchNo', $branchNo);
                    oci_bind_by_name($stmt, ':address', $address);
                    oci_bind_by_name($stmt, ':city', $city);
                    oci_bind_by_name($stmt, ':postalCode', $postalCode);

                    $result = oci_execute($stmt);

                    if ($result) {
                        echo "<p>Branch saved successfully!</p>";
                    } else {
                        $e = oci_error($stmt);
                        echo "<p>Error saving branch: " . htmlentities($e['message']) . "</p>";
                    }

                    oci_free_statement($stmt);
                    oci_close($conn);
                }

                ?>

            <form method="POST" action="">
                <label for="branchNo">Branch Number:</label>
                <input type="text" id="branchNo" name="branchNo"
                       value="<?php echo htmlspecialchars($branchNo); ?>"
                       <?php echo isset($_GET['branchNo']) ? 'readonly' : ''; ?>>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">

                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">

                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($postalCode); ?>">


                <input type="submit" value="Add Branch">

            </form>




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
