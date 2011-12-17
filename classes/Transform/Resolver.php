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

require_once t3lib_extMgm::extPath ( 'rpx' ) . 'classes/Transform/TransformatorInterface.php';
require_once t3lib_extMgm::extPath ( 'rpx' ) . 'classes/Transform/AbstractTransformator.php';
require_once t3lib_extMgm::extPath ( 'rpx' ) . 'classes/Transform/RegexTransformator.php';

class tx_rpx_Transform_Resolver implements t3lib_Singleton {
	/**
	 * Match transform names and options
	 * @var string
	 */
	const PATTERN_MATCH_TRANSFORMATORS = '/
			(?:^|,\s*)
			(?P<transformatorName>[a-z0-9_]*)
			\s*
			(?:\(
				(?P<transformOptions>(?:\s*[a-z0-9]+\s*=\s*(?:
					"(?:\\\\"|[^"])*"
					|\'(?:\\\\\'|[^\'])*\'
					|(?:\s|[^,"\']*)
				)(?:\s|,)*)*)
			\))?
		/ixS';

	/**
	 * Match transform options (to parse actual options)
	 * @var string
	 */
	const PATTERN_MATCH_TRANSFORMATOROPTIONS = '/
			\s*
			(?P<optionName>[a-z0-9]+)
			\s*=\s*
			(?P<optionValue>
				"(?:\\\\"|[^"])*"
				|\'(?:\\\\\'|[^\'])*\'
				|(?:\s|[^,"\']*)
			)
		/ixS';	

	/**
	 * Get a transformator for a given data type. Returns a transformator implementing
	 * the tx_rpx_Transform_TransformatorInterface or NULL if no transformator
	 * could be resolved.
	 *
	 * @param string $transformatorName Either one of the built-in data types or fully qualified transformator class name
	 * @param array $transformOptions Options to be passed to the transformator
	 * @return tx_rpx_Transform_TransformatorInterface Transformator or NULL if none found.
	 */
	public function createTransformator($transformatorName, array $transformOptions = array()) {
		$transformatorClassName = $this->resolveTransformatorObjectName($transformatorName);
		if ($transformatorClassName === FALSE) return NULL;
		$transformator = t3lib_div::makeInstance($transformatorClassName, $transformOptions);

		if (!($transformator instanceof tx_rpx_Transform_TransformatorInterface)) {
			return NULL;
		}
		
		return $transformator;
	}

	/**
	 *
	 *
	 * Returns an object of an appropriate transformator for the given class. If no transformator is available
	 * FALSE is returned
	 *
	 * @param string $transformatorName Either the fully qualified class name of the transformator or the short name of a built-in transformator
	 * @return string Name of the transformator object or FALSE
	 */
	protected function resolveTransformatorObjectName($transformatorName) {
		if (strpos($transformatorName, '_') !== FALSE && class_exists($transformatorName)) return $transformatorName;

		$possibleClassName = 'tx_rpx_Transform_' . ucfirst($transformatorName) . 'Transformator';
		if (class_exists($possibleClassName)) return $possibleClassName;

		return FALSE;
	}	
	
	/**
	 * Parses the transform options
	 *
	 * @return array
	 */
	public function parseTransformAnnotation($v) {
		
		$transformConfiguration = array('transformators' => array());
		$values = str_getcsv($v, $delimiter = '|', $enclosure = '"' , $escape = '\\');
		foreach ($values as $value) {
			$matches = array();
			preg_match_all(self::PATTERN_MATCH_TRANSFORMATORS, $value, $matches, PREG_SET_ORDER);
			
			foreach ($matches as $match) {
				if (trim($match['transformatorName'])) {
					$transformOptions = array();
					if (isset($match['transformOptions'])) {
						$transformOptions = $this->parseTransformOptions($match['transformOptions']);
					}
					
					$transformConfiguration['transformators'][] = array('transformatorName' => $match['transformatorName'], 'transformOptions' => $transformOptions);
				}
			}
		}

		return $transformConfiguration;
	}

	/**
	 * Parses $rawTransformOptions not containing quoted option values.
	 * $rawTransformOptions will be an empty string afterwards (pass by ref!).
	 *
	 * @param string &$rawTransformOptions
	 * @return array An array of optionName/optionValue pairs
	 */
	protected function parseTransformOptions($rawTransformOptions) {
		$transformOptions = array();
		$parsedTransformOptions = array();
		preg_match_all(self::PATTERN_MATCH_TRANSFORMATOROPTIONS, $rawTransformOptions, $transformOptions, PREG_SET_ORDER);
		foreach ($transformOptions as $transformOption) {
			$parsedTransformOptions[trim($transformOption['optionName'])] = trim($transformOption['optionValue']);
		}
		array_walk($parsedTransformOptions, array($this, 'unquoteString'));
		return $parsedTransformOptions;
	}

	/**
	 * Removes escapings from a given argument string and trims the outermost
	 * quotes.
	 *
	 * This method is meant as a helper for regular expression results.
	 *
	 * @param string &$quotedValue Value to unquote
	 */
	protected function unquoteString(&$quotedValue) {
		switch ($quotedValue[0]) {
			case '"':
				$quotedValue = str_replace('\"', '"', trim($quotedValue, '"'));
			break;
			case '\'':
				$quotedValue = str_replace('\\\'', '\'', trim($quotedValue, '\''));
			break;
		}
		$quotedValue = str_replace('\\\\', '\\', $quotedValue);
	}		
}