<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 Axel Jung <axel.jung@aoemedia.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
		$test =  $this->creatHash($values);
		if($verify !== $this->creatHash($values)){
			throw new tx_rpx_Core_Exception('invalid verify value: '.$verify.' expected: '.$test);
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
