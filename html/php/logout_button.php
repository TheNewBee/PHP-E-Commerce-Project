<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_REQUEST['nonce']) && $_REQUEST['nonce'] != $_SESSION['nonce']){
	echo json_encode(array('failed'=>'nonce not match'));
	exit();
}

$status = '';
$content = '<div class="loginout">';
if(isset($_SESSION["Accept"])){
    $content .= '<a rel="noopener noreferrer" href="login.php">Profile</a>';
    $status .= 'logout';
}else{
    $content .='<p>Guest</p>';
    $status = 'login';
}
$content .= '</br>';
$content .='<a rel="noopener noreferrer" href="'.$status.'.php">'.$status.'</a>';
$content .='</div>';
?>