<?php
session_start();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db;
}

function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute()){
        return $q->fetchAll();
	}
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // input validation or sanitization
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // TODO: complete the rest of the INSERT command
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
	$_POST['name'] = (string) $_POST['name'];
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
	$_POST['price'] = (float) $_POST['price'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['description']))
        throw new Exception("invalid-text");
	$_POST['description'] = (string) $_POST['description'];

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if ($_FILES["file"]["error"] == 0
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/webp")
        && (mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg" || mime_content_type($_FILES["file"]["tmp_name"]) == "image/webp")
        && $_FILES["file"]["size"] < 5000000) {

        $catid = test_input($_POST["catid"]);
        $name = test_input($_POST["name"]);
        $price = test_input($_POST["price"]);
        $desc = test_input($_POST["description"]);
        $sql="INSERT INTO products (catid, name, price, description) VALUES (:catid, :name, :price, :desc);";
        $q = $db->prepare($sql);
        $q->bindParam(":catid", $catid);
        $q->bindParam(":name", $name);
        $q->bindParam(":price", $price);
        $q->bindParam(":desc", $desc);
        $q->execute();
        $lastId = $db->lastInsertId();
        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/admin/lib/images/" . $lastId . ".jpg")) {
            // redirect back to original page; you may comment it during debug
            header('Location: admin.php');        
            echo readfile("/var/www/html/admin/lib/images/");        
        }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_user_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $uid = 0;

    if (!preg_match('/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',  test_input($_POST['email'])))
        throw new Exception("invalid-email");
	$_POST['uid'] = (string) $_POST['uid'];
    $_POST['email'] = (string) $_POST['email'];
    $_POST['password'] = (string) $_POST['password'];
    
    $role = (string)test_input($_POST["uid"]);
    if($role == 'user1'){
        $uid = 1;
    }else if($role == 'user2'){
        $uid = 0;
    }
	$email = (string)test_input($_POST["email"]);

    // Check if email has been registered
    $sql="SELECT * FROM USER WHERE email = :email;";
    $q = $db->prepare($sql);
    $q->bindParam(":email", $email);
	if ($q->execute()){
        if(sizeof($q->fetchAll()) > 0){
            header('Content-Type: text/html; charset=utf-8');
            echo 'User email already exists! <br/><a href="javascript:history.back();">Back to login.</a>';
            exit();
        }
	}
    
    // Hash immediately when receiving password
    $password = (string)password_hash($_POST["password"], PASSWORD_DEFAULT);
    $sql="INSERT INTO USER VALUES (NULL, :uid, :email, :password);";
    $q = $db->prepare($sql);
    
    $q->bindParam(":uid", $uid);
    $q->bindParam(":email", $email);
    $q->bindParam(":password", $password);
	$q->execute();
    
    header('Location: ../admin.php');
    exit();
}

function ierg4210_login_verify($email, $passwd) {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    
    if (!preg_match('/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',  $email))
        throw new Exception("invalid-email");

    // Check if email has been registered
    $sql="SELECT * FROM USER WHERE email = :email;";
    $q = $db->prepare($sql);
    $q->bindParam(":email", $email);
	if ($q->execute()){
        $result = $q->fetch();
        if(password_verify($passwd, $result['PASSWORD'])){
            session_regenerate_id();
            $hashToken = hash('sha512', $email.$passwd.$result['type']);
            // Redirect to respective admin and user page
            if($result['TYPE'] == 1){
                header("Location: ../admin.php");
                // Setting HTTPONLY and SECURE Cookie for Admin Only
                if (!isset($_COOKIE['auth'])) {
                    setcookie("auth", $hashToken, NULL, '/', "s65.ierg4210.ie.cuhk.edu.hk", TRUE, TRUE); 
                }
                $_SESSION["Accept"] = $hashToken;
            }else{
                header("Location: ../main.php");
                $_SESSION["Accept"] = 0;
            }
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo 'User credentials not correct, please try again. <br/><a href="javascript:history.back();">Back to login.</a>';
        }
	}
    exit();
}


function ierg4210_cat_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
	$_POST['name'] = (string) $_POST['name'];
    
	$name = test_input($_POST["name"]);
    $sql="INSERT INTO CATEGORIES VALUES (NULL, :name)";
    $q = $db->prepare($sql);
	$q->bindParam(":name", $name);
	$q->execute();
    
    header('Content-Type: text/html; charset=utf-8');
	echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

