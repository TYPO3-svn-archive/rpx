<?php
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'UserNotFoundException.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'DatabaseException.php';
/**
 * Import the Profile in FE User Table
 */
class tx_rpx_Core_UserStorage {
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @param string $prefix
	 * @param string $table
	 * @param string $pid
	 * @param string $groups
	 * @param string $username_column
	 * @param string $userident_column
	 * @param string $usergroup_column
	 */
	public function add(tx_rpx_Core_Profile $profile,$prefix,$table,$pid,$groups,$username_column,$userident_column,$usergroup_column){
		$values = array();
		$values['tx_rpx_identifier'] = $profile->getIdentifier();
		$values[$username_column] = uniqid($prefix);
		$values[$userident_column] = uniqid($prefix);
		$values['pid'] = $pid;
		$values[$usergroup_column] = $groups;
		$values['crdate'] = time();
		if(null !== $profile->getVerifiedEmail()){
			$values['email'] = $profile->getVerifiedEmail();
		}
		if(FALSE === $this->getDb()->exec_INSERTquery($table,$values)){
			throw new tx_rpx_Core_DatabaseException('insert not successfull'.mysql_error());
		}
	}
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @param string $table
	 * @return array
	 * @throws tx_rpx_Core_UserNotFoundException
	 */
	public function getUser(tx_rpx_Core_Profile $profile,$table,$check_pid_clause = '',$enable_clause=''){
		$where = 'tx_rpx_identifier=' . $this->getDb()->fullQuoteStr($profile->getIdentifier(), $table) ;
		$where .= $check_pid_clause .$enable_clause;
		$record =  $this->getDb()->exec_SELECTgetRows('*',$table,$where);
		if(empty($record)){
			throw new tx_rpx_Core_UserNotFoundException('user not found with ');
		}
		return $record[0];
	}
	/**
	 * @return t3lib_DB
	 */
	protected function getDb(){
		return $GLOBALS['TYPO3_DB'];
	} 
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserStorage.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserStorage.php']);
}
