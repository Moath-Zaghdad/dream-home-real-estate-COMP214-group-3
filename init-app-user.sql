-- SHOW PDBS;
-- Switch to the pluggable DB
ALTER SESSION SET CONTAINER = ORCLPDB1;

-- Create the application user
CREATE USER my_app_user IDENTIFIED BY my_secure_password;

-- Grant basic privileges
GRANT CONNECT, RESOURCE TO my_app_user;



-- Allow the user to use the USERS tablespace
ALTER USER my_app_user QUOTA 100M ON USERS;
