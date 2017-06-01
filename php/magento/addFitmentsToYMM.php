<?php

//Taylor Webber, July 2015
//Add Fitment information to Magento using Fitments_To_Add.csv

require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$websiteSkus = array_map('str_getcsv', file('allSkus.csv'));
echo "allSkus opened" . "\r\n";

$sku_list = array();

foreach($websiteSkus as $sku)
{
	array_push($sku_list, strtolower($sku[0]));
}
//Add Fitments to YMM
$csv = array_map('str_getcsv', file('Fitments_To_Add.csv'));
array_shift($csv);
echo "Fitments_To_Add opened" . "\r\n";

foreach($csv as $row)
{	
	//echo "Inside For Loop" . "\r\n";

	if(in_array(strtolower($row[0]), $sku_list))
	{
		//echo "Inside If" . "\r\n";

		$ymm = Mage::getModel('fitment/search');
		
		$sku = strtolower($row[0]);
		$year   = $row[1];
		$make   = ucwords(strtolower($row[2]));
		$model  = ucwords(strtolower($row[3]));
		$trim   = ucwords(strtolower($row[4]));
		$engine = ucwords(strtolower($row[5]));
		
		$ymm->setSku($sku);
		$ymm->setYear($year);
		$ymm->setMake($make);
		$ymm->setModel($model);
		$ymm->setTrim($trim);
		$ymm->setEngine($engine);
		
		$ymm->save();
		
		echo "Saved: " . $sku . "\t" . $year . "\t" . $make . "\t" . $model . "\t" . $trim . "\t" . $engine . "\r\n";
	}
}

?>