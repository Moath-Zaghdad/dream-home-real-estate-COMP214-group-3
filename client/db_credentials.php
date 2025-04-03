<?php

$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');

$connection_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$db_host})(PORT={$db_port}))(CONNECT_DATA=(SERVICE_NAME={$db_name})))";

?>
