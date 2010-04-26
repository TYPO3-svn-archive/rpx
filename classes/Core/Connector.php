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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'Exception.php';

/**
 * Service to call the Connector
 *
 */
class tx_rpx_Core_Connector {
	/**
	 * @var string
	 */
	var $api_key;
	/**
	 * @var string
	 */
	var $base_url;
	/**
	 * @var string
	 */
	var $format = 'xml';
	/**
	 * @var string
	 */
	var $response_body = '';
	/**
	 * @param string $api_key
	 * @param string $base_url
	 */
	public function __construct($api_key, $base_url) {
		while ( $base_url [strlen ( $base_url ) - 1] == "/" ) {
			$base_url = substr ( $base_url, 0, strlen ( $base_url ) - 1 );
		}
		$this->api_key = $api_key;
		$this->base_url = $base_url;
	
	}
	/**
	 * send the auth_info
	 * @param string $token
	 * @param string $current_url optional
	 * @return DOMDocument
	 */
	public function auth_info($token, $current_url = null) {
		$args = array ("token" => $token );
		if ($current_url !== null) {
			$args ['currentUrl'] = $current_url;
		}
		return $this->apiCall ( "auth_info", $args );
	}
	/**
	 * Returns an array of identifier mappings for the specified
	 * primary key.
	 * @param strings $primary_key
	 * @return array
	 */
	public function mappings($primary_key) {
		$doc = $this->apiCall ( "mappings", array ("primaryKey" => $primary_key ) );
		$identifiers = array ();
		$xpath = new DOMXPath ( $doc );
		$nodes = $xpath->query ( "/rsp/identifiers/identifier" );
		foreach ( $nodes as $identifier_node ) {
			$identifiers [] = $identifier_node->textContent;
		}
		return $identifiers;
	}
	/**
	 * Returns a hash of primary key -> array(identifier) of all identifier mappings for this application.
	 */
	public function all_mappings() {
		$doc = $this->apiCall ( "all_mappings", array () );
		$mappings = array ();
		$xpath = new DOMXPath ( $doc );
		$mapping_nodes = $xpath->query ( "/rsp/mappings/mapping" );
		foreach ( $mapping_nodes as $mapping_node ) {
			// Get the primaryKey element
			$pk_node = $mapping_node->childNodes->item ( 0 );
			// Get the identifier elements
			$identifier_nodes = $xpath->query ( "identifiers/identifier", $mapping_node );
			$mappings [$pk_node->textContent] = array ();
			foreach ( $identifier_nodes as $id_node ) {
				$mappings [$pk_node->textContent] [] = $id_node->textContent;
			}
		}
		return $mappings;
	}
	/**
	 * Maps an identifier to a primary key from your application.
	 * @param string $identifier
	 * @param string $primary_key
	 */
	public function map($identifier, $primary_key) {
		$this->apiCall ( "map", array ("primaryKey" => $primary_key, "identifier" => $identifier ) );
	}
	/**
	 * Removes a mapping for an identifier and primary key.  Returns
	 * @param string $identifier
	 * @param string $primary_key
	 */
	public function unmap($identifier, $primary_key) {
		$this->apiCall ( "unmap", array ("primaryKey" => $primary_key, "identifier" => $identifier ) );
	}
	/**
	 * @param string $method_name
	 * @param array $partial_query
	 * @return DOMDocument
	 */
	private function apiCall($method_name, array $partial_query) {
		$partial_query ["format"] = $this->format;
		$partial_query ["apiKey"] = $this->api_key;
		$query_str = "";
		foreach ( $partial_query as $k => $v ) {
			if (strlen ( $query_str ) > 0) {
				$query_str .= "&";
			}
			$query_str .= urlencode ( $k );
			$query_str .= "=";
			$query_str .= urlencode ( $v );
		}
		$url = $this->base_url . "/api/v2/" . $method_name;
		$response_body = $this->_post ( $url, $query_str );
		$api_response = $this->_parse ( $response_body );
		$status = $this->_getMessageStatus ( $api_response );
		if ($status != 'ok') {
			throw new tx_rpx_Core_Exception ( sprintf ( "API status was not 'ok', got '%s' instead", $status ) );
		}
		return $api_response;
	}
	/**
	 * @param DOMDocument $parsed_response
	 * @return string
	 */
	private function _getMessageStatus(DOMDocument $parsed_response) {
		$root = $parsed_response->childNodes->item ( 0 );
		$node = $root->attributes->getNamedItem ( 'stat' );
		return $node->value;
	}
	/**
	 * reset
	 */
	private function _resetPostData() {
		$this->response_data = "";
	}
	/**
	 * @param string $curl_handle
	 * @param string $raw
	 */
	private function _writeResponseData($curl_handle, $raw) {
		$this->response_data .= $raw;
		return strlen ( $raw );
	}
	/**
	 * @param string $url
	 * @param string $post_data
	 * @return string
	 */
	private function _post($url, $post_data) {
		$this->_resetPostData ();
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $post_data );
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_WRITEFUNCTION, array (&$this, "_writeResponseData" ) );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_exec ( $curl );
		$code = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		if (! $code) {
			throw new tx_rpx_Core_Exception ( sprintf ( "Error performing HTTP request: %s", curl_error ( $curl ) ) );
		}
		$response_body = $this->response_data;
		$this->_resetPostData ();
		curl_close ( $curl );
		return $response_body;
	}
	/**
	 * @param string $raw
	 * @return DOMDocument
	 * @throws RpxException
	 */
	private function _parse($raw) {
		$doc = new DOMDocument ();
		if (! $doc->loadXML ( $raw )) {
			throw new tx_rpx_Core_Exception ( "Error parsing XML response" );
		}
		return $doc;
	}
}
if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Connector.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/rpx/classes/Core/Connector.php']);
}