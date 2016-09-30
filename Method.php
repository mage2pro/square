<?php
// 2016-09-28
namespace Dfe\Square;
class Method extends \Df\Payment\Method {
	/**
	 * 2016-09-30
	 * @override
	 * @see \Df\Payment\Method::charge()
	 * @param float $amount
	 * @param bool|null $capture [optional]
	 * @return void
	 */
	protected function charge($amount, $capture = true) {}

	/**
	 * 2016-09-28
	 * @override
	 * @see \Df\Payment\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	protected function iiaKeys() {return [self::$TOKEN];}

	/**
	 * 2016-09-28
	 * @var string
	 */
	private static $TOKEN = 'token';
}