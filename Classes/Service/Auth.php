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
/**
 * Service to authentify the user with rpx
 */

require_once t3lib_extMgm::extPath ( 'rpx' ) . 'Classes/Configuration/Configuration.php';
require_once t3lib_extMgm::extPath ( 'sv' ) . 'class.tx_sv_auth.php';

/**
 * Service "RPX Auth Service" for the "rpx" extension.
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 * @author	Axel Jung <axel.jung@aoemedia.de>
 */
class tx_rpx_Service_Auth extends tx_sv_auth implements t3lib_Singleton {
	/**
	 * @var tx_rpx_Configuration_Configuration
	 */
	protected $configuration;
	/**
	 * @var string
	 */
	protected $prefixId = 'tx_rpx_Service_Auth'; // Same as class name
	/**
	 * @var string
	 */
	protected $scriptRelPath = 'Classes/Service/Auth.php'; // Path to this script relative to the extension dir.
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
	 * @var string
	 */
	private $error_page;
	
	/**
	 * @return	boolean
	 */
	public function init() {
		$available = parent::init ();
		$this->configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$this->configuration->initConfigurationForHash(t3lib_div::_GET('configurationHash'));
		
		if (FALSE === isset ( $GLOBALS ['TYPO3_CONF_VARS']['SYS']['encryptionKey'])) {
			return FALSE;
		}
		if ( !$this->configuration->getAPIKey() ) {
			return FALSE;
		}
		if ( !$this->configuration->getRPXDomain() ) {
			return FALSE;
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
				$user ['authenticated'] = FALSE;
				t3lib_div::devLog('invalid login detected: '.$this->username, 'rpx',2);
				return $user;
			}
			if ($this->isRPXResponse ()) {
				try {
					$this->parseRuntimeConfig ();
					$this->checkDomain();
					$responseXml = $this->getConnector ()->auth_info ( $_POST ['token'] );
					$profile = $this->getFactory ()->createProfile ( $responseXml );
					$user = $this->autoCreateUser ( $profile );
					$user ['authenticated'] = TRUE;
					return $user;
				} catch (Exception $e ) {
					t3lib_div::devLog('rpx Fatal error: '.$e->getMessage(), 'rpx',2);
					$this->redirectError();
					return FALSE;
				}
			}
		}
		return FALSE;
	}
	/**
	 * Authenticate a user
	 *
	 * @param	array $user		Data of user.
	 * @return	integer 100|200|-1
	 */
	public function authUser($user) {
		if (!$this->isRPXResponse ()) {
			return 100;
		}
		if ($user ['authenticated'] === FALSE) {
			$this->redirectError();
			return - 1;
		}
		t3lib_div::devLog('rpx login successfull', 'rpx', -1);
		$this->redirect();
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
			
			$this->connector = t3lib_div::makeInstance ( 'tx_rpx_Core_Connector', $this->configuration->getAPIKey(), $this->configuration->getRPXDomain() );
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
		return FALSE !== stripos ( $this->username, $this->configuration->getImportedFEUserPrefix() );
	}
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @return array
	 */
	private function autoCreateUser(tx_rpx_Core_Profile $profile) {
		$table = $this->authInfo ['db_user'] ['table'];
		$prefix = $this->configuration->getImportedFEUserPrefix();
		$check_pid_clause = $this->authInfo ['db_user'] ['check_pid_clause'];
		$enable_clause = $this->authInfo ['db_user'] ['enable_clause'];
		$checkPidList = $this->authInfo ['db_user'] ['checkPidList'];
		$username_column = $this->authInfo ['db_user'] ['username_column'];
		$userident_column = $this->authInfo ['db_user'] ['userident_column'];
		$usergroup_column = $this->authInfo ['db_user'] ['usergroup_column'];
		try {
			$user = $this->getUserStorage ()->getUser ( $profile, $table, $check_pid_clause, $enable_clause );
			t3lib_div::devLog('existing user found', 'rpx');
		} catch ( tx_rpx_Core_UserNotFoundException $e ) {
			t3lib_div::devLog('user not found - try to add one', 'rpx');
			try {
				$this->getUserStorage ()->add ( $profile, $prefix, $table, $checkPidList, $this->fe_user_groups,$username_column ,$userident_column,$usergroup_column);
				$user = $this->getUserStorage ()->getUser ( $profile, $table, $check_pid_clause, $enable_clause );
			}
			catch (Exception $e) {
				 t3lib_div::devLog('error on creating user:'.$e->getMessage(),'rpx');
				 throw $e;
			}
			t3lib_div::devLog('new user created', 'rpx');
		}
		return $user;
	}
	/**
	 * check the domain agains the allowed domains
	 * @throws tx_rpx_Core_Exception
	 */
	private function checkDomain(){
		if ( $this->configuration->getAllowedDomains() && null !== $this->authInfo['HTTP_HOST']){
			$domains = explode(';',$this->configuration->getAllowedDomains());
			$host = $this->authInfo['HTTP_HOST'];
			foreach($domains as $domain){
				$domain = trim($domain);
				if(empty($domain)){
					continue;
				}
				if(FALSE !== strpos($domain,'*')){
					$search= substr($domain,strpos($domain,'*'));
					if(FALSE !== strpos($domain,$search)){
						return;
					}
				}else{
					if(FALSE !== strpos($host,$domain)){
						return;
					}
				}
			}
			throw new tx_rpx_Core_Exception('invalid domain: '.$host);
		}
	}
	
	/**
	 * @return string
	 */
	private function parseRuntimeConfig() {
		if (FALSE === $_GET ['verify']) {
			throw new tx_rpx_Core_Exception ( 'Invalid return url' );
		}
		$params = array();
		$params['pid'] = $_GET ['pid'];
		$params['fe_groups'] = $_GET ['fe_groups'];
		$params['redirect'] = $_GET ['redirect'];
		$params['configurationHash'] = $_GET ['configurationHash'];
		$params['error'] = $_GET ['error'];
		$verify = $_GET ['verify'];
		$this->getEncryption ()->validate ($params,$verify );
		
		$this->fe_user_groups = $params['fe_groups'];
		$this->redirect_page = $params['redirect'];
		$this->error_page = $params['error'];
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
			t3lib_div::devLog('redirect to: '.$this->redirect_page, 'rpx');
		}
	}
	/**
	 * redirect to page if it requested
	 */
	public function redirectError() {
		if (! empty ( $this->error_page )) {
			header ( 'HTTP/1.1 303 See Other' );
			header ( 'Location: ' . t3lib_div::locationHeaderUrl ( $this->error_page ) );
			t3lib_div::devLog('redirect to: '.$this->error_page, 'rpx');
		}
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Service/Auth.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Service/Auth.php']);
}
