<?php

// Include database credentials
include 'db_credentials.php';

// Establish connection to Oracle
$conn = oci_connect($db_user, $db_password, $connection_string);

if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

function tableExists($conn, $tableName) {
    $query = "SELECT table_name FROM user_tables WHERE table_name = UPPER('" . $tableName . "')";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    $exists = (oci_fetch_row($stmt)) ? true : false;
    oci_free_statement($stmt);
    return $exists;
}

// SQL statements to create the tables
$createTableStatements = [
    "DH_BRANCH" => "CREATE TABLE DH_BRANCH (
        BRANCHNO VARCHAR2(50 BYTE) PRIMARY KEY,
        STREET VARCHAR2(50 BYTE),
        CITY VARCHAR2(50 BYTE),
        POSTCODE VARCHAR2(50 BYTE)
    )",
    "DH_CLIENT" => "CREATE TABLE DH_CLIENT (
        CLIENTNO VARCHAR2(50 BYTE) PRIMARY KEY,
        FNAME VARCHAR2(30 BYTE),
        LNAME VARCHAR2(30 BYTE),
        TELNO CHAR(20 BYTE),
        STREET VARCHAR2(30 BYTE),
        CITY VARCHAR2(30 BYTE),
        EMAIL VARCHAR2(40 BYTE),
        PREFTYPE VARCHAR2(5 BYTE),
        MAXRENT NUMBER(38,0)
    )",
    "DH_LEASE" => "CREATE TABLE DH_LEASE (
        LEASENO NUMBER(7,0) PRIMARY KEY,
        CLIENTNO VARCHAR2(50 BYTE),
        PROPERTYNO VARCHAR2(10 BYTE),
        LEASEAMOUNT NUMBER(9,2),
        LEASE_START DATE,
        LEASE_END DATE
    )",
    "DH_PRIVATEOWNER" => "CREATE TABLE DH_PRIVATEOWNER (
        OWNERNO CHAR(5 BYTE) PRIMARY KEY,
        FNAME VARCHAR2(10 BYTE),
        LNAME VARCHAR2(10 BYTE),
        ADDRESS VARCHAR2(50 BYTE),
        TELNO CHAR(15 BYTE),
        EMAIL VARCHAR2(50 BYTE),
        PASSWORD VARCHAR2(40 BYTE)
    )",
    "DH_PROPERTYFORRENT" => "CREATE TABLE DH_PROPERTYFORRENT (
        PROPERTYNO VARCHAR2(10 BYTE) PRIMARY KEY,
        STREET VARCHAR2(50 BYTE),
        CITY VARCHAR2(50 BYTE),
        POSTCODE VARCHAR2(50 BYTE),
        TYPE VARCHAR2(50 BYTE),
        ROOMS NUMBER(7,0),
        RENT NUMBER(7,0),
        OWNERNO CHAR(5 BYTE) NOT NULL,
        STAFFNO VARCHAR2(50 BYTE) NOT NULL,
        BRANCHNO VARCHAR2(50 BYTE),
        PICTURE VARCHAR2(50 BYTE),
        FLOORPLAN VARCHAR2(100 BYTE),
        FOREIGN KEY (OWNERNO) REFERENCES DH_PRIVATEOWNER(OWNERNO),
        FOREIGN KEY (STAFFNO) REFERENCES DH_STAFF(STAFFNO),
        FOREIGN KEY (BRANCHNO) REFERENCES DH_BRANCH(BRANCHNO)
    )",
    "DH_REGISTRATION" => "CREATE TABLE DH_REGISTRATION (
        CLIENTNO VARCHAR2(50 BYTE) NOT NULL,
        BRANCHNO VARCHAR2(50 BYTE) NOT NULL,
        STAFFNO VARCHAR2(50 BYTE),
        DATEREGISTER DATE,
        PRIMARY KEY (CLIENTNO, BRANCHNO),
        FOREIGN KEY (CLIENTNO) REFERENCES DH_CLIENT(CLIENTNO),
        FOREIGN KEY (BRANCHNO) REFERENCES DH_BRANCH(BRANCHNO),
        FOREIGN KEY (STAFFNO) REFERENCES DH_STAFF(STAFFNO)
    )",
    "DH_STAFF" => "CREATE TABLE DH_STAFF (
        STAFFNO VARCHAR2(50 BYTE) PRIMARY KEY,
        FNAME VARCHAR2(50 BYTE),
        LNAME VARCHAR2(50 BYTE),
        POSITION VARCHAR2(50 BYTE),
        SEX VARCHAR2(50 BYTE),
        DOB DATE,
        SALARY NUMBER(7,0),
        BRANCHNO VARCHAR2(50 BYTE),
        TELEPHONE VARCHAR2(16 BYTE),
        MOBILE VARCHAR2(16 BYTE),
        EMAIL VARCHAR2(50 BYTE),
        FOREIGN KEY (BRANCHNO) REFERENCES DH_BRANCH(BRANCHNO)
    )",
    "DH_VIEWING" => "CREATE TABLE DH_VIEWING (
        CLIENTNO VARCHAR2(50 BYTE) NOT NULL,
        PROPERTYNO VARCHAR2(10 BYTE) NOT NULL,
        VIEWDATE DATE,
        COMMENTS VARCHAR2(200 BYTE),
        PRIMARY KEY (CLIENTNO, PROPERTYNO),
        FOREIGN KEY (CLIENTNO) REFERENCES DH_CLIENT(CLIENTNO),
        FOREIGN KEY (PROPERTYNO) REFERENCES DH_PROPERTYFORRENT(PROPERTYNO)
    )"
];

// Check for delete_all parameter
if (isset($_GET['delete_all']) && $_GET['delete_all'] === 'yes') {
    echo "<p style='color: red;'>Warning: You are about to delete all database tables. Please confirm by adding '&confirm_delete=yes' to the URL.</p>";
    if (isset($_GET['confirm_delete']) && $_GET['confirm_delete'] === 'yes') {
        $tablesToDelete = array_keys($createTableStatements);
        $deletedCount = 0;
        foreach ($tablesToDelete as $tableName) {
            $dropQuery = "DROP TABLE " . $tableName;
            $stmt = oci_parse($conn, $dropQuery);
            $result = oci_execute($stmt);
            if ($result) {
                echo "<p style='color: green;'>Table '" . htmlspecialchars($tableName) . "' deleted successfully.</p>";
                $deletedCount++;
            } else {
                $e = oci_error($stmt);
                echo "<p style='color: red;'>Error deleting table '" . htmlspecialchars($tableName) . "': " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
            }
            oci_free_statement($stmt);
        }
        echo "<p>Total tables deleted: " . $deletedCount . "</p>";
    }
} else {
    $createdCount = 0;
    foreach ($createTableStatements as $tableName => $sql) {
        if (!tableExists($conn, $tableName)) {
            $stmt = oci_parse($conn, $sql);
            $result = oci_execute($stmt);
            if ($result) {
                echo "<p style='color: green;'>Table '" . htmlspecialchars($tableName) . "' created successfully.</p>";
                $createdCount++;
            } else {
                $e = oci_error($stmt);
                echo "<p style='color: red;'>Error creating table '" . htmlspecialchars($tableName) . "': " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
            }
            oci_free_statement($stmt);
        } else {
            echo "<p>Table '" . htmlspecialchars($tableName) . "' already exists.</p>";
        }
    }
    echo "<p>Total tables created: " . $createdCount . "</p>";
}

oci_close($conn);

?>
