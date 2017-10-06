<?php
namespace Dfe\Square\Source;
use SquareConnect\Api\LocationsApi as API;
use SquareConnect\ApiException;
// 2016-10-06
final class Location extends \Df\Payment\Source\API\Key\Testable {
	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API\Key::apiKeyName()
	 * @used-by \Df\Config\Source\API\Key::apiKey()
	 * @return string
	 */
	protected function apiKeyName() {return $this->tkey('AccessToken');}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API\Key::apiKeyTitle()
	 * @used-by \Df\Config\Source\API\Key::requirement()
	 * @return string
	 */
	protected function apiKeyTitle() {return 'an Access Token';}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API::exception()
	 * @used-by \Df\Config\Source\API::map()
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
	 * @see \Df\Config\Source\API::fetch()
	 * @used-by \Df\Config\Source\API::map()
	 * @return array(string => string)
	 */
	protected function fetch() {
		\SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($this->apiKey());
		return df_column((new API)->listLocations()->getLocations(), 'getName', 'getId');
	}
}