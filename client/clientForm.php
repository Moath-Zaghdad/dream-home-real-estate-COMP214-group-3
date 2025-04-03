<?php
include 'db_credentials.php';
$conn = oci_connect($db_user, $db_password, $connection_string);
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Initialize form fields
$clientNo = '';
$fName = '';
$lName = '';
$address = '';
$city = '';
$telephone = '';
$email = '';
$prefType = '';
$maxRent = '';
$isEdit = false;

// Load existing client if clientNo is passed
if (isset($_GET['clientNo']) && !empty($_GET['clientNo'])) {
    $clientNo = $_GET['clientNo'];
    $isEdit = true;

    // Enable DBMS_OUTPUT
    $enable = oci_parse($conn, "BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
    oci_execute($enable);

    $plsql = "
    DECLARE
        v_fName     VARCHAR2(100);
        v_lName     VARCHAR2(100);
        v_address   VARCHAR2(100);
        v_city      VARCHAR2(100);
        v_tel       VARCHAR2(50);
        v_email     VARCHAR2(100);
        v_prefType  VARCHAR2(10);
        v_maxRent   NUMBER;
    BEGIN
        SELECT fName, lName, STREET, city, telNo, email, prefType, maxRent
        INTO v_fName, v_lName, v_address, v_city, v_tel, v_email, v_prefType, v_maxRent
        FROM DH_CLIENT
        WHERE clientNo = :clientNo;

        DBMS_OUTPUT.PUT_LINE(v_fName || ',' || v_lName || ',' || v_address || ',' || v_city || ',' || v_tel || ',' || v_email || ',' || v_prefType || ',' || v_maxRent);
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            DBMS_OUTPUT.PUT_LINE(',,,,,,,');
    END;
    ";

    $stmt = oci_parse($conn, $plsql);
    oci_bind_by_name($stmt, ':clientNo', $clientNo);
    oci_execute($stmt);

    $output = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
    oci_bind_by_name($output, ":line", $line, 32767);
    oci_bind_by_name($output, ":status", $status);
    oci_execute($output);

    if ($status == 0 && !empty($line)) {
        list($fName, $lName, $address, $city, $telephone, $email, $prefType, $maxRent) = explode(',', $line);
    }

    oci_free_statement($stmt);
    oci_free_statement($output);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientNo = $_POST['clientNo'];
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $telephone = $_POST['Telephone'];
    $email = $_POST['email'];
    $prefType = $_POST['prefType'];
    $maxRent = $_POST['maxRent'];

    $sql = "
    BEGIN
        MERGE INTO DH_CLIENT c
        USING (SELECT :clientNo AS clientNo FROM dual) d
        ON (c.clientNo = d.clientNo)
        WHEN MATCHED THEN
            UPDATE SET
                fName = :fName,
                lName = :lName,
                STREET = :address,
                city = :city,
                telNo = :telephone,
                email = :email,
                prefType = :prefType,
                maxRent = :maxRent
        WHEN NOT MATCHED THEN
            INSERT (clientNo, fName, lName, STREET, city, telNo, email, prefType, maxRent)
            VALUES (:clientNo, :fName, :lName, :address, :city, :telephone, :email, :prefType, :maxRent);
        COMMIT;
    END;
    ";

    echo "<pre>" . htmlentities($sql) . "</pre>";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':clientNo', $clientNo);
    oci_bind_by_name($stmt, ':fName', $fName);
    oci_bind_by_name($stmt, ':lName', $lName);
    oci_bind_by_name($stmt, ':address', $address);
    oci_bind_by_name($stmt, ':city', $city);
    oci_bind_by_name($stmt, ':telephone', $telephone);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':prefType', $prefType);
    oci_bind_by_name($stmt, ':maxRent', $maxRent);

    $result = oci_execute($stmt);
    if ($result) {
        echo "<p>Client saved successfully!</p>";
    } else {
        $e = oci_error($stmt);
        echo "<p>Error saving client: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
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
            <h3><?php echo $isEdit ? 'Edit Client' : 'Add New Client'; ?></h3>

            <form method="POST" action="">
                <label for="clientNo">Client Number:</label>
                <input type="text" id="clientNo" name="clientNo"
                    value="<?php echo htmlspecialchars($clientNo); ?>"
                    <?php echo $isEdit ? 'readonly' : ''; ?>>

                <label for="fName">First Name:</label>
                <input type="text" id="fName" name="fName" value="<?php echo htmlspecialchars($fName); ?>">

                <label for="lName">Last Name:</label>
                <input type="text" id="lName" name="lName" value="<?php echo htmlspecialchars($lName); ?>">

                <label for="address">Street Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">

                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">

                <label for="Telephone">Telephone:</label>
                <input type="text" id="Telephone" name="Telephone" value="<?php echo htmlspecialchars($telephone); ?>">

                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <label for="prefType">Preferred Property Type:</label>
                <select id="prefType" name="prefType">
                    <option value="">-- Select --</option>
                    <option value="Flat" <?php if ($prefType === 'Flat') echo 'selected'; ?>>Flat</option>
                    <option value="House" <?php if ($prefType === 'House') echo 'selected'; ?>>House</option>
                    <option value="Condo" <?php if ($prefType === 'Condo') echo 'selected'; ?>>Condo</option>
                </select>

                <label for="maxRent">Max Rent:</label>
                <input type="number" id="maxRent" name="maxRent" value="<?php echo htmlspecialchars($maxRent); ?>">

                <input type="submit" value="<?php echo $isEdit ? 'Update Client' : 'Add Client'; ?>">
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
