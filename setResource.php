<?php

/**
 * Webshopapps Order Import
 *
 * NOTICE OF LICENSE
 *
 * Please see the license at www.webshopapps.com/license/license.txt
 *
 * DISCLAIMER
 *
 * This is a test order import script, and is not intended for live use.
 * Zowta Ltd bears no responsibility for any adverse affects of using 
 * this script
 *
 * @category   Webshopapps
 * @package    Resource re setter
 * @copyright  Copyright (c) 2011 Zowta Ltd (http://www.webshopapps.com)
 * @license    www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/

require_once 'app/Mage.php';
    $app = Mage::app();
    Mage::register('isSecureArea', true);
    $storeId=1;
    
function rewindResource($resName,$version) {
    $resource = Mage::getResourceSingleton('core/resource');
    $resource->setDbVersion($resName,$version);    	 
}

function deleteResource($resName,$version) {
    $resource = Mage::getResourceSingleton('wsacommon/resource');
    Mage::log($resource);
    $resource->deleteDbVersion($resName,$version);
}
    
if (isset($_POST['resource'])) {
	$resName = $_POST['resource']; 
} elseif ((isset($_POST['resource_other']))) {
	$resName = $_POST['resource_other'];
} else {
	$resName = "";
}
if (isset($_POST['version'])) {
	$version = $_POST['version']; 
} 

if (isset($_POST['delete'])) {
	deleteResource($resName, $version); }
else if (isset($_POST['rewind'])) {
	rewindResource($resName, $version); }
 
$resource = Mage::getResourceSingleton('core/resource');
$initVersion = $resource->getDbVersion($resName);
$newVersion = $resource->getDbVersion($resName);

$thisUrl = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI']; 	//Get Current Url

?>

<html>
<head>

<?php 
if (!isset($_POST['delete']) && !isset($_POST['rewind']) && $resName == "") {
	echo '<title>Webshopapps Resource setter</title>
	</head>
	<body>
	<h2>Current Core_Resource Module Version</h2>
	<form action="setResource.php" method="post">
	<p>
	<select name="resource">
	<option value="productmatrix_setup">Product Matrix</option>
	<option value="premiumrate_setup">Premium Matrix</option>
	<option value="matrixrate_setup">Matrix Rate</option>
	<option value="shippingoverride2_setup">Shipping Override</option>
	<option value="dropship_setup">Drop Ship</option>
	<option value="matrixdays_setup">Matrix Days</option>
	<option value="productrate_setup">Product Rate</option>
	<option value="wsacommon_setup">WebShopApps Common</option>
	<option value="wsalogger_setup">WebShopApps Logger</option>
	</select>
	</p>
	<input type="submit" name="Submit1" value="Show Current Resource Version">
	</form>
	<h2>Other</h2>
	<form action="setResource.php" method="post">
	<p>Use if you cannot find your extension in the dropdown, proceed with caution:</p>
	<p><input type="text" name="resource_other"/> (eg. <i>productmatrix_setup</i>)</p>
	<input type="submit" name="Submit2" value="Show Current Resource Version">
	</form>
	</body>';
}

if (!isset($_POST['delete']) && !isset($_POST['rewind']) && $resName != "") {
	echo '<title>Current "Core_Resource" Module Version</title>
	</head>
	<body>
	<h2>Current "Core_Resource" Module Version</h2>';
} 

if (!isset($_POST['delete']) && !isset($_POST['rewind']) && $initVersion == "" && $resName != "") {
	echo '<p><b><i>' . $resName . '</b></i>' . ' is not a valid core_resource code.</p>
	<input type="button" value="Back to rewind form" onClick="history.go(-1);return true;">';
} elseif ($initVersion != "" && $resName != "") {
	echo "<p>Module Code: <b>" . $resName . "</b> <br />";
} 
	
if (!isset($_POST['delete']) && !isset($_POST['rewind']) && $initVersion != "") {
	echo 'Module Version: <b>' . $initVersion . '</b> </p>
	<h2>Rewind Resource</h2> 
	<form action="setResource.php" method="post">
	<p><input type="hidden" name="resource" value="' . $resName . '"/>
	Version: <input type="text" name="version" value="' . $initVersion . '"/></p>
	<input type="submit" name="rewind" value="rewind">
	</form>
	<h2>Delete Resource</h2>
	<form action="setResource.php" method="post">
	<p><input type="hidden" name="resource" value="' . $resName . '"/>
	<input type="hidden" name="version" value="' . $initVersion . '"/></p>
	<input type="submit" name="delete" value="delete">
	</form>';
}

if (isset($_POST['delete']) && $newVersion == $resName) {
	echo '<p>You have deleted the core_resource row from your database</p>
	<input type=button onClick="location.href=\'' . $thisUrl . '\'" value="I would like to go back to the rewind form">';
} else if (isset($_POST['rewind']) && $newVersion != $resName) {
	echo 'You have rewound your core resource table to:
	<p>version: <b>' . $newVersion . '</b></p>
	<input type=button onClick="location.href=\'' . $thisUrl . '\'" value="I would like to go back to the rewind form">';
}
?>
	
</body>
</html>