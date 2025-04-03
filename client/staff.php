<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4 - Staff</title>
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
        <h3>Staff Main Menu</h3>
        <div>
            <form action="" method="GET" id="staffForm">
                <label for="staffList">Employees:</label>
                <select name="staffList" id="staff">
                    <option value="">-- Select an Employee --</option>
                    <?php
                    // Include database credentials
                    include 'db_credentials.php';

                    // Establish connection to Oracle
                    $conn = oci_connect($db_user, $db_password, $connection_string);

                    if (!$conn) {
                        $e = oci_error();
                        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                    }

                    // PL/SQL query to fetch staff details
                    $sql = "BEGIN
                                 FOR rec IN (SELECT staffNo, fName, lName FROM DH_STAFF) LOOP
                                     DBMS_OUTPUT.PUT_LINE(rec.staffNo || ',' || rec.fName || ' ' || rec.lName);
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

                    $staff_members = explode("\n", trim($output));

                    foreach ($staff_members as $staff_info) {
                        if (!empty($staff_info)) {
                            list($staffNo, $fullName) = explode(',', $staff_info);
                            echo '<option value="' . htmlspecialchars($staffNo) . '">' . htmlspecialchars($fullName) . '</option>';
                        }
                    }

                    // Clean up
                    oci_free_statement($stmt);
                    oci_close($conn);
                    ?>
                </select>
                <div class="marginTop">
                    <button type="button" onclick="editStaff()">Edit Employee</button>
                    <a href="staffForm.php"><button type="button">New Employee</button></a>
                </div>
            </form>
        </div>

        <script>
            function editStaff() {
                const staffDropdown = document.getElementById('staff');
                const selectedStaffNo = staffDropdown.value;

                if (selectedStaffNo) {
                    window.location.href = 'staffForm.php?staffNo=' + selectedStaffNo;
                } else {
                    alert('Please select an employee to edit.');
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
