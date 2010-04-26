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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR . 'Auth.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'UserStorage.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Connector.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Factory.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Profile.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Encryption.php';

/**
 * tx_rpx_Service_Auth test case.
 */
class Service_Auth_testcase extends tx_phpunit_testcase {
	
	/**
	 * @var tx_rpx_Service_Auth
	 */
	private $auth;
	/**
	 * @var tx_rpx_Core_UserStorage
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
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->userStorage = $this->getMock ( 'tx_rpx_Core_UserStorage', array (), array (), '', FALSE );
		$this->connector = $this->getMock ( 'tx_rpx_Core_Connector', array (), array (), '', FALSE );
		$this->factory = $this->getMock ( 'tx_rpx_Core_Factory', array (), array (), '', FALSE );
		$this->encryption = $this->getMock ( 'tx_rpx_Core_Encryption', array (), array (), '', FALSE );
		$this->auth = $this->getMock ( 'tx_rpx_Service_Auth', array ('getUserStorage', 'getConnector', 'getFactory','getEncryption' ) );
		$this->auth->expects ( $this->any () )->method ( 'getUserStorage' )->will ( $this->returnValue ( $this->userStorage ) );
		$this->auth->expects ( $this->any () )->method ( 'getConnector' )->will ( $this->returnValue ( $this->connector ) );
		$this->auth->expects ( $this->any () )->method ( 'getFactory' )->will ( $this->returnValue ( $this->factory ) );
		$this->auth->expects ( $this->any () )->method ( 'getEncryption' )->will ( $this->returnValue ( $this->encryption ) );
		$this->auth->init ();
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->auth = null;
		$this->userStorage = null;
		$this->connector = null;
		$this->factory = null;
		parent::tearDown ();
	}
	/**
	 * test the method getUser
	 * @test
	 */
	public function getUserWithInvalidUsername() {
		$conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['rpx'] );
		$prefix = $conf ['imported_fe_user_prefix'];
		$this->auth->initAuth ( 'getUser', array ('uname' => $prefix . 'test', 'status' => 'login' ), array () );
		$user = $this->auth->getUser ();
		$this->assertType ( 'array', $user );
		$this->assertFalse ( $user ['authenticated']  );
	}
	/**
	 * test the method getUser
	 * @test
	 */
	public function getUser() {
		$_POST ['token'] = 'test';
		$test = array ('authenticated'=>TRUE );
		$this->auth->initAuth ( 'getUser', array ('uname' =>  'test', 'status' => 'login' ), array () );
		$this->connector->expects ( $this->once () )->method ( 'auth_info' )->will ( $this->returnValue ( new DOMDocument () ) );
		$this->factory->expects ( $this->once () )->method ( 'createProfile' )->will ( $this->returnValue ( new tx_rpx_Core_Profile () ) );
		$this->userStorage->expects ( $this->once () )->method ( 'getUser' )->will ( $this->returnValue ( $test ) );
		$user = $this->auth->getUser ();
		$this->assertEquals ( $test ,$user);
	}
/**
	 * test the method getUser
	 * @test
	 */
	public function getUserWithAutoCreate() {
		$_POST ['token'] = 'test';
		$test = array ('authenticated'=>TRUE );
		$this->auth->initAuth ( 'getUser', array ('uname' =>  'test', 'status' => 'login' ), array () );
		$this->connector->expects ( $this->once () )->method ( 'auth_info' )->will ( $this->returnValue ( new DOMDocument () ) );
		$this->factory->expects ( $this->once () )->method ( 'createProfile' )->will ( $this->returnValue ( new tx_rpx_Core_Profile () ) );
		$this->userStorage->expects ( $this->at (0) )->method ( 'getUser' )->will ( $this->throwException ( new tx_rpx_Core_UserNotFoundException() ) );
		$this->userStorage->expects ( $this->any (2) )->method ( 'getUser' )->will ( $this->returnValue ( $test ) );
		$user = $this->auth->getUser ();
		$this->assertEquals ( $test ,$user);
	}
	
	/**
	 * test the method authUser
	 * @test
	 */
	public function authUser() {
		$this->assertEquals ( - 1, $this->auth->authUser ( array ('authenticated'=>FALSE ) ) );
		$this->assertEquals ( 200, $this->auth->authUser ( array ('authenticated'=>TRUE ) ) );
	}

}

