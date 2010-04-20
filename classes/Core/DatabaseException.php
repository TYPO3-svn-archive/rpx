<?php
/**
 * Exception from Database
 */
class tx_rpx_Core_DatabaseException extends Exception {}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/DatabaseException.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/DatabaseException.php']);
}