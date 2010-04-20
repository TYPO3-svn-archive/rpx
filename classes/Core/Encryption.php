<?php
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'Exception.php';
/**
 * Encryption Service
 */
class tx_rpx_Core_Encryption {
	/**
	 * @param string $value
	 * @return string
	 */
	public function encrypt($value){
		$verify = $this->creatHash($value);
		return base64_encode($value.'|'.$verify);
	}
	/**
	 * @param string $encrypted
	 * @return string
	 * @throws tx_rpx_Core_Exception
	 */
	public function decrypt($encrypted){
		$encrypted = base64_decode($encrypted);
		list($value,$verify) = explode('|',$encrypted);
		if($verify !== $this->creatHash($value)){
			throw new tx_rpx_Core_Exception('invalid verify value'.$verify);
		}
		return $value;
	}
	/**
	 * @param string $value
	 * @return string
	 */
	private function creatHash($value){
		return md5($this->getEncryptionKey().$value.$this->getEncryptionKey());
	}
	/**
	 * @return string
	 * @throws tx_rpx_Core_Exception
	 */
	private function getEncryptionKey(){
		$ext_conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['rpx'] );
		if(FALSE === isset($ext_conf['encryption_key']) || trim($ext_conf['encryption_key']) ===''){
			throw new tx_rpx_Core_Exception('encryption_key not set or empty');
		}
		return $ext_conf['encryption_key'];
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Encryption.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Encryption.php']);
}
