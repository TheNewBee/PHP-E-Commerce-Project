<?php
require __DIR__.'/lib/db.inc.php';
require __DIR__.'/php/content_security_policy.php';
if(isset($_SESSION['Accept'])){

}else{
    header("Location: ../login.php");
}
$nonce = hash('sha512', time());
$_SESSION['nonce'] = $nonce;

?>

<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/main_link.css">
</head>
<body>
    <div class="main"><a rel="noopener noreferrer" href="main.php">Shop Main Page</a></div>
    </br>
    <fieldset class="border border-dark rounded">
        <div class="border form-margin">
        <legend> Change Password</legend>
        <form class="form-group" id="user_change_pw" method="POST" action="/php/userPW.php">
            <label for="email"> Email *</label>
            <div> <input id="email" class="form-control" type="email" name="email" placeholder="abc@email.com" required="required" pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$"/> </div>
            <label for="password"> Current Password *</label>
            <div> <input class="form-control" type="password" placeholder="password" name="curr_password" required="required"/></div>
            <label for="password"> New Password *</label>
            <div> <input class="form-control" type="password" placeholder="password" name="new_password" required="required"/></div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>            
        </form>
        </div>
    </fieldset>
</body>
</html>