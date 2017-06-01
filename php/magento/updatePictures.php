<?php
	//Taylor Webber, July 2015
	//Removes any existing photos on Magento and assigns new image path

	require_once 'app/Mage.php';
	umask(0);
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	//2 column comma delimited CSV file with headers for updating UPCs
	//first column - SKU; second column - Image Path
	$csv = array_map('str_getcsv', file('SixBitPictures.csv'));
	array_shift($csv);
	
	//Update Images for products from CSV
	foreach($csv as $row)
	{
		echo $row[0];
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]);

		if ($product->getId())
		{
    		$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
    		$items = $mediaApi->items($product->getId());
    		foreach($items as $item)
    		{
        		$mediaApi->remove($product->getId(), $item['file']);
        	}

        	$imagePath = $row[1];

			//Remove unset images, add image to gallery if exists
			$importDir = Mage::getBaseDir('media') . DS . 'import/';

    		$filePath = $importDir.$imagePath;
    		if ( file_exists($filePath) ) {
    			echo "--->Adding Image...";
        		try {
            		$product->addImageToMediaGallery($filePath, array('image', 'small_image', 'thumbnail'), false, false);
            		$product->save();
            		echo "Saved" . "\r\n";
        		} catch (Exception $e) {
        			echo "Error: ";
            		echo $e->getMessage() . "\r\n";
        		}
    		} else {
        		echo "Product does not have an image or the path is incorrect. Path was: {$filePath}<br/>";
    		}
		}
	}
	echo "done";
	echo "\r\n";
?>