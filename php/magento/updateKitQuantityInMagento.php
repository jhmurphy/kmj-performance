<?php


$mageFilename = '../public_html/app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
set_time_limit(0);
ini_set('memory_limit','1024M');
 
/***************** UTILITY FUNCTIONS ********************/
function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}
 
function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}
 
function _getAttributeId($attribute_code = 'price'){
    $connection = _getConnection('core_read');
    $sql = "SELECT attribute_id
                FROM " . _getTableName('eav_attribute') . "
            WHERE
                entity_type_id = ?
                AND attribute_code = ?";
    $entity_type_id = _getEntityTypeId();
    return $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
}
 
function _getEntityTypeId($entity_type_code = 'catalog_product'){
    $connection = _getConnection('core_read');
    $sql        = "SELECT entity_type_id FROM " . _getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
    return $connection->fetchOne($sql, array($entity_type_code));
}
 
function _checkIfSkuExists($sku){
    $connection = _getConnection('core_read');
    $sql        = "SELECT COUNT(*) AS count_no FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
    $count      = $connection->fetchOne($sql, array($sku));
    if($count > 0){
        return true;
    }else{
        return false;
    }
}
 
function _getIdFromSku($sku){
    $connection = _getConnection('core_read');
    $sql        = "SELECT entity_id FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
    //echo $sku;
    return $connection->fetchOne($sql, array($sku));
}
 
function _updateStocks($data){
    $connection     = _getConnection('core_write');
    $sku            = $data[0];
    $newQty         = $data[1];
    //echo $sku;
    //echo $newQty;
    $productId      = _getIdFromSku($sku);
    $attributeId    = _getAttributeId();
 
    $sql            = "UPDATE " . _getTableName('cataloginventory_stock_item') . " csi,
                       " . _getTableName('cataloginventory_stock_status') . " css
                       SET
                       csi.qty = ?,
                       csi.is_in_stock = ?,
                       css.qty = ?
                       WHERE
                       csi.product_id = ?
                       AND csi.product_id = css.product_id";
    $isInStock      = $newQty > 0 ? 1 : 0;
    //$stockStatus    = $newQty > 0 ? 1 : 2;
    $connection->query($sql, array($newQty, $isInStock, $newQty, $productId));
}
/***************** UTILITY FUNCTIONS ********************/
 
$csv                = new Varien_File_Csv();
$data               = $csv->getData('stocksKits.csv'); //path to csv
array_shift($data);


/**********Array of PLCs that are drop shipped to check against**********/
$plc_array = array("");
echo 'Created Array' . "\r\n";

$notUpdated_array = array();

$message = '';
$count   = 1;
$updated = 0;
$notUpdated = 0;
$drop_ship_updated = 0;

echo 'Starting checking' . "\r\n";

foreach($data as $_data){
    if(_checkIfSkuExists($_data[0])){
        
        //Get Product Line Code
        $plcToCheck = substr($_data[0], 0, 3);
        //echo $plcToCheck;
        $plc_found = false;
        try{
            //Iterate through drop ship array
            foreach($plc_array as $drop_plc){
                //Check if plc equals current drop ship PLC
                if($plcToCheck == $drop_plc){
                    //if found set to true
                    echo 'PLC of ' . $drop_plc . ' was matched -' ; //. "\r\n";
                    $plc_found = true;
                    break 1;
                }
            }
            //if PLC was found set quantity to 10
            if($plc_found == true){
                $_data[1] = 10;
                echo ' Quantity adjusted to ' . $_data[1] . "\r\n";
                _updateStocks($_data);
                $drop_ship_updated = $drop_ship_updated + 1;
            }else{
                _updateStocks($_data);
                $updated = $updated + 1;
            }
            $message .= $count . '> Success:: Qty (' . $_data[1] . ') of Sku (' . $_data[0] . ') has been updated.' . "\r\n";
 
        }catch(Exception $e){
            $message .=  $count .'> Error:: while Updating  Qty (' . $_data[1] . ') of Sku (' . $_data[0] . ') => '.$e->getMessage()."\r\n";
        }
    }else{
        $message .=  $count .'> Error:: Product with Sku (' . $_data[0] . ') doesn\'t exist.' . "\r\n";
		$notUpdated = $notUpdated + 1;
		$notUpdated_array[] = $_data[0];
    }
    $count++;
}
echo $message;
echo 'Drop Ship items updated: ' . $drop_ship_updated . "\r\n";
echo 'Other items updated: ' . $updated . "\r\n";
echo 'Items not updated: ' . $notUpdated . "\r\n";
print_r($notUpdated_array);