// To DO: check and delete children in products first
function ierg4210_cat_delete(){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();
    
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
	$_POST['name'] = (string) $_POST['name'];
    
    $name = test_input($_POST['name']);
	$catid_sql = "SELECT * FROM categories where name = :name";
	$q = $db->prepare($catid_sql);
	$q->bindParam(":name", $name);
	$q->execute();
	$catid_result = $q->fetch();
	if(sizeof($catid_result) > 1){
        $sql = "DELETE FROM products WHERE CATID = :catid";
        $q = $db->prepare($sql);
        $q->bindParam(":catid", $catid_result["CATID"]);
        $q->execute();	
	}
    $sql="DELETE FROM categories WHERE NAME = :name";
    $q = $db->prepare($sql);
    $q->bindParam(":name", $name);
    $q->execute();
    
    header('Content-Type: text/html; charset=utf-8');
	echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_cat_edit(){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^[\w\- ]+$/', $_POST['ori_name']))
        throw new Exception("invalid-name");
	$_POST['ori_name'] = (string) $_POST['ori_name'];
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
	$_POST['name'] = (string) $_POST['name'];
    
	$name = test_input($_POST['name']);
	$ori_name = test_input($_POST['ori_name']);
	
    $sql="UPDATE categories SET NAME = :name WHERE NAME = :ori";
    $q = $db->prepare($sql);
	$q->bindParam(":name", $name);
	$q->bindParam(":ori", $ori_name);
	$q->execute();
    
    header('Content-Type: text/html; charset=utf-8');
	echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_prod_edit(){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

    // TODO: complete the rest of the INSERT command
    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];
    if (preg_match('/^[\w\- ]+$/', $_POST['name']))
        $_POST['name'] = (string) $_POST['name'];
    if (preg_match('/^[\d\.]+$/', $_POST['price']))
		$_POST['price'] = (float) $_POST['price'];
    if (preg_match('/^[\w\- ]+$/', $_POST['description']))
		$_POST['description'] = (string) $_POST['description'];

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if (($_FILES["file"]["error"] == 0
        && $_FILES["file"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
        && $_FILES["file"]["size"] < 5000000) || $_FILES['file']['size'] == 0) {

        $pid = test_input($_POST["pid"]);
        $name = test_input($_POST["name"]);
        $price = test_input($_POST["price"]);
        $desc = test_input($_POST["description"]);
		$sql="UPDATE products SET name = IFNULL(:name, name), price=IFNULL(:price, price), description=IFNULL(:desc, description) WHERE PID = :pid";
        $q = $db->prepare($sql);
        $q->bindParam(":name", $name);
        $q->bindParam(":price", $price);
        $q->bindParam(":desc", $desc);
		$q->bindParam(":pid", $pid);
        $q->execute();
        
		if($_FILES['file']['size'] != 0){
			$lastId = $db->lastInsertId();
			// Note: Take care of the permission of destination folder (hints: current user is apache)
			if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/admin/lib/images/" . $lastId . ".jpg")) {
				// redirect back to original page; you may comment it during debug
				header('Location: admin.php');
				exit();
			}
            header('Content-Type: text/html; charset=utf-8');
            echo 'Error. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
            exit();
		}
    }
    
    header('Content-Type: text/html; charset=utf-8');
    echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_prod_delete_by_catid($catid){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

	$sql = "DELETE FROM products WHERE CATID = :catid";
	$q = $db->prepare($sql);
	$q->bindParam(":catid", $catid);
	$q->execute();	
    
	header('Content-Type: text/html; charset=utf-8');
	echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_prod_fetchAll(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products");
    if ($q->execute()){
        return $q->fetchAll();
	}
}

function ierg4210_cat_fetch_by_catId($catid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories WHERE CATID=:catid;");
    $q->bindParam(":catid", $catid);
    if ($q->execute()){
        return $q->fetch();
	}
}

function ierg4210_prod_fetch_by_catId($catid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products WHERE CATID=:catid;");
    $q->bindParam(":catid", $catid);
    if ($q->execute()){
        return $q->fetchAll();
	}
}

function ierg4210_prod_fetch_by_pId($pid){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products WHERE PID=:pid;");
    $q->bindParam(":pid", $pid);
    if ($q->execute()){
        return $q->fetch();
	}
}

function ierg4210_prod_fetchOne(){
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    $sql = "SELECT * FROM products";
    $q = $db->prepare($sql);
    if ($q->execute()){
        return $q->fetch();
	}
}

function ierg4210_prod_delete(){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];
	
    $pid = test_input($_POST['pid']);
	$sql = "DELETE FROM products WHERE PID = ?";
	$q = $db->prepare($sql);
	$q->bindParam(1, $pid);
	$q->execute();	
    
    header('Content-Type: text/html; charset=utf-8');
	echo 'Success. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_user_change_pw($curr, $new, $email){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

    $sql = "SELECT * FROM USER WHERE EMAIL = :email;";
	$q = $db->prepare($sql);
	$q->bindParam(":email", $email);
    if ($q->execute()){
        $result = $q->fetch();
        echo(password_verify($curr, $result['PASSWORD']));
        if(password_verify($curr, $result['PASSWORD'])){
            $sql = "UPDATE USER SET PASSWORD = :new WHERE EMAIL=:email;";
            $q = $db->prepare($sql);
            $q->bindParam(":new", $new);
            $q->bindParam(":email", $email);
            $q->execute();
            header("Location: ../logout.php");
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo 'User credentials not correct, please try again. <br/><a href="javascript:history.back();">Back to login.</a>';
            exit();
        }
	}
    exit();
}

function ierg4210_user_register($pwd, $email){
	// DB manipulation
    global $db;
    $db = ierg4210_DB();

    // Check if email has been registered
    $sql="SELECT * FROM USER WHERE email = :email;";
    $q = $db->prepare($sql);
    $q->bindParam(":email", $email);
	if ($q->execute()){
        if(sizeof($q->fetchAll()) > 0){
            header('Content-Type: text/html; charset=utf-8');
            echo 'User email already exists! <br/><a href="javascript:history.back();">Back to Register Page.</a>';
            exit();
        }
	}

    // Hash immediately when receiving password
    $password = (string)password_hash($_POST["password"], PASSWORD_DEFAULT);
    $sql="INSERT INTO USER VALUES (NULL, 0, :email, :password);";
    $q = $db->prepare($sql);
    
    $q->bindParam(":email", $email);
    $q->bindParam(":password", $password);
	$q->execute();

    header('Location: ../main.php');
    exit();
}