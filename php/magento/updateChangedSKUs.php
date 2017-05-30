<?php
	//Taylor Webber, July 2015
	//Used to update skus that have been changed using SKU_To_Update.csv
	//currently using to update changed Kit SKUs

	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	
	echo "Getting SKUs to Update" . "\r\n";
	//CSV should have 2 columns with headers
	//Old SKU in column 1, New SKU in column 2
	$csv = array_map('str_getcsv', file('SKU_To_Update.csv'));
	array_shift($csv);
	
	//Loads product using Old SKU
	//Updates and saves product with New SKU
	foreach($csv as $row)
	{
		$old_sku = $row[0];
		$new_sku = $row[1];
			
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $old_sku);
		$partnum = substr($row[0], 3);
		echo $old_sku . " To: " . $new_sku . "\r\n";
		$product->setSku($new_sku);
		$product->save();
	}
?>