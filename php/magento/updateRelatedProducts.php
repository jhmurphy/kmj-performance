<?php
	//Taylor Webber, July 2015
	//Update Related Products, Upsells, and Cross sells through csv

	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	//2 column comma delimited CSV file with headers for updating UPCs
	//first column - SKU; next columns - SKUs of related products
	$csv = array_map('str_getcsv', file('RelatedProducts.csv'));
	array_shift($csv);

	//Update Related products, Upsells, Cross Sells for products from CSV
	foreach($csv as $row)
	{
		$mainProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
		$counter = 0;

		echo "Main Product: " . $row[$counter] . "\r\n";
		echo "Related Product(s):" . "\t";
		$param = array();

		foreach($row as $sku)
		{
			$relatedProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

			if($relatedProduct and $mainProduct)
			{
				if($relatedProduct != $mainProduct)
				{
					echo $sku . "\t";
					$relatedID = $relatedProduct->getId();

					$param[$relatedID] = array('position'=>$counter);
				}
			}
			$counter = $counter + 1;
		}
		echo "\r\n";

		$mainProduct->setRelatedLinkData($param);
		echo "Updated Related Products--->";
		$mainProduct->setUpSellLinkData($param);
		echo "Upsells--->";
		$mainProduct->setCrossSellLinkData($param);
		echo "Cross Sells--->";
		$mainProduct->save();
		echo "SAVED!";
	}

	echo "done";
	echo "\r\n";
?>