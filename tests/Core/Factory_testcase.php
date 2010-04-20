<?php
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
		$this->factory = new tx_rpx_Core_Factory(/* parameters */);
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
		$this->assertEquals('Max Mustermann',$profile->getFormatted());
		$this->assertEquals('max.muster',$profile->getPreferredUsername());
		$this->assertEquals('Google',$profile->getProviderName());
		$this->assertEquals('max.muster@googlemail.com',$profile->getVerifiedEmail());
		$this->assertEquals('max.muster',$profile->getPreferredUsername());
	}

}

