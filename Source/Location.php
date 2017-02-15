<?php
namespace Dfe\Square\Source;
use SquareConnect\Api\LocationApi as API;
use SquareConnect\ApiException;
// 2016-10-06
final class Location extends \Df\Config\Source\Testable {
	/**
	 * 2016-10-06
	 * https://docs.connect.squareup.com/articles/processing-payment-php/#retrievinglocationids
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		/** @var array(string => string) $result */
		$result = [0 => 'Specify an Access Token first, and then save the settings.'];
		/** @var string $token */
		if ($token = $this->ss()->p($this->tkey('AccessToken'))) {
			try {
				$result = df_column((new API)->listLocations($token)->getLocations(), 'getName', 'getId');
			}
			/**
			 * 2016-10-06
			 * Я работал с неактивированной учётной записью Square,
			 * и в промышленном режиме у меня этот запрос вызывал исключительную ситуацию:
			 * [HTTP/1.1 403 Forbidden] {"errors":[{"category":"AUTHENTICATION_ERROR","code":"FORBIDDEN","detail":"You have insufficient permissions to perform that action."}]}
			 */
			catch (ApiException $e) {
				/** @var object $error */
				$error = df_first(dfo($e->getResponseBody(), 'errors'));
				return [dfo($error, 'code') => dfo($error, 'detail')];
			}
		}
		return $result;
	}
}