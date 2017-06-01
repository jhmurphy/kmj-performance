<?php
require_once 'app/Mage.php';
umask(0);
Mage::app ()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//Used to Update Titles in Bulk
$csv = array_map('str_getcsv', file('ARC_Titles.csv'));
array_shift($csv);
$count = 0;

foreach($csv as $row)
{
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
	
	if($product)
	{
		$sku = $product->getName();
		echo $sku . "\r\n";
		$new = $row[1];
		$product->setName($new);
		$product->save();
		$count++;
	}
}

echo $count . " Products Updated" . "\r\n";
echo "done". "\r\n";
?>