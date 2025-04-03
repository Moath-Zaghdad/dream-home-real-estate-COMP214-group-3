<?php
include 'db_credentials.php';
$conn = oci_connect($db_user, $db_password, $connection_string);

if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Initialize variables
$staffNo = '';
$fName = '';
$lName = '';
$position = '';
$sex = '';
$branchNo = '';
$dob = '';
$salary = '';
$telephone = '';
$mobile = '';
$email = '';
$isEdit = false;

// If editing, load the staff data via PL/SQL
if (isset($_GET['staffNo']) && !empty($_GET['staffNo'])) {
    $staffNo = $_GET['staffNo'];
    $isEdit = true;

    $enable = oci_parse($conn, "BEGIN DBMS_OUTPUT.ENABLE(NULL); END;");
    oci_execute($enable);

    $plsql = "
    DECLARE
        v_fname     VARCHAR2(100);
        v_lname     VARCHAR2(100);
        v_position  VARCHAR2(100);
        v_sex       VARCHAR2(10);
        v_branch    VARCHAR2(10);
        v_dob       VARCHAR2(100);
        v_salary    NUMBER;
        v_tel       VARCHAR2(100);
        v_mobile    VARCHAR2(100);
        v_email     VARCHAR2(100);
    BEGIN
        SELECT fName, lName, position, sex, TO_CHAR(dob, 'YYYY-MM-DD'), salary, branchNo, telephone, mobile, email
        INTO v_fname, v_lname, v_position, v_sex, v_dob, v_salary, v_branch, v_tel, v_mobile, v_email
        FROM DH_STAFF
        WHERE staffNo = :staffNo;

        DBMS_OUTPUT.PUT_LINE(v_fname || ',' || v_lname || ',' || v_position || ',' || v_sex || ',' || v_branch || ',' || v_dob || ',' || v_salary || ',' || v_tel || ',' || v_mobile || ',' || v_email);
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            DBMS_OUTPUT.PUT_LINE(',,,,,,,,,');
    END;
    ";

    $stmt = oci_parse($conn, $plsql);
    oci_bind_by_name($stmt, ':staffNo', $staffNo);
    oci_execute($stmt);

    $out = oci_parse($conn, "BEGIN DBMS_OUTPUT.GET_LINE(:line, :status); END;");
    oci_bind_by_name($out, ":line", $line, 32767);
    oci_bind_by_name($out, ":status", $status);
    oci_execute($out);

    if ($status == 0 && !empty($line)) {
        list($fName, $lName, $position, $sex, $branchNo, $dob, $salary, $telephone, $mobile, $email) = explode(',', $line);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $sql = "
    BEGIN
        MERGE INTO DH_STAFF s
        USING (SELECT :staffNo AS staffNo FROM dual) d
        ON (s.staffNo = d.staffNo)
        WHEN MATCHED THEN
            UPDATE SET
                fName = :fName,
                lName = :lName,
                position = :position,
                sex = :sex,
                dob = TO_DATE(:dob, 'YYYY-MM-DD'),
                salary = :salary,
                branchNo = :branchNo,
                telephone = :telephone,
                mobile = :mobile,
                email = :email
        WHEN NOT MATCHED THEN
            INSERT (staffNo, fName, lName, position, sex, dob, salary, branchNo, telephone, mobile, email)
            VALUES (:staffNo, :fName, :lName, :position, :sex, TO_DATE(:dob, 'YYYY-MM-DD'), :salary, :branchNo, :telephone, :mobile, :email);
        COMMIT;
    END;
    ";

    $stmt = oci_parse($conn, $sql);
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

    $result = oci_execute($stmt);

    if ($result) {
        echo "<p>Staff member saved successfully!</p>";
    } else {
        $e = oci_error($stmt);
        echo "<p>Error saving staff member: " . htmlentities($e['message']) . "</p>";
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
            <h3><?php echo $isEdit ? 'Edit Staff Member' : 'Add New Staff Member'; ?></h3>

            <form method="POST" action="">
                <label for="staffNo">Staff Number:</label>
                <input type="text" id="staffNo" name="staffNo" value="<?php echo htmlspecialchars($staffNo); ?>" <?php echo $isEdit ? 'readonly' : ''; ?>>

                <label for="fName">First Name:</label>
                <input type="text" id="fName" name="fName" value="<?php echo htmlspecialchars($fName); ?>">

                <label for="lName">Last Name:</label>
                <input type="text" id="lName" name="lName" value="<?php echo htmlspecialchars($lName); ?>">

                <label for="position">Position:</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($position); ?>">

                <label for="sex">Sex:</label>
                <input type="text" id="sex" name="sex" value="<?php echo htmlspecialchars($sex); ?>">

                <label for="branchNo">Branch Number:</label>
                <input type="text" id="branchNo" name="branchNo" value="<?php echo htmlspecialchars($branchNo); ?>">

                <label for="dob">Date of Birth (YYYY-MM-DD):</label>
                <input type="text" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>">

                <label for="salary">Salary:</label>
                <input type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>">

                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>">

                <label for="mobile">Mobile:</label>
                <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>">

                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <input type="submit" value="<?php echo $isEdit ? 'Update Staff Member' : 'Add Staff Member'; ?>">
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
