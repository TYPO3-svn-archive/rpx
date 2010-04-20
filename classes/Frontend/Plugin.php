<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010  <>
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
require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR. 'Encryption.php';
/**
 * Plugin 'RPX Login Box' for the 'rpx' extension.
 *
 * @author	Axel Jung <axel.jung@aoemedia.de>
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class tx_rpx_Frontend_Plugin extends tslib_pibase {
	/**
	 * @var string
	 */
	public $prefixId = 'tx_rpx_pi1'; // Same as class name
	/**
	 * @var string
	 */
	public $scriptRelPath = 'classes/Frontend/Plugin.php'; // Path to this script relative to the extension dir.
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
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		$this->ext_conf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] [$this->extKey] );
		$this->pi_setPiVarDefaults ();
		$this->pi_loadLL ();
		$this->pi_initPIflexForm ();
		$displayMode = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'displayMode' );
		$tokenUrl = $this->getTokenUrl ();
		if ($displayMode === 'embedded') {
			$url = $this->getRPXDomain().'openid/embed?token_url=' . $tokenUrl;
			$content = ' <iframe src="' . $url . '"  scrolling="no"  frameBorder="no" allowtransparency="true"  style="width:400px;height:240px"></iframe> ';
		} else {
			$js = '';
			$js .= 'var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");'.PHP_EOL;
			$js .= 'var rpxJsHost = document.write(unescape("%3Cscript src=\'" + rpxJsHost + "rpxnow.com/js/lib/rpx.js\' type=\'text/javascript\'%3E%3C/script%3E"));'.PHP_EOL;
			$js .= 'RPXNOW.overlay = true;'.PHP_EOL;
			$js .= 'RPXNOW.language_preference = '.$this->LLkey.';'.PHP_EOL;
			$GLOBALS['TSFE']->getPageRenderer()->addJsInlineCode('tx_rpx',$js, FALSE);
			$url = $this->getRPXDomain().'openid/v2/signin?token_url=' . $tokenUrl;
			$content = '<br/><br/><br/><br/><br/><a class="rpxnow" onclick="return false;" href="' . $url . '"> '.$this->pi_getLL('sign_in_label').' </a>';
		}
		return $this->pi_wrapInBaseClass ( $content );
	}
	/**
	 * @return string
	 */
	private function getRPXDomain(){
		return $this->ext_conf['rpx_domain'];
	}

	/**
	 * @return string
	 */
	private function getTokenUrl() {
		$url = $this->getCurrentUrl ();
		$url = $this->addLoginParameters ( $url );
		return urlencode ( $url );
	}
	private function getCurrentUrl() {
		if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
			$proto = "https";
			$standard_port = '443';
		} else {
			$proto = 'http';
			$standard_port = '80';
		}
		$authority = $_SERVER ['HTTP_HOST'];
		if (strpos ( $authority, ':' ) === FALSE && 
		$_SERVER ['SERVER_PORT'] != $standard_port) {
			$authority .= ':' . $_SERVER ['SERVER_PORT'];
		}
		if (isset ( $_SERVER ['REQUEST_URI'] )) {
			$request_uri = $_SERVER ['REQUEST_URI'];
		} else {
			$request_uri = $_SERVER ['SCRIPT_NAME'] . $_SERVER ['PATH_INFO'];
			$query = $_SERVER ['QUERY_STRING'];
			if (isset ( $query )) {
				$request_uri .= '?' . $query;
			}
		}
		return $proto . '://' . $authority . $request_uri;
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
		$fe_groups = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'fe_groups');
		$redirectPageId = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'redirectPageId');
		$redirectPage = $this->cObj->getTypoLink_URL($redirectPageId);
		$encryption = t3lib_div::makeInstance('tx_rpx_Core_Encryption');
		$url .= '&conf='.$encryption->encrypt($fe_groups.':'.$redirectPage);
		return $url;
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']);
}