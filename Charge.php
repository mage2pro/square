<?php
// 2016-10-06
namespace Dfe\Square;
use Dfe\Square\Settings as S;
class Charge extends \Df\Payment\Charge\WithToken {
	/**
	 * 2016-10-06
	 * https://docs.connect.squareup.com/articles/processing-payment-php/#chargingcardnonce
	 * @return array(string => mixed)
	 */
	private function _request() {/** @var Settings $s */ $s = S::s(); return [
		'amount_money' => [
			'amount' => $this->amountF()
			,'currency' => $this->currencyC()
		]
		,'card_nonce' => $this->token()
		,'idempotency_key' => uniqid()
	];}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\Method::charge()
	 * @param Method $method
	 * @param string $token
	 * @param float|null $amount [optional]
	 * @return array(string => mixed)
	 */
	public static function request(Method $method, $token, $amount = null) {return
		(new self([
			self::$P__AMOUNT => $amount
			, self::$P__METHOD => $method
			, self::$P__TOKEN => $token
		]))->_request();
	}
}