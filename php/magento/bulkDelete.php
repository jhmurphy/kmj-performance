<?php

require_once 'app/Mage.php';
Mage :: app("default")->setCurrentStore(Mage_Core_Model_App :: ADMIN_STORE_ID);
$skuAll = array();
$file_handle = fopen("skustodelete.csv", "r");
$delete = array();
$catalog = Mage::getModel('catalog/product');
while (!feof($file_handle)) {
     $line_of_text = fgetcsv($file_handle, 1024);
     $allSku = $line_of_text[0];
     $delete[] = $allSku;
}
foreach ($delete as $del) {
     $product = $catalog->loadByAttribute('sku', $del);
     try {
          $product->delete();
          echo "Product with ID: " . $product->getId() . " Deleted Successfully<br />";
     } catch (Exception $e) {
          echo "Product with ID: " . $product->getId() . "cannot be deleted<br />";
     }
}
echo "Finish Delete";

?>