<?php
/**
 * Exception from RPX Service
 */
class tx_rpx_Core_UserNotFoundException extends Exception {}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserNotFoundException.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserNotFoundException.php']);
}