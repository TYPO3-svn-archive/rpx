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

class tx_rpx_Transform_RegexTransformator extends tx_rpx_Transform_AbstractTransformator {

	/**
	 * Transforms the value based on the selected options
	 *
	 * @param mixed $value The value that should be transformed
	 * @return string
	 * @api
	 */
	public function transform($value) {
		if (!isset($this->options['pattern'])) {
			throw new tx_rpx_Transform_TransformException( 'No pattern parameter specified for the Regex transformator!' );
		}
		if (!isset($this->options['replacement'])) {
			throw new tx_rpx_Transform_TransformException( 'No replacement parameter specified for the Regex transformator!' );
		}
		if (!isset($this->options['limit'])) {
			$this->options['limit'] = -1;
		}

		return preg_replace($this->options['pattern'], $this->options['replacement'], $value, $this->options['limit']);
	}
}

?>