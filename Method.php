<?php
// 2016-09-28
namespace Dfe\Square;
class Method extends \Df\Payment\Method {
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