<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010  <>
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
/**
 * Service to authentify the user with rpx
 */

require_once t3lib_extMgm::extPath ( 'sv' ) . 'class.tx_sv_auth.php';
/**
 * Service "RPX Auth Service" for the "rpx" extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class tx_rpx_Service_Auth extends tx_sv_auth implements t3lib_Singleton {
	/**
	 * @var string
	 */
	protected $prefixId = 'tx_rpx_Service_Auth'; // Same as class name
	/**
	 * @var string
	 */
	protected $scriptRelPath = 'classes/Service/Auth.php'; // Path to this script relative to the extension dir.
	/**
	 * @var string
	 */
	protected $extKey = 'rpx'; // The extension key.
	/**
	 * @var $conf array
	 */
	private $conf;
	/**
	 * @var $tx_rpx_Core_UserStorage
	 */
	private $userStorage;
	/**
	 * @var tx_rpx_Core_Connector
	 */
	private $connector;
	/**
	 * @var tx_rpx_Core_Factory
	 */
	private $factory;
	/**
	 * @var tx_rpx_Core_Encryption
	 */
	private $encryption;
	/**
	 * @var string
	 */
	private $fe_user_groups;
	/**
	 * @var string
	 */
	private $redirect_page;
	
	/**
	 * @return	boolean
	 */
	public function init() {
		$available = parent::init ();
		$this->conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] [$this->extKey] );
		if (FALSE === isset ( $this->conf ['imported_fe_user_prefix'] )) {
			throw new Exception ( 'imported_fe_user_prefix isnot set in extConf' );
		}
		if (FALSE === isset ( $this->conf ['api_key'] )) {
			throw new Exception ( 'api_key isnot set in extConf' );
		}
		if (FALSE === isset ( $this->conf ['rpx_domain'] )) {
			throw new Exception ( 'rpx_domain isnot set in extConf' );
		}
		return $available;
	}
	/**
	 * @param string $subType
	 * @param array $loginData
	 * @param array $authenticationInformation
	 */
	public function initAuth($subType, array $loginData, array $authenticationInformation) {
		$this->loginData = $loginData;
		$this->authInfo = $authenticationInformation;
		$this->username = $this->loginData ['uname'];
	}
	/**
	 * Find a user (eg. look up the user record in database when a login is sent)
	 *
	 * @return	mixed		user array or false
	 */
	
	public function getUser() {
		
		if ($this->loginData ['status'] === 'login') {
			if ($this->isImportedLoginName ()) {
				$user = array ();
				$user ['invalid'] = TRUE;
				return $user;
			}
			if ($this->isRPXResponse ()) {
				try {
					$responseXml = $this->getConnector ()->auth_info ( $_POST ['token'] );
					$profile = $this->getFactory ()->createProfile ( $responseXml );
					return $this->autoCreateUser ( $profile );
				} catch ( tx_rpx_Core_Exception $e ) {
					//exit('Error on rpx login: '.$e->getMessage());
					return FALSE;
				}
			}
		}
		return FALSE;
	}
	/**
	 * Authenticate a user
	 *
	 * @param	array		Data of user.
	 * @return	integer 100|200|-1
	 */
	public function authUser($user) {
		if (isset ( $user ['invalid'] ) && $user ['invalid'] === TRUE) {
			return - 1;
		}
		return 200;
	
	}
	/**
	 * @return tx_rpx_Core_UserStorage
	 */
	protected function getUserStorage() {
		if (FALSE === isset ( $this->userStorage )) {
			require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'UserStorage.php';
			$this->userStorage = t3lib_div::makeInstance ( 'tx_rpx_Core_UserStorage' );
		}
		return $this->userStorage;
	}
	/**
	 * @return tx_rpx_Core_Connector
	 */
	protected function getConnector() {
		if (FALSE === isset ( $this->connector )) {
			require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Connector.php';
			
			$this->connector = t3lib_div::makeInstance ( 'tx_rpx_Core_Connector', $this->conf ['api_key'], $this->conf ['rpx_domain'] );
		}
		return $this->connector;
	}
	/**
	 * @return tx_rpx_Core_Factory
	 */
	protected function getFactory() {
		if (FALSE === isset ( $this->factory )) {
			require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Factory.php';
			$this->factory = t3lib_div::makeInstance ( 'tx_rpx_Core_Factory' );
		}
		return $this->factory;
	}
	/**
	 * @return tx_rpx_Core_Encryption
	 */
	protected function getEncryption() {
		if (FALSE === isset ( $this->encryption )) {
			require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Encryption.php';
			$this->encryption = t3lib_div::makeInstance ( 'tx_rpx_Core_Encryption' );
		}
		return $this->encryption;
	}
	/**
	 * @return boolean
	 */
	private function isImportedLoginName() {
		return FALSE !== stripos ( $this->username, $this->conf ['imported_fe_user_prefix'] );
	}
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @return array
	 */
	private function autoCreateUser(tx_rpx_Core_Profile $profile) {
		$table = $this->authInfo ['db_user'] ['table'];
		$prefix = $this->conf ['imported_fe_user_prefix'];
		$check_pid_clause = $this->authInfo ['db_user'] ['check_pid_clause'];
		$enable_clause = $this->authInfo ['db_user'] ['enable_clause'];
		$checkPidList = $this->authInfo ['db_user'] ['checkPidList']; //TODO verify valkue
		$this->parseRuntimeConfig ();
		try {
			$user = $this->getUserStorage ()->getUser ( $profile, $table, $check_pid_clause, $enable_clause );
		} catch ( tx_rpx_Core_UserNotFoundException $e ) {
			
			$this->getUserStorage ()->add ( $profile, $prefix, $table, $checkPidList, $this->fe_user_groups );
			$user = $this->getUserStorage ()->getUser ( $profile, $table, $check_pid_clause, $enable_clause );
		}
		return $user;
	}
	/**
	 * @return string
	 */
	private function parseRuntimeConfig() {
		if (FALSE === $_GET ['conf']) {
			throw new tx_rpx_Core_Exception ( 'Invalid return url' );
		}
		$conf = $_GET ['conf'];
		$values = $this->getEncryption ()->decrypt ( $conf );
		list ( $this->fe_user_groups, $this->redirect_page ) = explode ( ':', $values );
		$this->redirect ();
	}
	/**
	 * @return boolean
	 */
	private function isRPXResponse() {
		return isset ( $_POST ['token'] );
	}
	/**
	 * redirect to page if it requested
	 */
	public function redirect() {
		if (! empty ( $this->redirect_page )) {
			header ( 'HTTP/1.1 303 See Other' );
			header ( 'Location: ' . t3lib_div::locationHeaderUrl ( $this->redirect_page ) );
		}
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Service/Auth.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Service/Auth.php']);
}