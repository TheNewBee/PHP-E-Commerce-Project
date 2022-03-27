<?php
session_start();

if(isset($_SESSION['Accept'])){
    session_destroy();
    unset($_COOKIE['auth']); 
    setcookie("auth", NULL, -1, '/', "s65.ierg4210.ie.cuhk.edu.hk", TRUE, TRUE); 
    header("Location: ../main.php");
    exit();
}else{
    echo 'You have not logged in. <br/><a href="javascript:history.back();">Back to login.</a>';
}
?>