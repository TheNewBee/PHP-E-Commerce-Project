<?php
header('Content-Type: application/json');
require __DIR__.'/lib/db.inc.php';

$pid = $_POST['pid'];

$product = ierg4210_prod_fetch_by_pId($pid);
$name = $product["NAME"];
$price = $product["PRICE"];
$quant = $_POST['quant'];

echo json_encode(array("pid" => $pid, "name" => $name, "price" => $price, "quant" => $quant));
exit;
?>