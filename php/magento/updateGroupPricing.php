<?php
//Taylor Webber
//June 2015

	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	
	//Edit file name, make sure it is comma delimited.
	//first column-sku, second column-jobber price, third column-price to set
	$csv = array_map('str_getcsv', file('STA_groupPricing.csv'));
	
	$products_updated = 0;
	$not_updated = 0;
	
	$product_notupdated = array();
	
	foreach($csv as $row)
	{
		echo 'Getting Next Product' . "\r\n";
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
		
		if($product)
		{
			echo 'Updating:' . "\r\n";
			echo $row[0] . ' : ' . $product->getPrice() . ' to ' . $row[1];
			
			//array of arrays for group_pricing
			//website_id of 0 means for all stores
			//cust_group 3 is retailers
			$groupPricingData = array (array ('website_id'=>0, 'cust_group'=>3, 'price'=>$row[2]));
			
			echo "\r\n";
			//setData for group_price is expecting an array of arrays
			$product->setData('group_price',$groupPricingData);
			echo "data set->sleeping...3..";
			sleep(1);
			echo "2..";
			sleep(1);
			echo "1..";
			sleep(1);
			
			$product->save();
			echo "data saved!" . "\r\n";
			//couter for updated products
			$products_updated = $products_updated + 1;
		}
		else
		{
			echo 'Product not found: ' . $row[0] . "\r\n";
			$not_updated = $not_updated + 1;
			array_push($product_notupdated, $row[0]);
		}

		echo 'Finishing product' . "\r\n";
	}
	
	echo "Updated: " . $products_updated . "\r\n";
	echo "Not Updated: " . $not_updated . "\r\n";
	echo "-----------\r\n";
	echo "Not Updated:\r\n";
	foreach($product_notupdated as $sku)
	{
		echo $sku . "\r\n";
	}
?>