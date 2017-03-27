<?php
namespace Dfe\Square\Source;
use SquareConnect\Api\LocationApi as API;
use SquareConnect\ApiException;
// 2016-10-06
final class Location extends \Df\Payment\Source\Testable\Api {
	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Payment\Source\Testable\Api::apiKeyName()
	 * @used-by \Df\Payment\Source\Testable\Api::map()
	 * @return string
	 */
	protected function apiKeyName() {return 'AccessToken';}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Payment\Source\Testable\Api::apiKeyTitle()
	 * @used-by \Df\Payment\Source\Testable\Api::map()
	 * @return string
	 */
	protected function apiKeyTitle() {return 'an Access Token';}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Payment\Source\Testable\Api::exception()
	 * @used-by \Df\Payment\Source\Testable\Api::map()
	 * @param \Exception|ApiException $e
	 * @return array(string => string)
	 */
	protected function exception(\Exception $e) {
		/** @var object $error */
		$error = df_first(dfo($e->getResponseBody(), 'errors'));
		return [dfo($error, 'code') => dfo($error, 'detail')];
	}

	/**
	 * 2017-02-15
	 * https://docs.connect.squareup.com/articles/processing-payment-php/#retrievinglocationids
	 * @override
	 * @see \Df\Payment\Source\Testable\Api::fetch()
	 * @used-by \Df\Payment\Source\Testable\Api::map()
	 * @param string $token
	 * @return array(string => string)
	 */
	protected function fetch($token) {return df_column(
		(new API)->listLocations($token)->getLocations(), 'getName', 'getId'
	);}
}