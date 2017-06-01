<?php
// Magento Required Stuff
require_once '../public_html/app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 7200);
$start = microtime(true);
$all_skus = array();
$products = Mage::getModel('catalog/product')->getCollection();

foreach($products as $product)
{
	array_push($all_skus, $product->getSku());
}

asort($all_skus);
$fp = fopen('../public_html/allSkus.csv','w');
foreach($all_skus as $sku)
{
	echo $sku . "\r\n";
	$val = explode("," , $sku);
	fputcsv($fp, $val);
}

fclose($fp);
$time_elapsed = microtime(true) - $start;
echo $time_elapsed . "\r\n";
?>
