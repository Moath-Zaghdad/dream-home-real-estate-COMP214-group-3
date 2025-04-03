<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Edit Branches</title>
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
            <h3>Edit Branches</h3>
            <table>
                <tr>
                    <th>Branch No.</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Postal Code</th>
                    <th>Action</th>
                </tr>
                    <?php
                    // Include database credentials
                    include 'db_credentials.php';

                    // Establish connection to Oracle
                    $conn = oci_connect($db_user, $db_password, $connection_string);

                    if (!$conn) {
                        $e = oci_error();
                        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                    }

                    // Enable DBMS_OUTPUT
                    $stmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
                    oci_execute($stmt);

                    // Your PL/SQL block with DBMS_OUTPUT
                    $plsql = "
                    BEGIN
                        FOR rec IN (SELECT branchNo, street, city, postcode FROM DH_BRANCH ORDER BY branchNo) LOOP
                            DBMS_OUTPUT.PUT_LINE(rec.branchNo || ',' || rec.street || ',' || rec.city || ',' || rec.postcode);
                        END LOOP;
                    END;
                    ";

                    $stmt = oci_parse($conn, $plsql);
                    oci_execute($stmt);

                    // Now fetch the lines from DBMS_OUTPUT
                    $output_stmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
                    oci_bind_by_name($output_stmt, ":line", $line, 32767);
                    oci_bind_by_name($output_stmt, ":status", $status);

                    while (true) {
                        oci_execute($output_stmt);
                        if ($status != 0) break;

                        list($branchNo, $street, $city, $postcode) = explode(',', $line);
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($branchNo) . '</td>';
                        echo '<td>' . htmlspecialchars($street) . '</td>';
                        echo '<td>' . htmlspecialchars($city) . '</td>';
                        echo '<td>' . htmlspecialchars($postcode) . '</td>';
                        echo '<td><a href="branchForm.php?branchNo=' . urlencode($branchNo) . '"><button>Edit</button></a></td>';
                        echo '</tr>';
                    }

                    // Cleanup
                    oci_free_statement($stmt);
                    oci_free_statement($output_stmt);
                    oci_close($conn);
                    ?>

            </table>

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
