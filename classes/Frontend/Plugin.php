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
	public $scriptRelPath = 'pi1/class.tx_rpx_pi1.php'; // Path to this script relative to the extension dir.
	/**
	 * @var string
	 */
	public $extKey = 'rpx'; // The extension key.
	/**
	 * @var boolean
	 */
	public $pi_checkCHash = true;
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		
		$this->pi_setPiVarDefaults ();
		$this->pi_loadLL ();
		$this->pi_initPIflexForm ();
		$displayMode = $this->pi_getFFvalue ( $this->cObj->data ['pi_flexform'], 'displayMode' );
		
		$tokenUrl = $this->getTockenUrl ();
		
		if ($displayMode === 'embedded') {
			$url = 'https://ajung.rpxnow.com/openid/embed?token_url=' . $tokenUrl;
			$content = ' <iframe src="' . $url . '"  scrolling="no"  frameBorder="no" allowtransparency="true"  style="width:400px;height:240px"></iframe> ';
		} else {
			/**
			 * <script type="text/javascript">
  var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");
  document.write(unescape("%3Cscript src='" + rpxJsHost +
"rpxnow.com/js/lib/rpx.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
  RPXNOW.overlay = true;
  RPXNOW.language_preference = 'en';
</script>


			 */
			$url = 'https://ajung.rpxnow.com/openid/v2/signin?token_url=' . $tokenUrl;
			$content = ' <a class="rpxnow" onclick="return false;" href="' . $url . '"> Sign In </a>';
		}
		return $this->pi_wrapInBaseClass ( $content );
	}
	/**
	 * @return string
	 */
	private function getTockenUrl() {
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
		//todo add login params to trigger auth service
		return $url;
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Frontend/Plugin.php']);
}