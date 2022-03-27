<?php
function CheckAccess()
{
    // Validate the token when accessing admin page

    if(!authToken())
    {
        header('Location: ../login.php');
    }
}

function authToken(){
    $result = (isset($_COOKIE['auth']) &&isset($_SESSION['Accept']) && $_SESSION['Accept'] == $_COOKIE['auth'] );
    return $result;
}
?>