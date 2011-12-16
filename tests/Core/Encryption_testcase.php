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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Encryption.php';

/**
 * tx_rpx_Core_Encryption test case.
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class Core_Encryption_testcase extends tx_phpunit_testcase {
	
	/**
	 * @var tx_rpx_Core_Encryption
	 */
	private $encryption;
	/**
	 * @var boolean
	 */
	protected $backupGlobals = TRUE;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->encryption = new tx_rpx_Core_Encryption ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->encryption = null;
		parent::tearDown ();
	}
	/**
	 * Tests tx_rpx_Core_Encryption->encrypt()
	 * @test
	 * @expectedException tx_rpx_Core_Exception
	 */
	public function encryptWithException() {
		unset($GLOBALS ['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
		$this->encryption->creatHash(array('dd'));
	}
	/**
	 * Tests tx_rpx_Core_Encryption->creatHash()
	 * @test
	 */
	public function creatHash() {
		$value = '1,233';
		$test = $this->encryption->creatHash(array($value));
		$this->assertNotEquals($value,$test);
	}
	/**
	 * Tests tx_rpx_Core_Encryption->decrypt()
	 * @test
	 */
	public function validate() {
		$value = '1,233';
		$test = $this->encryption->creatHash(array($value));
		$this->encryption->validate(array($value),$test);
	}
	/**
	 * Tests tx_rpx_Core_Encryption->decrypt()
	 * @test
	 * @expectedException tx_rpx_Core_Exception
	 */
	public function validateWithException() {
		$this->encryption->validate(array('1,233'),'123');
	}

}

