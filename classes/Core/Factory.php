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
 * 
 * @package	TYPO3
 * @subpackage	tx_rpx
 */
class tx_rpx_Core_Factory {
	/**
	 * @param DOMDocument $DOMDocument
	 * @return tx_rpx_Core_Profile
	 */
	public function createProfile(DOMDocument $DOMDocument) {
		/* @var $profile tx_rpx_Core_Profile */
		$profile = t3lib_div::makeInstance ( 'tx_rpx_Core_Profile' );
		$simpleXMLElement = simplexml_import_dom ( $DOMDocument );
		if (isset ( $simpleXMLElement->profile )) {
			if (isset ( $simpleXMLElement->profile->displayName )) {
				$profile->setDisplayName ( ( string ) $simpleXMLElement->profile->displayName );
			}
			if (isset ( $simpleXMLElement->profile->email )) {
				$profile->setEmail ( ( string ) $simpleXMLElement->profile->email );
			}
			if (isset ( $simpleXMLElement->profile->identifier )) {
				$profile->setIdentifier ( ( string ) $simpleXMLElement->profile->identifier );
			}
			if (isset ( $simpleXMLElement->profile->name )) {
				if (isset ( $simpleXMLElement->profile->name->givenName )) {
					$profile->setGivenName ( ( string ) $simpleXMLElement->profile->name->givenName );
				}
				if (isset ( $simpleXMLElement->profile->name->familyName )) {
					$profile->setFamilyName ( ( string ) $simpleXMLElement->profile->name->familyName );
				}
				if (isset ( $simpleXMLElement->profile->name->formatted )) {
					$profile->setFormattedName ( ( string ) $simpleXMLElement->profile->name->formatted );
				}
				if (isset ( $simpleXMLElement->profile->name->middleName )) {
					$profile->setMiddleName( ( string ) $simpleXMLElement->profile->name->middleName );
				}
				if (isset ( $simpleXMLElement->profile->name->honorificPrefix )) {
					$profile->setHonorificPrefix( ( string ) $simpleXMLElement->profile->name->honorificPrefix );
				}
				if (isset ( $simpleXMLElement->profile->name->honorificSuffix )) {
					$profile->setHonorificSuffix( ( string ) $simpleXMLElement->profile->name->honorificSuffix );
				}
			}
			if (isset ( $simpleXMLElement->profile->preferredUsername )) {
				$profile->setPreferredUsername ( ( string ) $simpleXMLElement->profile->preferredUsername );
			}
			if (isset ( $simpleXMLElement->profile->providerName )) {
				$profile->setProviderName ( ( string ) $simpleXMLElement->profile->providerName );
			}
			if (isset ( $simpleXMLElement->profile->verifiedEmail )) {
				$profile->setVerifiedEmail ( ( string ) $simpleXMLElement->profile->verifiedEmail );
			}
			if (isset ( $simpleXMLElement->profile->birthday )) {
				$profile->setBirthday ( ( string ) $simpleXMLElement->profile->birthday );
			}
			if (isset ( $simpleXMLElement->profile->utcOffset )) {
				$profile->setUtcOffset ( ( string ) $simpleXMLElement->profile->utcOffset );
			}
			if (isset ( $simpleXMLElement->profile->url )) {
				$profile->setUrl ( ( string ) $simpleXMLElement->profile->url );
			}
			if (isset ( $simpleXMLElement->profile->phoneNumber )) {
				$profile->setPhoneNumber ( ( string ) $simpleXMLElement->profile->phoneNumber );
			}
			if (isset ( $simpleXMLElement->profile->photo )) {
				$profile->setPhoto ( ( string ) $simpleXMLElement->profile->photo );
			}
			if (isset ( $simpleXMLElement->profile->photo )) {
				$profile->setPhoto ( ( string ) $simpleXMLElement->profile->photo );
			}
			if (isset ( $simpleXMLElement->profile->address )) {
				
				if (isset ( $simpleXMLElement->profile->address->formatted )) {
					
					$profile->setAddressFormatted ( ( string ) $simpleXMLElement->profile->address->formatted );
				}
				if (isset ( $simpleXMLElement->profile->address->streetAddress )) {
					$profile->setStreetAddress ( ( string ) $simpleXMLElement->profile->address->streetAddress );
				}
				if (isset ( $simpleXMLElement->profile->address->locality )) {
					$profile->setLocality ( ( string ) $simpleXMLElement->profile->address->locality );
				}
				if (isset ( $simpleXMLElement->profile->address->region )) {
					$profile->setRegion ( ( string ) $simpleXMLElement->profile->address->region );
				}
				if (isset ( $simpleXMLElement->profile->address->postalCode )) {
					$profile->setPostalCode ( ( string ) $simpleXMLElement->profile->address->postalCode );
				}
				if (isset ( $simpleXMLElement->profile->address->country )) {
					$profile->setCountry ( ( string ) $simpleXMLElement->profile->address->country );
				}
			}
		}
		return $profile;
	}
}