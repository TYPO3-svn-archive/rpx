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

require_once dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'Profile.php';

/**
 * create rpx entities
 */
class tx_rpx_Core_Factory {
	/**
	 * @param DOMDocument $DOMDocument
	 * @return tx_rpx_Core_Profile
	 */
	public function createProfile(DOMDocument $DOMDocument) {
		$profile = t3lib_div::makeInstance('tx_rpx_Core_Profile');
		$simpleXMLElement = simplexml_import_dom ( $DOMDocument );
		if (isset ( $simpleXMLElement->profile )) {
			if (isset ( $simpleXMLElement->profile->displayName )) {
				$profile->setDisplayName ( (string) $simpleXMLElement->profile->displayName );
			}
			if (isset ( $simpleXMLElement->profile->email )) {
				$profile->setEmail ( (string) $simpleXMLElement->profile->email );
			}
			if (isset ( $simpleXMLElement->profile->identifier )) {
				$profile->setIdentifier ( (string) $simpleXMLElement->profile->identifier );
			}
			if (isset ( $simpleXMLElement->profile->name )) {
				if (isset ( $simpleXMLElement->profile->name->givenName )) {
					$profile->setGivenName ( (string) $simpleXMLElement->profile->name->givenName );
				}
				if (isset ( $simpleXMLElement->profile->name->familyName )) {
					$profile->setFamilyName ( (string) $simpleXMLElement->profile->name->familyName );
				}
				if (isset ( $simpleXMLElement->profile->name->formatted )) {
					$profile->setFormatted ( (string) $simpleXMLElement->profile->name->formatted );
				}
			}
			if (isset ( $simpleXMLElement->profile->preferredUsername )) {
				$profile->setPreferredUsername ( (string) $simpleXMLElement->profile->preferredUsername );
			}
			if (isset ( $simpleXMLElement->profile->providerName )) {
				$profile->setProviderName ( (string) $simpleXMLElement->profile->providerName );
			}
			if (isset ( $simpleXMLElement->profile->verifiedEmail )) {
				$profile->setVerifiedEmail ( (string) $simpleXMLElement->profile->verifiedEmail );
			}
		}
		return $profile;
	}
}