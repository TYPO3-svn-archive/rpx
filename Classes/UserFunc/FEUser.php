<?php

function user_rpx_FEUser_isRPXUser() {
	return ($GLOBALS['TSFE']->fe_user->user['uid'] && $GLOBALS['TSFE']->fe_user->user['tx_rpx_identifier']);
}

function user_rpx_FEUser_isRPXProvider($providers) {
	$providersArr = t3lib_div::trimExplode('||', $providers, true);
	
	if ($GLOBALS['TSFE']->fe_user->user['uid'] && $GLOBALS['TSFE']->fe_user->user['tx_rpx_identifier']) {
		if ($GLOBALS['TSFE']->fe_user->user['tx_rpx_provider']) {
			$provider = $GLOBALS['TSFE']->fe_user->user['tx_rpx_provider'];
		} else {
			$host = parse_url($GLOBALS['TSFE']->fe_user->user['tx_rpx_identifier'], PHP_URL_HOST);
			switch ($host) {
				case 'www.facebook.com':
					$provider = 'Facebook';
					break;
				case 'www.google.com':
					$provider = 'Google';
					break;
				case 'twitter.com':
					$provider = 'Twitter';
					break;
				default:
					$provider = 'Other';
					break;
			}
		}
	}

	return in_array($provider, $providersArr);
}