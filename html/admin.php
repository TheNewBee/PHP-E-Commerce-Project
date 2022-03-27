<?php
require __DIR__.'/php/content_security_policy.php';
require __DIR__.'/php/checkAccess.php';
require __DIR__.'/lib/db.inc.php';
CheckAccess();
$res = ierg4210_cat_fetchall();
$options = '';
// Hash the nonce based on time
$nonce = hash('sha512', time());
$_SESSION['nonce'] = $nonce;

foreach ($res as $value){
    $options .= '<option value="'.$value["CATID"].'"> '.$value["NAME"].' </option>';
}
?>

<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<script src="/js/admin.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<link rel="stylesheet" type="text/css" href="/css/main_link.css">
</head>
<body>
    <center><h1>Admin Panel</h1></center>
    <div class="main"><a rel="noopener noreferrer" href="main.php">Shop Main Page</a></div>
    <fieldset class="border border-dark rounded">
        <div class="border form-margin">
        <legend> New User</legend>
        <form class="form-group" id="user_insert" method="POST" action="admin-process.php?action=user_insert">
            <label for="prod_catid"> User Role *</label>
            <div> 
                <select class="form-select" id="user" name="uid">
                    <option value='user1'>admin</option>
                    <option value='user2'>user</option>
                </select>
            </div>
            <label for="email"> Email *</label>
            <div> <input id="email" class="form-control" type="email" name="email" placeholder="abc@email.com" required="required" pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$"/> </div>
            <div>
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <label for="password"> Password *</label>
            <div> <input id="password" class="form-control" type="password" placeholder="password" name="password" required="required"/></div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>            
        </form>
        </div>

    </fieldset>
    <fieldset class="border border-dark rounded">
    <div class="border form-margin">
    <legend> New Product</legend>
        <form class="form-group" id="prod_insert" method="POST" action="admin-process.php?action=prod_insert"
        enctype="multipart/form-data">
            <label for="prod_catid"> Category *</label>
            <div> <select class="form-select" id="prod_catid" name="catid">
			<?php echo $options; ?>
			</select></div>
            <label for="prod_name"> Name *</label>
            <div> <input class="form-control" id="new_prod_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input class="form-control"  id="new_prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input class="form-control" id="new_prod_desc" type="text" name="description" pattern="^[\w\-]+$"/> </div>
            <label for="prod_image"> Image * </label></br>
            <div id="drop-region">
                <div class="drop-message">
                    Drag & Drop images or click to upload
                </div>
                <div id="image-preview"></div>
            </div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>    
        </form>
    </div>
    <div class="border form-margin">
    <legend> Update Product</legend>
        <form class="form-group" id="prod_edit" method="POST" action="admin-process.php?action=prod_edit"
        enctype="multipart/form-data">
            <label for="prod_pid"> Enter Product ID To Update*</label>
			<div> 
                <input class="form-group"  id="update_prod_pid" type="number" name="pid" required="required" pattern="/^\d*$/"/>
            </div>
            <label for="prod_name"> Name *</label>
            <div> <input class="form-group"  id="update_prod_name" type="text" name="name" pattern="^[\w\-]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input class="form-group"  id="update_prod_price" type="text" name="price" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input class="form-group"  id="update_prod_desc" type="text" name="description" pattern="^[\w\-]+$"/> </div>
            <label for="prod_image"> Image * </label>
            <div> <input class="form-control-file"  type="file" name="file" accept="image/jpeg"/> </div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>    
        </form>
    </div>
    <div class="border form-margin">
    <legend> Delete Product </legend>
        <form class="form-group" id="prod_delete" method="POST" action="admin-process.php?action=prod_delete"
        enctype="multipart/form-data">
            <label for="prod_pid"> Enter Product ID To Delete*</label>
			<div> <input class="form-control" id="delete_prod_pid" type="number" name="pid" required="required" pattern="/^\d*$/"/></div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>    
        </form>
    </div>
    </fieldset>
	<fieldset class="border border-dark rounded">
    <div class="border form-margin">
    <legend> New Category</legend>
        <form class="form-group" id="cat_insert" method="POST" action="admin-process.php?action=cat_insert"
        enctype="multipart/form-data">
            <label for="cat_name"> Name *</label>
            <div> <input class="form-control" id="new_cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
            <input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>   
        </form>
    </div>
    <div class="border form-margin">
    <legend> Update Category</legend>
        <form class="form-group" id="cat_edit" method="POST" action="admin-process.php?action=cat_edit"
        enctype="multipart/form-data">
            <label for="cat_ori_name"> Original Name *</label>
            <div> <input class="form-control" id="cat_ori_name" type="text" name="ori_name" required="required" pattern="^[\w\-]+$"/></div>
            <label for="cat_name"> New Name *</label>
            <div> <input class="form-control" id="update_cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
			<input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>   
        </form>
    </div>
    <div class="border form-margin">
    <legend> Delete Category</legend>
        <form class="form-group" id="cat_delete" method="POST" action="admin-process.php?action=cat_delete"
        enctype="multipart/form-data">
            <label for="cat_name"> Name *</label>
            <div> <input class="form-control" id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
			<input type="hidden" name="nonce" value="<?php echo $nonce ?>" />
            <div class="center">
                <input class="form-control" type="submit" value="Submit"/>
            </div>   
        </form>
    </div>
    </fieldset>
</body>
</html>
