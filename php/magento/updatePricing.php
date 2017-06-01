<?php
	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	
	//Edit file name, make sure it is comma delimited.
	$csv = array_map('str_getcsv', file('PTX_pricing_1101.csv'));
	
	$products_updated = 0;
	$not_updated = 0;
	$same_price = 0;
	
	$product_notupdated = array();
	
	foreach($csv as $row)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
		
		if($product)
		{
			echo 'Updating: ' . $row[0] . ' -> ' . $product->getPrice() . ' to ' . $row[1];
			
			if(floatval($row[1]) === floatval($product->getPrice()))
			{
				$same_price = $same_price + 1;
			}
			else
			{
				echo " Change!";
			}
			
			echo "\r\n";
			$product->setPrice($row[1]);
			$product->save();
			$products_updated = $products_updated + 1;
		}
		else
		{
			$not_updated = $not_updated + 1;
			array_push($product_notupdated, $row[0]);
		}
	}
	
	echo "Updated: " . $products_updated . "\r\n";
	echo "Not Updated: " . $not_updated . "\r\n";
	echo "Product Prices Same After Updating " . $same_price . "\r\n"; 
	echo "Not Updated\r\n";
	echo "-----------\r\n";
	foreach($product_notupdated as $sku)
	{
		echo $sku . "\r\n";
	}
?>