<?php
	require_once '../public_html/app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	//2 column comma delimited CSV file with headers for updating Brands
	//first column - SKU; second column - New Brand
	$csv = array_map('str_getcsv', file('New-Brand.csv'));
	array_shift($csv);
	
	//Update UPC Codes for products from CSV
	foreach($csv as $row)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
		
		if($product)
		{
			//echo 'Updating: ' . $row[0] . ' -> ' . 'Old: ' . $product->getAttributeText('brand');
			echo 'Updating: ' . $row[0] . ' -> ' . 'Old: ' . $product->getBrand();
						
			$product->setBrand($row[1]);
			$product->save();
			
			echo ' New: ' . $product->getAttributeText('brand') . "\t" . "\r\n";
		}
	}
	echo "done";
	echo "\r\n";
?>