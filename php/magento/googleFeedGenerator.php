<?php
require_once '../public_html/app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 7200);

// The Array to Output\(~ delimited)
$product_lines = array();

// Header Info(~ delimited)
$header_info = "id\ttitle\tdescription\tgoogle_product_category\tproduct_type\tlink\timage_link\tcondition\tavailability\tprice\tbrand\tgtin\tmpn\tidentifier_exists";
array_push($product_lines, $header_info);

// Basic Product Information
$id        = "";
$title     = "";
$desc      = "";
$googleCat = "Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts";
$type      = "";
$link      = "";
$imgLink   = "";
$cond      = "new";

// Availability & Price
$avail     = "in stock";
$price     = "";
$salePrice = "";
$saleDate  = "";

// Unique Product Identifiers 
$brand  = "";
$gtin   = "";
$mpn    = "";
$exists = "TRUE";

$csv = array_map('str_getcsv', file('allSkus.csv'));

foreach($csv as $row)
{	
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);
	$productline = null;
	
	if($product && $product->getStatus() == 1)
	{
		// Basic Product Information
		$sku = $product->getSku();
		$flags = array('flags' => FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH);
		//Use Filter_var to sanitize HTML, eBay -> Website HTML Encoding issues
		$title = filter_var(ucwords(strtolower($product->getName())), FILTER_SANITIZE_STRING, $flags)?:'';
		$desc  = filter_var($product->getDescription(), FILTER_SANITIZE_STRING, $flags)?:'';

		$url   = 'http://www.kmjperformance.com/' . $product->getUrlPath();

		$imgUrl = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
		$imgResize = Mage::helper('catalog/image')->init($product, 'image')->resize(500);
	
		// Availability & Price
		$price = $product->getPrice();
	
		// Unique Product Identifiers
		$brand = substr($sku, 0, 3);
		
		
		$mpn = $product->getResource()->getAttribute('mpn')->getFrontend()->getValue($product);
		
		if(empty($mpn))
		{
			$mpn = $sku;
		}
		
		
		// Check for a upc
		$upc = $product->getResource()->getAttribute('upc_code')->getFrontend()->getValue($product);
		
		if(!empty($upc))
		{
			$gtin = $upc;
		}
		else
		{
			$gtin = "";
		}
		
		$catIds = $product->getCategoryIds();
		$type = Mage::getModel('catalog/category')->load($catIds[0])->getName();
		/*
		// Get Categories
		$categoryCollection  = $product->getCategoryCollection();
		
		foreach($categoryCollection as $category)
		{
			$category = Mage::getModel('catalog/category')->load($category->getId());
			$type .= $category->getName();
			$type .= ' > ';
			$category->clearInstance();
			unset($category);
			gc_collect_cycles();
		}
		unset($categoryCollection);
		*/
		$productline .= $sku;
		$productline .= "\t";
		$productline .= $title;
		$productline .= "\t";
		$productline .= $desc;
		$productline .= "\t";
		$productline .= $googleCat;
		$productline .= "\t";
		$productline .= $type;
		$productline .= "\t";
		$productline .= $url;
		$productline .= "\t";
		$productline .= $imgUrl;
		$productline .= "\t";
		$productline .= $cond;
		$productline .= "\t";
		$productline .= $avail;
		$productline .= "\t";
		$productline .= $price;
		$productline .= "\t";
		$productline .= $brand;
		$productline .= "\t";
		$productline .= $gtin;
		$productline .= "\t";
		$productline .= $mpn;
		$productline .= "\t";
		$productline .= $exists;
		$productline .= "\t";
		$productline .= $imgResize;
		
		// Make sure there is a price
		if($price != 0)
			array_push($product_lines, $productline);
		
		echo $sku . "\r\n";
		// Force Memory Collection
		//unset($product);
		//gc_collect_cycles();
	}
}

$fp = fopen('Google_Feed.csv','w');

foreach($product_lines as $line)
{
  echo $line;
  echo "\r\n";
  $val = explode("\t" , $line);
  fputcsv($fp, $val, "\t", '"');
}

fclose($fp);

echo "done\r\n";

?>
