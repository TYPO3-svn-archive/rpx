<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2011 Nikola Stojiljkovic <nikola.stojiljkovic(at)essentialdots.com>
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

require_once t3lib_extMgm::extPath ( 'rpx' ) . 'classes/Core/Encryption.php';

/**
 * Configuration used by the extensions
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class tx_rpx_Configuration_Configuration implements t3lib_Singleton {

	/**
	 * @var t3lib_cache_frontend_VariableFrontend
	 */
	protected $cacheInstance;
	
	/**
	 * @var array
	 */
	protected $configuration;
	
	/**
	 * @var tx_rpx_Core_Encryption
	 */
	protected $encryptionService;
	
	/**
	 * constructor
	 */
	public function __construct() {
		$this->cacheInstance = $GLOBALS['typo3CacheManager']->getCache('rpx');
		
		$this->configuration['ext'] = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rpx'] ? unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rpx']) : array();
		
		if (t3lib_div::_GET('configurationHash')) {
			$this->initConfigurationForHash(t3lib_div::_GET('configurationHash'));
		} else {
			$this->initConfigurationForTSArray($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_rpx.']);			
		}
		
		$this->encryptionService = t3lib_div::makeInstance('tx_rpx_Core_Encryption');
	}

	/**
	 * returns configurationvalue for the given key
	 *
	 * @param string $key
	 * @param string $provider
	 * @return string
	 */
	protected function get($key, $provider) {
		$result = NULL;
		
		if ($this->configuration['ts'][$provider.'.'][$key]) {
			$result = $this->configuration['ts'][$provider.'.'][$key];
		} elseif ($this->configuration['ts']['default.'][$key]) {
			$result = $this->configuration['ts']['default.'][$key];
		} else {
			$result = $this->configuration['ext'][$key];
		}
		return $result;
	}

	/**
	 * returns configurationvalue for the given key
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param string $provider
	 * @return void
	 */
	protected function set($key, $value, $provider) {
		if ($provider) {
			$this->configuration['ts'][$provider.'.'][$key] = $value;
		} else {
			$this->configuration['ext'][$key] = $value;
		}
	}
	
	/**
	 * Initialize configuration for the given $configurtion hash
	 * 
	 * @param unknown_type $configurationHash
	 */
	public function initConfigurationForHash($configurationHash) {
		if ($configurationHash && $this->cacheInstance->has($configurationHash)) {
			$this->configuration = $this->cacheInstance->get($configurationHash);
		} else {
			// TODO: throw Exception
		}
	}
	
	/**
	 * Initialize configuration for the given $configurtion hash
	 * 
	 * @param unknown_type $configurationHash
	 */
	public function initConfigurationForTSArray($tsConfiguration) {
		$this->configuration['ts'] = $tsConfiguration['settings.'] ? $tsConfiguration['settings.'] : array();
	}
	
	/**
	 * Returns the hash for the current configuration
	 * 
	 * @return string
	 */
	public function getConfigurationHash() {
		$configurationHash = $this->encryptionService->creatHash($this->configuration);
		
		if (!$this->cacheInstance->has($configurationHash)) {
			$this->cacheInstance->set($configurationHash, $this->configuration);
		}
		
		return $configurationHash;
	}
		
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getRedirectParameter($provider = 'default') {
		return $this->get('redirect_parameter', $provider);
	}
	
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getRPXDomain($provider = 'default'){
		return $this->get('rpx_domain', $provider);
	}
	
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getRPXProjectName($provider = 'default') {
		return $this->get('rpx_project', $provider);
	}

	/**
	 * @param string $provider
	 * @return string
	 */
	public function getAPIKey($provider = 'default') {
		return $this->get('api_key', $provider);
	}
	
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getImportedFEUserPrefix($provider = 'default') {
		return $this->get('imported_fe_user_prefix', $provider);
	}
		
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getAllowedDomains($provider = 'default') {
		return $this->get('allowed_domains', $provider);
	}
	
	/**
	 * @param string $provider
	 * @return string
	 */
	public function getImportFields($provider = 'default') {
		return $this->get('import_fields', $provider);
	}
	
	/**
	 * @param string $importFields
	 * @param string $provider
	 * @return void
	 */
	public function setImportFields($importFields, $provider = 'default') {
		return $this->set('import_fields', $importFields, $provider);
	}
}

if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Configuration/Configuration.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Configuration/Configuration.php']);
}