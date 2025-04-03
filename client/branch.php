<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Branch</title>
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
            <h3>Branch Main Menu</h3>
            <h4>Find Branch</h4>
            <form method="GET" action="">
                <label for="getAddress">Enter Branch Number:</label>
                <input type="text" name="branchNumber" id="getAddress">
                <div class="marginTop">
                    <a href="editBranch.php"><button type="button">Edit Branches</button></a>
                    <button type="submit">Find Branch</button>
                </div>
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

            if (isset($_GET['branchNumber']) && !empty($_GET['branchNumber'])) {
                $branchNumber = $_GET['branchNumber'];

                // Enable DBMS_OUTPUT
                $stmt = oci_parse($conn, 'BEGIN DBMS_OUTPUT.ENABLE(NULL); END;');
                oci_execute($stmt);

                // Your PL/SQL block
                $sql = "BEGIN
                          FOR rec IN (SELECT street, city, postcode FROM DH_BRANCH WHERE branchNo = :branch_no) LOOP
                            DBMS_OUTPUT.PUT_LINE('Street: ' || rec.street || ', City: ' || rec.city || ', Postcode: ' || rec.postcode);
                          END LOOP;
                        END;";

                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':branch_no', $branchNumber);
                oci_execute($stmt);

                // Now fetch DBMS_OUTPUT results
                $outputStmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
                oci_bind_by_name($outputStmt, ":line", $line, 32767);
                oci_bind_by_name($outputStmt, ":status", $status);

                echo "<h4>Branch Details:</h4>";
                echo "<pre>";
                do {
                    oci_execute($outputStmt);
                    if ($status == 0) {
                        echo htmlentities($line) . "\n";
                    }
                } while ($status == 0);
                echo "</pre>";

                // Clean up
                oci_free_statement($stmt);
                oci_free_statement($outputStmt);
            }

            if (isset($conn)) {
                oci_close($conn);
            }
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
