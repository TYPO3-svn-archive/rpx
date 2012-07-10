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

class tx_rpx_Transform_RemoteFileTransformator extends tx_rpx_Transform_AbstractTransformator {

	/**
	 * @var array
	 */
	protected $contentTypesToExt = array (
		'image' => array (
			'image/bmp' => 'bmp',
			'image/gif' => 'gif',
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/svg+xml' => 'svg',
			'image/tiff' => 'tiff',
			'image/x-icon' => 'ico'
		)
	);
	
	/**
	 * Transforms the value based on the selected options
	 *
	 * @param mixed $value The value that should be transformed
	 * @return string
	 * @api
	 */
	public function transform($value) {
		if (!isset($this->options['destinationFolder'])) {
			throw new tx_rpx_Transform_TransformException( 'No destination folder parameter specified for the RemoteFile transformator!' );
		}
		if (!isset($this->options['fileType'])) {
			$this->options['fileType'] = 'image';
		}
		$result = '';
		
		if ($value) {
			$tempName = tempnam('typo3temp', 'rpx');
			
			$fp = fopen($tempName, 'w');
 
			$ch = curl_init($value);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

			$data = curl_exec($ch);
			$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			
			curl_close($ch);
			fclose($fp);
			
			if ($this->contentTypesToExt[$this->options['fileType']][$contentType]) {
					/* @var $basicFileFunctions t3lib_basicFileFunctions */
				$basicFileFunctions = t3lib_div::makeInstance('t3lib_basicFileFunctions');

				$filename = $basicFileFunctions->cleanFileName(uniqid('rpx').'.'.$this->contentTypesToExt[$this->options['fileType']][$contentType]);
				$destinationFolder = $basicFileFunctions->cleanDirectoryName(PATH_site . $this->options['destinationFolder']);
				$uniqueFilename = $basicFileFunctions->getUniqueName($filename, $destinationFolder);

				if (rename($tempName, $uniqueFilename)) {
					t3lib_div::fixPermissions($uniqueFilename);
					$result = basename($uniqueFilename);
				}
			} else {
					// didn't find the proper image file type
				@unlink($tempName);
			}
		}

		return $result;
	}
}

?>
