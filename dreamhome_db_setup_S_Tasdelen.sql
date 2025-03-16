-- dreamhome_db_setup_S_Tasdelen.sql
-- 
-- This SQL file contains stored procedures for managing staff and branch records
-- in the Dream Home database. It includes procedures for inserting, updating, 
-- and retrieving data while enforcing integrity constraints.

-- =====================================================================
-- STORED PROCEDURES INCLUDED:
-- =====================================================================
-- Staff_hire_sp: Inserts a new staff member while ensuring the staff number is unique.
-- Staff_update_sp: Updates salary, phone, and email for a staff member.
-- Get_branch_address: Retrieves the full address of a branch.
-- Update_branch_sp: Updates the details of an existing branch.
-- Add_branch_sp: Adds a new branch if it does not already exist.
-- =====================================================================

-- USAGE INSTRUCTIONS:
-- 1. Open SQL Developer or any compatible database tool.
-- 2. Ensure you are connected to the Dream Home database.
-- 3. Execute this script to create all stored procedures.
-- 4. Verify successful execution with:
--    SELECT OBJECT_NAME FROM USER_PROCEDURES;
-- 5. Test procedures using EXEC commands as needed.
--
-- Example:
-- EXEC Staff_hire_sp('S002', 'Jane', 'Smith', 'Assistant', 'F', TO_DATE('1990-08-25','YYYY-MM-DD'), 4500, 'B002', '111223344', '999888777', 'jane.smith@email.com');
-- EXEC Get_branch_address('B002');

-- =====================================================================
-- ADDITIONAL NOTES FOR BACKEND INTEGRATION:
-- =====================================================================
-- These procedures are designed to be used directly in SQL queries or called from 
-- backend code using cx_Oracle or another database interaction library.
-- 
-- Staff_hire_sp and Add_branch_sp check for existing records before insertion.
-- Staff_update_sp ensures that only existing records can be modified.
-- Get_branch_address returns a formatted address or an error if the branch is not found.
--
-- If additional queries or modifications are required for backend integration, 
-- inform the database team before making changes.
--
-- =====================================================================
-- END OF DOCUMENTATION. PROCEED WITH EXECUTION.
-- =====================================================================



--Write the Stored Procedure
CREATE OR REPLACE PROCEDURE Staff_hire_sp (
    p_staffno IN VARCHAR2,
    p_fname IN VARCHAR2,
    p_lname IN VARCHAR2,
    p_position IN VARCHAR2,
    p_sex IN VARCHAR2,
    p_dob IN DATE,
    p_salary IN NUMBER,
    p_branchno IN VARCHAR2,
    p_telephone IN VARCHAR2,
    p_mobile IN VARCHAR2,
    p_email IN VARCHAR2
) AS
    v_count NUMBER;
BEGIN
    -- Check if StaffNo already exists
    SELECT COUNT(*) INTO v_count FROM DH_STAFF WHERE STAFFNO = p_staffno;
    
    IF v_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error: Staff already exists.');
    ELSE
        -- Insert new staff member
        INSERT INTO DH_STAFF (STAFFNO, FNAME, LNAME, POSITION, SEX, DOB, SALARY, BRANCHNO, TELEPHONE, MOBILE, EMAIL)
        VALUES (p_staffno, p_fname, p_lname, p_position, p_sex, p_dob, p_salary, p_branchno, p_telephone, p_mobile, p_email);
        
        COMMIT;
    END IF;
END;
/
EXEC Staff_hire_sp('S001', 'John', 'Doe', 'Manager', 'M', TO_DATE('1985-06-15', 'YYYY-MM-DD'), 5500, 'B001', '123456789', '987654321', 'john.doe@example.com');

SELECT * FROM DH_BRANCH WHERE BRANCHNO = 'B001';

INSERT INTO DH_BRANCH (BRANCHNO, STREET, CITY, POSTCODE)
VALUES ('B001', 'Main Street', 'New York', '10001');

COMMIT;

--Staff Hiring Procedure
EXEC Staff_hire_sp('S001', 'John', 'Doe', 'Manager', 'M', TO_DATE('1985-06-15', 'YYYY-MM-DD'), 5500, 'B001', '123456789', '987654321', 'john.doe@example.com');

SELECT * FROM DH_STAFF WHERE STAFFNO = 'S001';

--Create the Staff Update Procedure
CREATE OR REPLACE PROCEDURE Staff_update_sp (
    p_staffno IN VARCHAR2,
    p_salary IN NUMBER,
    p_telephone IN VARCHAR2,
    p_mobile IN VARCHAR2,
    p_email IN VARCHAR2
) AS
    v_count NUMBER;
