<?php
	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	
	$products = Mage::getModel('catalog/product')->getCollection();
	$all_kits = array();
	
	#Get All KITS/Combo Items i.e. items with a + or KIT at the end
	
	foreach($products as $product)
	{
		if(strpos($product->getSku(), '+') !== false)
		{
			array_push($all_kits, $product->getSku());
		}
	}
	
	asort($all_kits);
	$fp = fopen('allKits.csv', 'w');
	
	foreach($all_kits as $kit)
	{
		echo $kit . "\r\n";
		$val = explode(",", $kit);
		fputcsv($fp, $val);
	}
	
	fclose($fp);
?>