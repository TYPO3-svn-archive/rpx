<?php
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'UserStorage.php';

/**
 * tx_rpx_Core_UserStorage test case.
 */
class Core_UserStorage_testcase extends tx_phpunit_database_testcase {
	
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
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ( 'identitier' ) );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2','username' ,'password','usergroup');
	
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
		$profile = new tx_rpx_Core_Profile ();
		$profile->setIdentifier ( uniqid ( 'identitier' ) );
		$profile->setVerifiedEmail ( uniqid ( 'email' ) );
		$this->userStorage->add ( $profile, 'testprefix', 'fe_users', 2, '1,2','username' ,'password','usergroup' );
		$record = $this->userStorage->getUser ( $profile, 'fe_users' );
		$this->assertEquals($profile->getIdentifier(),$record['tx_rpx_identifier']);
		$this->assertContains('testprefix',$record['username']);
		$this->assertContains('testprefix',$record['password']);
		$this->assertContains($profile->getVerifiedEmail(),$record['email']);
	}
}