BEGIN
    -- Check if staff exists
    SELECT COUNT(*) INTO v_count FROM DH_STAFF WHERE STAFFNO = p_staffno;
    
    IF v_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20002, 'Error: Staff does not exist.');
    ELSE
        -- Update salary, phone, and email
        UPDATE DH_STAFF
        SET SALARY = p_salary,
            TELEPHONE = p_telephone,
            MOBILE = p_mobile,
            EMAIL = p_email
        WHERE STAFFNO = p_staffno;
        
        COMMIT;
    END IF;
END;
/

--Update Procedure
EXEC Staff_update_sp('S001', 6000, '111222333', '999888777', 'john.doe@newemail.com');
--Verify the Update
SELECT * FROM DH_STAFF WHERE STAFFNO = 'S001';

--Create a Function to Retrieve Branch Address
CREATE OR REPLACE FUNCTION get_branch_address (
    p_branchno IN VARCHAR2
) RETURN VARCHAR2 AS
    v_address VARCHAR2(200);
BEGIN
    -- Fetch address if branch exists
    SELECT STREET || ', ' || CITY || ', ' || POSTCODE 
    INTO v_address 
    FROM DH_BRANCH 
    WHERE BRANCHNO = p_branchno;
    
    RETURN v_address;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN 'Error: Branch not found.';
END;
/
--Test the Function-true
SELECT get_branch_address('B001') FROM dual;
--Test the Function-false
SELECT get_branch_address('B999') FROM dual;

--Create a Procedure to Update a Branch
CREATE OR REPLACE PROCEDURE Update_branch_sp (
    p_branchno IN VARCHAR2,
    p_street IN VARCHAR2,
    p_city IN VARCHAR2,
    p_postcode IN VARCHAR2
) AS
    v_count NUMBER;
BEGIN
    -- Check if branch exists
    SELECT COUNT(*) INTO v_count FROM DH_BRANCH WHERE BRANCHNO = p_branchno;
    
    IF v_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20003, 'Error: Branch does not exist.');
    ELSE
        -- Update branch details
        UPDATE DH_BRANCH
        SET STREET = p_street,
            CITY = p_city,
            POSTCODE = p_postcode
        WHERE BRANCHNO = p_branchno;
        
        COMMIT;
    END IF;
END;
/
-- COMPLETED SO FAR: 
--Staff Hiring Procedure (Staff_hire_sp) 
--Staff Update Procedure (Staff_update_sp)
--Branch Address Retrieval Function (get_branch_address)
--Branch Update Procedure (Update_branch_sp)


EXEC Update_branch_sp('B001', 'New Street', 'Los Angeles', '90001');

SELECT * FROM DH_BRANCH WHERE BRANCHNO = 'B001';


--Create a Procedure to Add a New Branch

CREATE OR REPLACE PROCEDURE Add_branch_sp (
    p_branchno IN VARCHAR2,
    p_street IN VARCHAR2,
    p_city IN VARCHAR2,
    p_postcode IN VARCHAR2
) AS
    v_count NUMBER;
BEGIN
    -- Check if branch already exists
    SELECT COUNT(*) INTO v_count FROM DH_BRANCH WHERE BRANCHNO = p_branchno;
    
    IF v_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20004, 'Error: Branch already exists.');
    ELSE
        -- Insert new branch
        INSERT INTO DH_BRANCH (BRANCHNO, STREET, CITY, POSTCODE)
        VALUES (p_branchno, p_street, p_city, p_postcode);
        
        COMMIT;
    END IF;
END;
/

EXEC Add_branch_sp('B002', 'Sunset Boulevard', 'San Francisco', '94110');

SELECT * FROM DH_BRANCH WHERE BRANCHNO = 'B002';

EXEC Add_branch_sp('B003', 'Broadway', 'New York', '10003');

SELECT * FROM DH_BRANCH WHERE BRANCHNO = 'B003';

EXEC Add_branch_sp('B004', 'Fifth Avenue', 'New York', '10011');

EXEC Add_branch_sp('B999', 'Ocean Drive', 'Miami', '39130');

SELECT * FROM DH_BRANCH WHERE BRANCHNO = 'B999';

--COMPLETED SO FAR:
-- Created and tested stored procedures for:
--- Inserting a new staff member (Staff_hire_sp)
--- Updating staff salary, phone, and email (Staff_update_sp)
--- Fetching a branch address (Get_branch_address)
--- Updating branch details (Update_branch_sp)
--- Adding a new branch (Add_branch_sp)
-- Verified error handling for duplicate entries
-- Ensured database integrity constraints are enforced
-- Checked data retrieval with SELECT queries

