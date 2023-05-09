<?php
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

// session_start();
// if (session_status() == PHP_SESSION_NONE) {
// }
session_start();

include 'db_login_detail.php';


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);


/// Added To Set Cookiee Same Site Attribute-
header('Set-Cookie: cookie1=value1; SameSite=Lax', false);
header('Set-Cookie: cookie2=value2; SameSite=None; Secure', false);

header("Pragma: no-cache");
?>
