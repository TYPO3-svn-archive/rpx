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
	private $formattedName;
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
	 * @var string
	 */
	private $utcOffset;
	/**
	 * @var string
	 */
	private $photo;
	/**
	 * @var string
	 */
	private $gender;
	/**
	 * @var string
	 */
	private $birthday;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $phoneNumber;
	/**
	 * @var string
	 */
	private $addressFormatted;
	/**
	 * @var string
	 */
	private $streetAddress;
	/**
	 * @var string
	 */
	private $locality;
	/**
	 * @var string
	 */
	private $region;
	/**
	 * @var string
	 */
	private $postalCode;
	/**
	 * @var string
	 */
	private $country;
	/**
	 * @return string
	 */
	public function getLocality() {
		return $this->locality;
	}
	/**
	 * @param string $locality
	 */
	public function setLocality($locality) {
		$this->locality = $locality;
	}

	public function getUtcOffset() {
		return $this->utcOffset;
	}
	/**
	 * @return string
	 */
	public function getPhoto() {
		return $this->photo;
	}

	/**
	 * @return string
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * @return string
	 */
	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	/**
	 * @return string
	 */
	public function getAddressFormatted() {
		return $this->addressFormatted;
	}

	/**
	 * @return string
	 */
	public function getStreetAddress() {
		return $this->streetAddress;
	}

	/**
	 * @return string
	 */
	public function getRegion() {
		return $this->region;
	}

	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postalCode;
	}

	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @param string $utcOffset
	 */
	public function setUtcOffset($utcOffset) {
		$this->utcOffset = $utcOffset;
	}

	/**
	 * @param string $photo
	 */
	public function setPhoto($photo) {
		$this->photo = $photo;
	}

	/**
	 * @param string $gender
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * @param string $birthday
	 */
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
	}

	/**
	 * @param string $url 
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @param string $phoneNumber
	 */
	public function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	/**
	 * @param string $addressFormatted
	 */
	public function setAddressFormatted($addressFormatted) {
		$this->addressFormatted = $addressFormatted;
	}

	/**
	 * @param string $streetAddress
	 */
	public function setStreetAddress($streetAddress) {
		$this->streetAddress = $streetAddress;
	}

	/**
	 * @param string $region
	 */
	public function setRegion($region) {
		$this->region = $region;
	}

	/**
	 * @param string $postalCode
	 */
	public function setPostalCode($postalCode) {
		$this->postalCode = $postalCode;
	}

	/**
	 * @param string $country
	 */
	public function setCountry($country) {
		$this->country = $country;
	}
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
	public function getFormattedName() {
		return $this->formattedName;
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
	 * @param string $formattedName
	 */
	public function setFormattedName($formattedName) {
		$this->formattedName = $formattedName;
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