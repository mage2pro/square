<?php
// 2016-10-06
namespace Dfe\Square;
final class Charge extends \Df\Payment\Charge\WithToken {
	/**
	 * 2016-10-06
	 * @used-by p()
	 * https://docs.connect.squareup.com/articles/processing-payment-php/#chargingcardnonce
	 * @return array(string => mixed)
	 */
	private function pCharge() {return [
		'amount_money' => ['amount' => $this->amountF(), 'currency' => $this->currencyC()]
		,'card_nonce' => $this->token()
		,'idempotency_key' => uniqid()
	];}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\Method::charge()
	 * @param Method $m
	 * @param string $token
	 * @param float|null $amount [optional]
	 * @return array(string => mixed)
	 */
	static function p(Method $m, $token, $amount = null) {return (new self($m, $token, $amount))->pCharge();}
}