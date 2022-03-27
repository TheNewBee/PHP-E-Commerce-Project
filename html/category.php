<?php
require __DIR__.'/php/content_security_policy.php';
require __DIR__.'/lib/db.inc.php';
require __DIR__.'/php/shopping_cart.php';
require __DIR__.'/php/logout_button.php';

$res = ierg4210_cat_fetchall();

$cat_id = test_input($_GET['Cat']);
$product = ierg4210_prod_fetch_by_catId($cat_id);
$cat_name = ierg4210_cat_fetch_by_catId($cat_id);

$categories = '';
$products = '';
$cover = '';

$file_path = '/admin/lib/images/';

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
<div class="row row-cols-1  g-4">';
foreach($product as $value){
    $products .= '<form class="product" id="product_form_'.$value["PID"].'" method="GET" action="product.php">';
    $products .= '<div class="col"><div class="card">
                    <a rel="noopener noreferrer" id="product_link" href="javascript:handleClickAction('.$value["PID"].')">
                    <img src="'.$file_path.$value['PID'].'.jpg"';
    $products .= ' class="card-img-top thumbnail">
                    </a>
                    <input type="hidden" name="pid" value="'.$value["PID"].'">
                    <input type="hidden" name="cid" value="'.$value["CATID"].'">
                    <div class="card-body">
                    <h5 class="card-title">
                    <a rel="noopener noreferrer" href="product.php">'.$value['NAME'].'</a>';
    $products .= '</h5>
                    <section>
                    <p class="card-text">$'.$value['PRICE'].'</p>';
    $products .= '<button type="button" id="addToCart_'.$value["PID"].'" value="'.$value["PID"].'" class="btn btn-primary AddToCartBtn">Add To Cart</button>
                    </section></div></div></div>';
                    $products .= '</form>';
}
$products .= '</div>
<ul id="pagin">

</ul>
</div>';

$cover = '<nav class="navi">
            <a rel="noopener noreferrer" href="main.php">Home</a>>
            <a rel="noopener noreferrer" href="category.php?Cat='.$cat_id.'">'.$cat_name["NAME"].'</a>
            </nav>';
?>
<html>
<body>

<?php echo $cover; ?>
<?php echo $shopping_cart ?>
<?php echo $categories; ?>  
<?php echo $products; ?>
<?php echo $content; ?>

</body>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/category.css">
    <link rel="stylesheet" type="text/css" href="/css/shopping_cart.css">
    <link rel="stylesheet" type="text/css" href="/css/logout.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script> 
    <script src="/js/cart.js"></script> 
    <script src="/js/category.js"></script> 
</head>
</html>