<?php
namespace Dfe\Square\Source;
use Dfe\Square\API\Facade\Location as L;
use Dfe\Square\API\Validator as V;
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
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
	protected function apiKeyName():string {return $this->tkey('AccessToken');}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API\Key::apiKeyTitle()
	 * @used-by \Df\Config\Source\API\Key::requirement()
	 */
	protected function apiKeyTitle():string {return 'an Access Token';}

	/**
	 * 2017-02-15
	 * 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @override
	 * @see \Df\Config\Source\API::exception()
	 * @used-by \Df\Config\Source\API::map()
	 * @param Th|V $t
	 * @return array(string => string)
	 */
	protected function exception(Th $t):array {return ['error' => $t instanceof V ? $t->short() : df_xts($t)];}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source\API::fetch()
	 * @used-by \Df\Config\Source\API::map()
	 * @return array(string => string)
	 */
	protected function fetch():array {return (new L)->map();}
}