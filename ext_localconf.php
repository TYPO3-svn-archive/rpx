<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

	// Register cache t3lib_l10n
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['rpx'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['rpx'] = array();
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['rpx']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['rpx']['backend'] = 't3lib_cache_backend_FileBackend';
}

t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_rpx_sv1' /* sv key */,
		array(
			'title' => 'RPX Auth Service',
			'description' => 'Login with RPX',
			'subtype' => 'getUserFE,authUserFE,getGroupsFE',
			'available' => TRUE,
			'priority' => 90,
			'quality' => 56,
			'os' => '',
			'exec' => '',
			'classFile' => t3lib_extMgm::extPath($_EXTKEY).'classes/Service/Auth.php',
			'className' => 'tx_rpx_Service_Auth',
		)
	);
t3lib_extMgm::addPItoST43($_EXTKEY, 'classes/Frontend/Plugin.php', '_Frontend_Plugin', 'list_type', 1);
?>