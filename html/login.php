<?php
require __DIR__.'/php/content_security_policy.php';
require __DIR__.'/lib/db.inc.php';
$nonce = hash('sha512', time());
$_SESSION['nonce'] = $nonce;
$login_page = '';
if(isset($_SESSION['Accept'])){
  $login_page .= '<div class="fadeIn first">
  <img src="/image/login.png" id="icon" alt="User Icon" />
  </div>

  <form method="POST" action="changePW.php">
  <input type="hidden" name="nonce" value="'.$nonce.'" />
  <input type="submit" class="fadeIn fourth" value="Change Password">
  </form>

  <form method="POST" action="logout.php">
  <input type="hidden" name="nonce" value="'.$nonce.'" />
  <input type="submit" class="fadeIn fourth" value="Log Out">
  </form>';
}else{
  $login_page .= '<div class="fadeIn first">
  <img src="/image/login.png" id="icon" alt="User Icon" />
  </div>
  <form method="POST" action="/php/userLogin.php">
    <input type="email" id="email" class="fadeIn second" name="email" placeholder="email" required="required" pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$">
    <input type="password" id="passwd" class="fadeIn third" name="passwd" placeholder="password" required="required">
    <input type="hidden" name="nonce" value="'.$nonce.'" />
    <input type="submit" class="fadeIn fourth" value="Log In">
  </form>
  <form method="POST" action="register.php">
  <input type="hidden" name="nonce" value="'.$nonce.'" />
  <input type="submit" class="fadeIn fourth" value="Register">
  </form>';
}
?>
<html>
  <body>
  <div class="main"><a rel="noopener noreferrer" href="main.php">Shop Main Page</a></div>
    <div class="wrapper fadeInDown">
      <div id="formContent">
        <?php echo $login_page ?>
      </div>
    </div>
</body>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/css/login.css">
<link rel="stylesheet" type="text/css" href="/css/main_link.css">
</head>
</html>