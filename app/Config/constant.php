<?php
$config = array('constant' => array(
    'Something' => 1234,
    'Foo' => 'Bar',
));

define("LOGINNOTSUCCESSFULL", "Invalid username or password, try again");
define("LOGINSUCCESSFULL", "Welcome to your account, You are logged In successfully.");
define("FORGETMAILSENT", "Please check your email address for new password");
define("WRONGEMAIL", "Email address is not registered in our system, Please check again.");
define("PROFILEUPDATED", "Profile has been updated Successfully.");
define("PROFILENOTUPDATED", "Profile not updated Successfully, please try again.");

$protocol = "http://";
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $protocol = "https://";
}
define('HTTP_ROOT',$protocol.$_SERVER['HTTP_HOST'].'/');
define('ADMIN_EMAIL','sanchitn.sdd@gmail.com');

//----Template Constant-------------------//
define('USER_REGISTRATION','customer_registration');
define('FORGET_PASSWORD_CUTOMER','customer_forget_password');
define('CUSTOME_DINEIN_REQUEST','customer_dine_in_request');
define('ORDER_RECEIPT','order_receipt');



define('BASE_URL', $_SERVER['HTTP_HOST']);

?>
