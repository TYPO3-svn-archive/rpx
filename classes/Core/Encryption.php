<?php
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'Exception.php';
/**
 * Encryption Service
 */
class tx_rpx_Core_Encryption {
	/**
	 * @param array $values
	 * @param string $verify
	 * @return string
	 * @throws tx_rpx_Core_Exception
	 */
	public function validate(array $values,$verify){
		if($verify !== $this->creatHash($values)){
			throw new tx_rpx_Core_Exception('invalid verify value'.$verify);
		}
	}
	/**
	 * @param string $values
	 * @return string
	 */
	public function creatHash(array $values){
		return md5($this->getEncryptionKey().serialize($values).$this->getEncryptionKey());
	}
	/**
	 * @return string
	 * @throws tx_rpx_Core_Exception
	 */
	private function getEncryptionKey(){
		if(FALSE === isset($GLOBALS ['TYPO3_CONF_VARS']['SYS']['encryptionKey'])){
			throw new tx_rpx_Core_Exception('encryptionKey not set');
		}
		return $GLOBALS ['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Encryption.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Encryption.php']);
}
