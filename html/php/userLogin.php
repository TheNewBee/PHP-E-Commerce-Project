<?php
require '../lib/db.inc.php';

$email = (string) (test_input($_POST['email']));
$passwd = (string) $_POST['passwd'];
$nonce = (string) $_POST['nonce'];

if($_REQUEST['nonce'] && $_REQUEST['nonce'] != $_SESSION['nonce']){
	echo json_encode(array('failed'=>'nonce not match'));
	exit();
}

ierg4210_login_verify($email, $passwd, $nonce);

exit;
?>