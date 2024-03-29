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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'UserStorage.php';

/**
 * tx_rpx_Core_UserStorage test case.
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class Core_UserStorage_testcase extends tx_phpunit_database_testcase {
	/**
	 * @var boolean
	 */
	protected $backupGlobals = true;
	/**
	 * @var tx_rpx_Core_UserStorage
	 */
	private $userStorage;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->userStorage = new tx_rpx_Core_UserStorage ();
		$this->createDatabase ();
		$this->useTestDatabase ();
		$GLOBALS ['TYPO3_DB']->admin_query ( file_get_contents ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'fe_users.sql' ) );
		$this->importExtensions ( array ('rpx' ) );
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->userStorage = null;
		$this->dropDatabase ();
		parent::tearDown ();
	}
	/**
	 * Tests tx_rpx_Core_UserStorage->createProfile()
	 * @test
	 */
	public function add() {
			/* @var $configuration tx_rpx_Configuration_Configuration */
		$configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$configuration->setImportFields('displayName:name;verifiedEmail:email;url:www;country:country;locality:city;postalCode:zip;addressFormatted:address');
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ( 'identitier' ) );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2', 'username', 'password', 'usergroup' );
	}
	/**
	 * Tests tx_rpx_Core_UserStorage->getUser()
	 * @test
	 * @expectedException tx_rpx_Core_UserNotFoundException
	 */
	public function getUserWithException() {
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ( 'identitier' ) );
		$this->userStorage->getUser ( $profile, 'fe_users' );
	}
	/**
	 * Tests tx_rpx_Core_UserStorage->getUser()
	 * @test
	 */
	public function getUser() {
			/* @var $configuration tx_rpx_Configuration_Configuration */
		$configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$configuration->setImportFields('displayName:name;verifiedEmail:email;photo:image;url:www;country:country;locality:city;postalCode:zip;addressFormatted:address');
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ('setIdentifier') );
		$profile->setVerifiedEmail ( uniqid ('setVerifiedEmail') );
		$profile->setDisplayName ( uniqid ('setDisplayName') );
		$profile->setPhoto ( uniqid ('setPhoto') );
		$profile->setUrl ( uniqid ('setUrl') );
		$profile->setCountry ( uniqid ('setCountry') );
		$profile->setLocality ( uniqid ('setLocality') );
		$profile->setPostalCode ( '65195' );
		$profile->setAddressFormatted ( uniqid ('setAddressFormatted') );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2', 'username', 'password', 'usergroup' );
		$record = $this->userStorage->getUser ( $profile, 'fe_users' );
		$this->assertEquals ( $profile->getIdentifier (), $record ['tx_rpx_identifier'] );
		$this->assertContains ( 'testprefix', $record ['username'] );
		$this->assertContains ( 'testprefix', $record ['password'] );
		$this->assertEquals ( $profile->getVerifiedEmail (), $record ['email'] );
		$this->assertEquals ( $profile->getDisplayName (), $record ['name'] );
		//$this->assertEquals ( $profile->getPhoto (), $record ['image'] );
		$this->assertEquals ( $profile->getUrl (), $record ['www'] );
		$this->assertEquals ( $profile->getCountry (), $record ['country'] );
		$this->assertEquals ( $profile->getLocality (), $record ['city'] );
		$this->assertEquals ( $profile->getPostalCode (), $record ['zip'] );
		$this->assertEquals ( $profile->getAddressFormatted (), $record ['address'] );
	}
	/**
	 * Tests tx_rpx_Core_UserStorage->getUser() and tx_rpx_Transform_RegexTransformator->transform()
	 * @test
	 */
	public function getUserWithTransformator() {
			/* @var $configuration tx_rpx_Configuration_Configuration */
		$configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$configuration->setImportFields('displayName:name Regex(pattern="/^(.*)$/", replacement="prefix $1 postfix");verifiedEmail:email');
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ('setIdentifier') );
		$displayName = uniqid ('setDisplayName');
		$profile->setDisplayName ( $displayName );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2', 'username', 'password', 'usergroup' );
		$record = $this->userStorage->getUser ( $profile, 'fe_users' );
		$this->assertEquals ( $profile->getIdentifier (), $record ['tx_rpx_identifier'] );
		$this->assertEquals ( 'prefix ' . $profile->getDisplayName () . ' postfix', $record ['name'] );
	}	
	/**
	 * Tests tx_rpx_Core_UserStorage->getUser() and tx_rpx_Transform_RegexTransformator->transform()
	 * @test
	 */
	public function getUserWithMultipleTransformators() {
			/* @var $configuration tx_rpx_Configuration_Configuration */
		$configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$configuration->setImportFields('displayName:name "Regex(pattern=""/^(.*)$/"", replacement=""prefix-1st $1 postfix-1st"")" | "Regex(pattern=""/^(.*)$/"", replacement=""prefix-2nd $1 postfix-2nd"")";verifiedEmail:email');
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ('setIdentifier') );
		$displayName = uniqid ('setDisplayName');
		$profile->setDisplayName ( $displayName );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2', 'username', 'password', 'usergroup' );
		$record = $this->userStorage->getUser ( $profile, 'fe_users' );
		$this->assertEquals ( $profile->getIdentifier (), $record ['tx_rpx_identifier'] );
		$this->assertEquals ( 'prefix-2nd prefix-1st ' . $profile->getDisplayName () . ' postfix-1st postfix-2nd', $record ['name'] );
	}		
}

