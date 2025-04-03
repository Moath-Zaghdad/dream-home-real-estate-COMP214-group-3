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

                // PL/SQL query to fetch all branch details
                $sql = "BEGIN
                                 FOR rec IN (SELECT branchNo, street, city, postcode FROM DH_BRANCH) LOOP
                                     DBMS_OUTPUT.PUT_LINE(rec.branchNo || ',' || rec.street || ',' || rec.city || ',' || rec.postcode);
                                 END LOOP;
                             END;";

                echo "";
                echo "<pre>";
                echo htmlentities($sql);
                echo "</pre>";

                // Prepare the statement
                $stmt = oci_parse($conn, $sql);

                // Enable DBMS_OUTPUT
                oci_set_prefetch($stmt, 1000);
                oci_exec($stmt, OCI_DEFAULT);

                // Fetch DBMS_OUTPUT lines
                $output = '';
                oci_set_output($conn, 100000, $output_length);
                oci_execute($stmt);

                $branches = explode("\n", trim($output));

                foreach ($branches as $branch_info) {
                    if (!empty($branch_info)) {
                        list($branchNo, $street, $city, $postcode) = explode(',', $branch_info);
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($branchNo) . '</td>';
                        echo '<td>' . htmlspecialchars($street) . '</td>';
                        echo '<td>' . htmlspecialchars($city) . '</td>';
                        echo '<td>' . htmlspecialchars($postcode) . '</td>';
                        echo '<td><a href="branchForm.php?branchNo=' . htmlspecialchars($branchNo) . '"><button>Edit</button></a></td>';
                        echo '</tr>';
                    }
                }

                // Clean up
                oci_free_statement($stmt);
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
