<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nikola Stojiljkovic <nikola.stojiljkovic(at)essentialdots.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class Tx_Rpx_Hook_AoeLogin implements t3lib_Singleton {
	/**
	 * @var tx_rpx_Configuration_Configuration
	 */
	protected $configuration;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$this->configuration->initConfigurationForHash(t3lib_div::_GET('configurationHash'));
	}
	
	/**
	 * @param array $params
	 * @param Tx_AoeLogin_Domain_Validator_LastResetDateValidator $pObj
	 * @return Tx_Extbase_Validation_Error
	 */
	public function filterUsers(&$params, &$pObj) {
		$hasRPXAccount = false;
		$users = &$params['users'];
		foreach ($users as $i => $user) {
				/* @var $user Tx_AoeLogin_Domain_Model_FrontendUser */
			if ($this->isRPXUser($user)) {
				$hasRPXAccount = true;
				unset($users[$i]);
			}
		}
		
		if ($hasRPXAccount) {
			return new Tx_Extbase_Validation_Error(Tx_Extbase_Utility_Localization::translate('error.1328140801', 'rpx'), 1328140801);
		} else {
			return false;
		}
	}
	
	/**
	 * @param Tx_AoeLogin_Domain_Model_FrontendUser $user
	 * @return boolean
	 */
	protected function isRPXUser($user) {
		return FALSE !== stripos ( $user->getUsername(), $this->configuration->getImportedFEUserPrefix() );
	}
	
}