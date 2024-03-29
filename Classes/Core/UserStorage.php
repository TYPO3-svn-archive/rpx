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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'UserNotFoundException.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'DatabaseException.php';
require_once t3lib_extMgm::extPath ( 'rpx' ) . 'Classes/Transform/Resolver.php';

/**
 * Import the Profile in FE User Table
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class tx_rpx_Core_UserStorage {		
	/**
	 * @var tx_rpx_Configuration_Configuration
	 */
	protected $configuration;

	/**
	 * @var tx_rpx_Transform_Resolver
	 */
	protected $transformatorResolver;
	
	/**
	 * constructor
	 */
	public function __construct() {
		$this->configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$this->transformatorResolver = t3lib_div::makeInstance('tx_rpx_Transform_Resolver');
	}
	
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
	public function add(tx_rpx_Core_Profile $profile, $prefix, $table, $pid, $groups, $username_column, $userident_column, $usergroup_column) {
		$values = array ();
		$values ['tx_rpx_identifier'] = $profile->getIdentifier ();
		$values ['tx_rpx_provider'] = $profile->getProviderName ();
		$values [$username_column] = uniqid ( $prefix );
		$values [$userident_column] = uniqid ( $prefix );
		$values ['pid'] = $pid;
		$values [$usergroup_column] = $groups;
		$values ['crdate'] = time ();
		$values = $this->importFields ( $profile, $values );
		if (FALSE === $this->getDb ()->exec_INSERTquery ( $table, $values )) {
			throw new tx_rpx_Core_DatabaseException ( 'insert not successfull' . mysql_error () );
		}
	}
	
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @param string $table
	 * @return array
	 * @throws tx_rpx_Core_UserNotFoundException
	 */
	public function getUser(tx_rpx_Core_Profile $profile, $table, $check_pid_clause = '', $enable_clause = '') {
		$where = 'tx_rpx_identifier=' . $this->getDb ()->fullQuoteStr ( $profile->getIdentifier (), $table );
		$where .= $check_pid_clause . $enable_clause;
		$record = $this->getDb ()->exec_SELECTgetRows ( '*', $table, $where );
		if (empty ( $record )) {
			throw new tx_rpx_Core_UserNotFoundException ( 'user not found with ' );
		}
		return $record [0];
	}
	
	/**
	 * @return t3lib_DB
	 */
	protected function getDb() {
		return $GLOBALS ['TYPO3_DB'];
	}
	
	/**
	 * @param tx_rpx_Core_Profile $profile
	 * @param array $values
	 * @return array
	 */
	protected function importFields(tx_rpx_Core_Profile $profile, array $values) {
		$importFields = $this->configuration->getImportFields($profile->getProviderName());
		if (! empty ( $importFields )) {
			foreach ( t3lib_div::trimExplode ( ';', $importFields ) as $fields ) {
				list ( $rpxField, $userFieldWithTransformatorDefinition ) = t3lib_div::trimExplode ( ':', $fields );
				list ( $userField, $transformatorDefinition ) = t3lib_div::trimExplode ( ' ', $userFieldWithTransformatorDefinition, true, 2 );
				$transformatorConfiguration = $this->transformatorResolver->parseTransformAnnotation($transformatorDefinition);
				$method = 'get' . ucfirst ( $rpxField );
				if (method_exists ( $profile, $method )) {
					$userFieldValue = call_user_func (array(  $profile, $method) );
					foreach ($transformatorConfiguration['transformators'] as $transformator) {
						$transformator = $this->transformatorResolver->createTransformator($transformator['transformatorName'], $transformator['transformOptions']);
						$userFieldValue = $transformator->transform($userFieldValue);
					}
					$values [$userField] = $userFieldValue;
				}
			}
		}
		return $values;
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserStorage.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/UserStorage.php']);
}
