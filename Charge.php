<?php
// 2016-10-06
namespace Dfe\Square;
use Df\Payment\Token;
final class Charge extends \Df\Payment\Charge {
	/**
	 * 2016-10-06
	 * @used-by p()
	 * https://docs.connect.squareup.com/articles/processing-payment-php/#chargingcardnonce
	 * @return array(string => mixed)
	 */
	private function pCharge() {return [
		'amount_money' => ['amount' => $this->amountF(), 'currency' => $this->currencyC()]
		,'card_nonce' => Token::get($this->ii())
		,'idempotency_key' => uniqid()
	];}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\Method::charge()
	 * @param Method $m
	 * @return array(string => mixed)
	 */
	static function p(Method $m) {return (new self($m))->pCharge();}
}