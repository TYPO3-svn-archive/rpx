<?php
if (!defined ('TYPO3_MODE')) 
	die ('Access denied.');

// add flexform to pi1
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_Frontend_Plugin'] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_Frontend_Plugin'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_Frontend_Plugin', 'FILE:EXT:' . $_EXTKEY . '/res/flexform.xml');
t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_Frontend_Plugin'),'list_type');
if (TYPO3_MODE=="BE")
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_rpx_Frontend_Wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'classes/Frontend/Wizicon.php';

?>