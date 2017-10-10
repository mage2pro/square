<?php
namespace Dfe\Square;
use Df\Core\Exception as DFE;
use Magento\Sales\Model\Order\Payment\Transaction as T;
// 2016-09-28
/** @method Settings s() */
final class Method extends \Df\StripeClone\Method {
	/**
	 * 2017-10-09
	 * @used-by \Dfe\Square\Facade\Customer::cardAdd()
	 * @return string|null
	 */
	function cardholder() {return $this->iia(self::$II_CARDHOLDER);}

	/**
	 * 2017-10-09
	 * @used-by \Dfe\Square\Facade\Customer::cardAdd()
	 * @return string|null
	 */
	function postalCode() {return $this->iia(self::$II_POSTAL_CODE);}

	/**
	 * 2016-12-22
	 * https://code.dmitry-fedyuk.com/m2e/square/issues/6
	 * https://www.sellercommunity.com/t5/Developers-API/Connect-API-v2-What-are-the-minimum-and-maximum-limits-for/m-p/26939#M346
	 * https://mage2.pro/t/2411
	 * 2017-10-05
	 * Note 1.
	 * What are the minimum and maximum limits for payment amounts in JPY, GBP, and AUD?
	 * https://www.sellercommunity.com/t5/Developer-Discussions/Connect-API-v2-What-are-the-minimum-and-maximum-limits-for/m-p/52016#M891
	 * Note 2.
	 * «Add support for the new Square merchant countries: Japan, Australia, and the United Kingdom»
	 * https://github.com/mage2pro/square/issues/8
	 * @override
	 * @see \Df\Payment\Method::amountLimits()
	 * @used-by isAvailable()
	 * @return array(string => array(int|float))
	 */
	protected function amountLimits() {return [
		'AUD' => [1, 50000], 'CAD' => [1, 50000], 'GBP' => [1, 50000]
		// 2017-10-05 @todo It is certainly wrong!
		// Need to be updated when I will have the right limits from the Square support.
		,'JPY' => [1, 50000]
		,'USD' => [1, 50000]
	];}

	/**
	 * 2016-09-28
	 * @override
	 * @see \Df\StripeClone\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	protected function iiaKeys() {return array_merge(parent::iiaKeys(), [
		self::$II_CARDHOLDER, self::$II_POSTAL_CODE
	]);}

	/**
	 * 2017-10-07
	 * @override
	 * @see \Df\StripeClone\Method::transUrlBase()
	 * @used-by \Df\StripeClone\Method::transUrl()
	 * @param T $t
	 * @return string
	 */
	protected function transUrlBase(T $t) {return '';}

	/**
	 * 2017-10-09
	 * @used-by cardholder()
	 * @used-by iiaKeys()
	 */
	private static $II_CARDHOLDER = 'cardholder';

	/**
	 * 2017-10-09
	 * @used-by iiaKeys()
	 * @used-by postalCode()
	 */
	private static $II_POSTAL_CODE = 'postalCode';
}