<?php
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Encryption.php';

/**
 * tx_rpx_Core_Encryption test case.
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
		$ext_conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['rpx'] );
		unset($ext_conf['encryption_key']);
		$GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['rpx'] = unserialize($ext_conf);
		$this->encryption->encrypt('dd');
	}
	/**
	 * Tests tx_rpx_Core_Encryption->encrypt()
	 * @test
	 */
	public function encrypt() {
		$value = '1,233';
		$test = $this->encryption->encrypt($value);
		$this->assertNotEquals($value,$test);
	}
	/**
	 * Tests tx_rpx_Core_Encryption->decrypt()
	 * @test
	 */
	public function decrypt() {
		$value = '1,233';
		$test = $this->encryption->encrypt($value);
		$test = $this->encryption->decrypt($test);
		$this->assertEquals($value,$test);
	}

}

