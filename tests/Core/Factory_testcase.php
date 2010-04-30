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

require_once  dirname ( __FILE__ ) .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR.'classes'. DIRECTORY_SEPARATOR.'Core'. DIRECTORY_SEPARATOR.'Factory.php';

/**
 * tx_rpx_Core_Factory test case.
 */
class Core_Factory_testcase extends tx_phpunit_testcase {
	
	/**
	 * @var tx_rpx_Core_Factory
	 */
	private $factory;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->factory = new tx_rpx_Core_Factory();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->factory = null;
		parent::tearDown ();
	}
	/**
	 * Tests tx_rpx_Core_Factory->createProfile()
	 * @test
	 */
	public function createProfile() {
		$dom = DOMDocument::load(dirname(__FILE__).DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'response_Profile.xml');
		$profile = $this->factory->createProfile($dom);
		$this->assertType('tx_rpx_Core_Profile',$profile);
		$this->assertEquals('max.muster',$profile->getDisplayName());
		$this->assertEquals('max.muster@googlemail.com',$profile->getEmail());
		$this->assertEquals('https://www.google.com/accounts/o8/id?id=AIsss',$profile->getIdentifier());
		$this->assertEquals('Max',$profile->getGivenName());
		$this->assertEquals('Mustermann',$profile->getFamilyName());
		$this->assertEquals('Max Mustermann',$profile->getFormattedName());
		$this->assertEquals('max.muster',$profile->getPreferredUsername());
		$this->assertEquals('Google',$profile->getProviderName());
		$this->assertEquals('max.muster@googlemail.com',$profile->getVerifiedEmail());
		$this->assertEquals('max.muster',$profile->getPreferredUsername());
		$this->assertEquals('1976-06-26',$profile->getBirthday());
		$this->assertEquals('-08:00',$profile->getUtcOffset());
		$this->assertEquals('https://www.google.com/',$profile->getUrl());
		$this->assertEquals('01777777777',$profile->getPhoneNumber());
		$this->assertEquals('http://www.example.com/',$profile->getPhoto());
		$this->assertEquals('Borsigstr. 3,65185 Wiesbaden, Germany',$profile->getAddressFormatted());
		$this->assertEquals('Borsigstr. 3',$profile->getStreetAddress());
		$this->assertEquals('Germany',$profile->getCountry());
		$this->assertEquals('Hessen',$profile->getRegion());
		$this->assertEquals('Wiesbaden',$profile->getLocality());
	}
}
