<?php
require __DIR__.'/php/content_security_policy.php';
require __DIR__.'/lib/db.inc.php';
require __DIR__.'/php/shopping_cart.php';
require __DIR__.'/php/logout_button.php';

$res = ierg4210_cat_fetchall();

$cat_id = test_input($_GET['cid']);
$pid = test_input($_GET['pid']);
$product = ierg4210_prod_fetch_by_pId($pid);
$cat_name = ierg4210_cat_fetch_by_catId($cat_id);

$stock = 'Not In Stock';
$categories = '';
$products = '';
$cover = '';

$file_path = '/admin/lib/images/';

if($product['STOCKS'] > 0){
    $stock = 'In Stock' . ': ' . $product['STOCKS'];
}

$categories .= '<div class="list-group categories">';
foreach ($res as $value){
    $categories .= '<form method="GET" action="category.php">';
    $categories .= '<button';
    $categories .= ' type="submit" name="Cat" value="'.$value["CATID"].'" class="list-group-item list-group-item-action"';
    $categories .= ">".$value["NAME"]."</button>";
    $categories .= '</form>';
}
$categories .= ' </div>';

$products .= '<div class="ProductList">
                <div class="card mb-3" style="max-width: 540px;">
                <div class="row g-0">
                <div class="col-md-4">
                <img src="'.$file_path.$pid.'.jpg" class="img-fluid rounded-start" alt="...">
                </div>
                <input type="hidden" id="pid" name="pid" value="'.$pid.'">
                <div class="col-md-8">
                <div class="card-body">';
$products .= '<h5 id="pname" value="'.$product["NAME"].'" class="card-title">'.$product["NAME"].'</h5>';
$products .= ' <p class="card-text">'.$product["DESCRIPTION"].'</p>';
$products .= ' <p class="card-text">$'.$product["PRICE"].'</p>';
$products .= '<p class="card-text"><small class="text-muted">'.$stock;
$products .=  '</small></p>
                <button type="button" id="addToCart" value="'.$product["PID"].'" class="btn btn-primary AddToCartBtn">Add To Cart</button>
                </div>
                </div>
                </div>
                </div>
                </div>';

$cover = '<nav class="navi">
            <a rel="noopener noreferrer" href="main.php">Home</a>>
            <a rel="noopener noreferrer" href="category.php?Cat='.$cat_id.'">'.$cat_name["NAME"].'</a>>
            <a rel="noopener noreferrer" href="product.php?pid='.$pid.'&cid='.$cat_id.'">'.$product["NAME"].'</a>
            </nav>';

?>
<html>
<body>
<?php echo $cover; ?>
<?php echo $shopping_cart; ?>
<?php echo $categories; ?>
<?php echo $products; ?>
<?php echo $content; ?>

</body>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/product.css">
    <link rel="stylesheet" type="text/css" href="/css/shopping_cart.css">
    <link rel="stylesheet" type="text/css" href="/css/logout.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>    
    <script src="/js/cart.js"></script> 
</head>

</html>