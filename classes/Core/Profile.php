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
 * Rpx Profile of a user
 */
class tx_rpx_Core_Profile {
	/**
	 * @var string
	 */
	private $displayName;
	/**
	 * @var string
	 */
	private $email;
	/**
	 * @var string
	 */
	private $identifier;
	/**
	 * @var string
	 */
	private $givenName;
	/**
	 * @var string
	 */
	private $familyName;
	/**
	 * @var string
	 */
	private $formatted;
	/**
	 * @var string
	 */
	private $preferredUsername;
	/**
	 * @var string
	 */
	private $providerName;
	/**
	 * @var string
	 */
	private $verifiedEmail;
	/**
	 * @return string
	 */
	public function getDisplayName() {
		return $this->displayName;
	}
	
	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * @return string
	 */
	public function getGivenName() {
		return $this->givenName;
	}
	
	/**
	 * @return string
	 */
	public function getFamilyName() {
		return $this->familyName;
	}
	
	/**
	 * @return string
	 */
	public function getFormatted() {
		return $this->formatted;
	}
	
	/**
	 * @return string
	 */
	public function getPreferredUsername() {
		return $this->preferredUsername;
	}
	
	/**
	 * @return string
	 */
	public function getProviderName() {
		return $this->providerName;
	}
	
	/**
	 * @return string
	 */
	public function getVerifiedEmail() {
		return $this->verifiedEmail;
	}
	
	/**
	 * @param string $displayName
	 */
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}
	
	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	
	/**
	 * @param string $givenName
	 */
	public function setGivenName($givenName) {
		$this->givenName = $givenName;
	}
	
	/**
	 * @param string $familyName
	 */
	public function setFamilyName($familyName) {
		$this->familyName = $familyName;
	}
	
	/**
	 * @param string $formatted
	 */
	public function setFormatted($formatted) {
		$this->formatted = $formatted;
	}
	
	/**
	 * @param string $preferredUsername
	 */
	public function setPreferredUsername($preferredUsername) {
		$this->preferredUsername = $preferredUsername;
	}
	
	/**
	 * @param string $providerName
	 */
	public function setProviderName($providerName) {
		$this->providerName = $providerName;
	}
	
	/**
	 * @param string $verifiedEmail
	 */
	public function setVerifiedEmail($verifiedEmail) {
		$this->verifiedEmail = $verifiedEmail;
	}
}