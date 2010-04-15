<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_rpx_sv1' /* sv key */,
		array(

			'title' => 'RPX Auth Service',
			'description' => 'Login with RPX',

			'subtype' => '',

			'available' => TRUE,
			'priority' => 75,
			'quality' => 56,

			'os' => '',
			'exec' => '',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY).'classes/Service/Auth.php',
			'className' => 'tx_rpx_Service_Auth',
		)
	);


t3lib_extMgm::addPItoST43($_EXTKEY, 'classes/Frontend/Plugin.php', '_Frontend_Plugin', 'list_type', 1);
?>