<?php
define("DB_HOST", "localhost");
define("DB_USER", "mifactura");
define("DB_PASS", "fac9845o");
define("DB_MASTER", "cfd_master");

if(!isset($_SESSION['dbcorp'])){
	define("DB_NAME", "cfd_master");
} else {
	define("DB_NAME", $_SESSION['dbcorp']);
}
?>