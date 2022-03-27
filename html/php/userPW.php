<?php
require '../lib/db.inc.php';

$curr = (string) $_POST['curr_password'];
$new = (string) $_POST['new_password'];
$email = (string) test_input($_POST['email']);
$nonce = (string) $_POST['nonce'];

if($_REQUEST['nonce'] && $_REQUEST['nonce'] != $_SESSION['nonce']){
	echo json_encode(array('failed'=>'nonce not match'));
	exit();
}

ierg4210_user_change_pw($curr, $new, $email);

exit;
?>