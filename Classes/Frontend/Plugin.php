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

/**
 * Frontend Plugin to render the login box of the RPX
 */
require_once (PATH_tslib . 'class.tslib_pibase.php');
require_once t3lib_extMgm::extPath ( 'rpx' ) . 'Classes/Configuration/Configuration.php';
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR. 'Encryption.php';
/**
 * Plugin 'RPX Login Box' for the 'rpx' extension.
 *
 * @package	TYPO3
 * @subpackage	tx_rpx
 * @author	Axel Jung <axel.jung@aoemedia.de>
 */
class tx_rpx_Frontend_Plugin extends tslib_pibase {
	/**
	 * @var string
	 */
	public $prefixId = 'tx_rpx_pi1'; // Same as class name
	/**
	 * @var string
	 */
	public $scriptRelPath = 'Classes/Frontend/Plugin.php'; // Path to this script relative to the extension dir.
	/**
	 * @var string
	 */
	public $extKey = 'rpx'; // The extension key.
	/**
	 * @var boolean
	 */
	public $pi_checkCHash = true;
	/**
	 * @var array
	 */
	private $ext_conf;
	/**
	 * @var tx_rpx_Configuration_Configuration
	 */
	private $configuration;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content The PlugIn content
	 * @param	array		$conf The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->configuration = t3lib_div::makeInstance('tx_rpx_Configuration_Configuration');
		$this->configuration->initConfigurationForTSArray($conf);
		
		$this->pi_setPiVarDefaults ();
		$this->pi_loadLL ();
		$this->pi_initPIflexForm ();
		$displayMode = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'displayMode' );
		$tokenUrl = $this->getTokenUrl ();
		if($this->isConfigured() === FALSE){
			$content = $this->pi_getLL('config_warning');
		}else{
			if ($displayMode === 'embedded') {
				$url = $this->configuration->getRPXDomain().'openid/embed?token_url=' . $tokenUrl;
				$content = ' <iframe src="' . $url . '"  scrolling="no"  frameBorder="no" allowtransparency="true"  style="width:400px;height:240px"></iframe> ';
			} else {
				
				$js = <<<RPX_JS
(function() {
    if (typeof window.janrain != 'object') {
		window.janrain = {};
    }
    window.janrain.settings = {};
    
    janrain.settings.tokenUrl = '$tokenUrl';

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
      window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/###PROJECTNAME###/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/###PROJECTNAME###/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();
RPX_JS;
				$js = str_replace('###PROJECTNAME###',$this->configuration->getRPXProjectName(),$js);


				$GLOBALS['TSFE']->getPageRenderer()->addJsInlineCode('tx_rpx',$js, FALSE);
				$url = $this->configuration->getRPXDomain().'openid/v2/signin?token_url=' . $tokenUrl;
				$content = '<a class="janrainEngage" onclick="return false;" href="#"> '.$this->pi_getLL('sign_in_label').' </a>';
			}
		}
		return $this->pi_wrapInBaseClass ( $content );
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function addLoginParameters($url) {
		if(FALSE === strpos($url,'?')){
			$url .= '?';
		}else{
			$url .= '&';
		}
		$url .= 'logintype=login';
		$url .= '&user=rpx';
		$url .= '&pass=rpx';
		
		$encryption = t3lib_div::makeInstance('tx_rpx_Core_Encryption');
		$params = array();
		$params['pid'] = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'pid');
		$params['fe_groups'] = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'fe_groups');
		if(NULL !== $redirectUrl = $this->getRedirectUrl()) {
			$params['redirect'] = $redirectUrl;
		}
		$params['configurationHash'] = $this->configuration->getConfigurationHash();
		$params['error'] = $this->getUrl($this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'errorPid'));
		$url .= '&verify='.$encryption->creatHash($params);
		foreach($params as $key=>$value){
			$url .= '&'.$key.'='.$value;
		}
		
		return $url;
	}

	/**
	 * @return string
	 */
	private function getRedirectUrl() {
		$url = NULL;
		$staticRedirectPageId  = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'redirectPageId');
		$dynamicRedirectPageId = t3lib_div::_GP( $this->configuration->getRedirectParameter() );
		if($dynamicRedirectPageId !== NULL && (integer) $dynamicRedirectPageId > 0) {
			$url = $this->getUrl( $dynamicRedirectPageId );
		} elseif($staticRedirectPageId !== NULL && (integer) $staticRedirectPageId > 0) {
			$url = $this->getUrl($staticRedirectPageId);
		}
		else {
			$url = $this->getUrl($GLOBALS['TSFE']->id);
		}
		return $url;
	}
	/**
	 * @return string
	 */
	private function getReturnUrl() {
		$conf = array();
		$conf['parameter'] = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'returnUrlPid');
		$conf['returnLast'] = 'url';
		$conf['forceAbsoluteUrl'] = TRUE;
		$conf['forceAbsoluteUrl.']['scheme'] = t3lib_div::getIndpEnv('TYPO3_SSL') ? 'https' : 'http';
		$conf['additionalParams'] = array();
		
		$url = $this->cObj->typoLink('',$conf);
		return $url;
	}

	/**
	 * @return string
	 */
	private function getTokenUrl() {
		$url = $this->getReturnUrl ();
		$url = $this->addLoginParameters ( $url );
		return  $url ;
	}
	/**
	 * @param string $pid
	 * @return string
	 */
	private function getUrl($pid){
		$conf = array();
		$conf['parameter'] = $pid;
		$conf['returnLast'] = 'url';
		$conf['linkAccessRestrictedPages'] = TRUE;
		return $this->cObj->typoLink('',$conf);
	}

	/**
	 * @return boolean
	 */
	private function isConfigured(){
		if( !$this->configuration->getRPXDomain() || '' == trim($this->configuration->getRPXDomain())){
			return FALSE;
		}else{
			return TRUE;
		}
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']);
}
