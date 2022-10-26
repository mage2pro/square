<?php
namespace Dfe\Square\Source;
use Dfe\Square\API\Facade\Location as L;
use Dfe\Square\API\Validator as V;
/**
 * 2016-10-06
 * 2017-10-07
 * «Processing a card payment (PHP)» → «Retrieving your location IDs»
 * «Every Square merchant's business consists of one or more locations.
 * Every payment a merchant processes is associated with one of these locations (even online payments).
 * In order to process a payment with Connect v2,
 * you need to know which location you want to associate the payment with.»
 * https://docs.connect.squareup.com/articles/processing-payment-php#retrievinglocationids
 */
final class Location extends \Df\Payment\Source\API\Key\Testable {
	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API\Key::apiKeyName()
	 * @used-by \Df\Config\Source\API\Key::isRequirementMet()
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
	 * @param \Exception|V $e
	 * @return array(string => string)
	 */
	protected function exception(\Exception $e):array {return ['error' => $e instanceof V ? $e->short() : df_ets($e)];}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API::fetch()
	 * @used-by \Df\Config\Source\API::map()
	 * @return array(string => string)
	 */
	protected function fetch():array {return (new L)->map();}
}