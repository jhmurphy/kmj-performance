<?php
	require_once '../public_html/app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	//2 column comma delimited CSV file with headers for updating UPCs
	//first column - SKU; second column - New UPC
	$csv = array_map('str_getcsv', file('New-UPC.csv'));
	array_shift($csv);
	
	//Update UPC Codes for products from CSV
	foreach($csv as $row)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
		
		if($product)
		{
			echo "SKU: " . $row[0] . "\t";
			echo "Old: ";
			echo $product->getUpc_code();
			echo "\t";
			
			$product->setUpc_code($row[1]);
			$product->save();
			
			echo "New: ";
			echo $product->getUpc_code();
			echo "\t";
			echo "\r\n";
		}
	}
	echo "done";
	echo "\r\n";
?>