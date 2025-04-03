<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Clients</title>
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
            <h3>Client Main Menu</h3>
            <form method="GET" action="">
                <label for="clientList">Clients:</label>
                <select name="clientList" id="clientList">
                    <option value="">-- Select a Client --</option>
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
                    $enable = oci_parse($conn, "BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
                    oci_execute($enable);

                    // PL/SQL block to output all clients
                    $sql = "
                    BEGIN
                        FOR rec IN (SELECT clientNo, fName, lName FROM DH_CLIENT ORDER BY clientNo) LOOP
                            DBMS_OUTPUT.PUT_LINE(rec.clientNo || ',' || rec.fName || ' ' || rec.lName);
                        END LOOP;
                    END;
                    ";

                    echo "<pre>" . htmlentities($sql) . "</pre>";

                    $stmt = oci_parse($conn, $sql);
                    oci_execute($stmt);

                    // Prepare to fetch lines from DBMS_OUTPUT
                    $output_stmt = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
                    oci_bind_by_name($output_stmt, ":line", $line, 32767);
                    oci_bind_by_name($output_stmt, ":status", $status);

                    // Loop and build dropdown options
                    while (true) {
                        oci_execute($output_stmt);
                        if ($status != 0) break;

                        if (!empty($line)) {
                            list($clientNo, $fullName) = explode(',', $line);
                            echo '<option value="' . htmlspecialchars($clientNo) . '">' . htmlspecialchars($fullName) . '</option>';
                        }
                    }

                    // Clean up
                    oci_free_statement($stmt);
                    oci_free_statement($output_stmt);
                    oci_close($conn);
                    ?>
                </select>

                <div class="marginTop">
                    <button type="button" onclick="editClient()">Edit Client</button>
                    <a href="clientForm.php"><button type="button">New Client</button></a>
                </div>
            </form>
        </div>

        <script>
            function editClient() {
                const clientDropdown = document.getElementById('clientList');
                const selectedClientNo = clientDropdown.value;

                if (selectedClientNo) {
                    window.location.href = 'clientForm.php?clientNo=' + selectedClientNo;
                } else {
                    alert('Please select a client to edit.');
                }
            }
        </script>
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
