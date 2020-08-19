<?php
namespace Dfe\Square\Facade;
use Df\API\Operation;
use Dfe\Square\API\Facade\Transaction as T;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-10-08
 * [Square] An example of a response to `POST /v2/locations/{location_id}/transactions`
 * https://mage2.pro/t/4648
 * @method \Dfe\Square\Method m()
 */
final class Charge extends \Df\StripeClone\Facade\Charge {
	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «CaptureTransaction»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-capturetransaction
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::capturePreauthorized()
	 * @used-by \Df\StripeClone\Method::charge()
	 * @param string $id
	 * @param int|float $a
	 * The $a value is already converted to the PSP currency and formatted according to the PSP requirements.
	 * @return Operation
	 */
	function capturePreauthorized($id, $a) {return (new T)->capture($id);}

	/**
	 * 2017-10-09
	 * [Square] An example of a response to `POST /v2/customers/{customer_id}/cards`: https://mage2.pro/t/4652
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::cardIdPrefix()
	 * @used-by \Df\StripeClone\Payer::tokenIsNew()
	 * @return string
	 */
	function cardIdPrefix() {return 'icard-';}

	/**
	 * 2017-10-08
	 * 2017-10-09
	 * Note 1. [Square] An example of a response to `POST /v2/locations/{location_id}/transactions`
	 * https://mage2.pro/t/4648
	 * Note 2. «Connect API v2 Reference» → «Endpoints» → «Transactions» → «Charge»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-charge
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::create()
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param array(string => mixed) $p
	 * @return Operation
	 */
	function create(array $p) {return (new T)->post($p);}

	/**
	 * 2017-10-08 A result looks like `KnL67ZIwXCPtzOrqj0HrkxMF`.
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::id()
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param Operation $c
	 * @return string
	 */
	function id($c) {return $c['id'];}

	/**
	 * 2017-10-08
	 * Returns the path to the bank card information
	 * in a charge converted to an array by @see \Df\StripeClone\Facade\O::toArray()
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::pathToCard()
	 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
	 * @used-by \Df\StripeClone\Facade\Charge::cardData()
	 * @return string
	 */
	function pathToCard() {return 'tenders/0/card_details/card';}

	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «CreateRefund»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-createrefund
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::refund()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @param string $id
	 * @param float $a
	 * В формате и валюте платёжной системы.
	 * Значение готово для применения в запросе API.
	 * @return null
	 */
	function refund($id, $a) {
		$api = new T; /** @var T $api */
		# 2017-10-09
		# [Square] An example of a response to `GET /v2/locations/{location_id}/transactions/{transaction_id}`
		# https://mage2.pro/t/4654
		$t = $api->get($id); /** @var Operation $t */
		$tender = $t->a('tenders/0'); /** @var array(string => mixed) $tender */
		return $api->refund($id, [
			/**
			 * 2017-10-09
			 * «The amount of money to refund.
			 * Note that you specify the amount in the smallest denomination of the applicable currency.
			 * For example, US dollar amounts are specified in cents.
			 * See "Working with monetary amounts" for details:
			 * https://docs.connect.squareup.com/api/connect/v2#workingwithmonetaryamounts
			 * This amount cannot exceed the amount
			 * that was originally charged to the tender that corresponds to tender_id.»
			 * Type: money.
			 * https://docs.connect.squareup.com/api/connect/v2#type-money
			 */
			'amount_money' => [
				# 2017-10-09
				# «The amount of money, in the smallest denomination of the currency indicated by currency.
				# For example, when currency is `USD`, amount is in cents.»
				# Type: integer.
				'amount' => $a
				/**
				 * 2017-10-09
				 * «The type of currency, in ISO 4217 format.
				 * For example, the currency code for US dollars is `USD`.
				 * See Currency for possible values:
				 * https://docs.connect.squareup.com/api/connect/v2#type-currency »
				 * Type: string.
				 */
				,'currency' => $tender['amount_money']['currency']
			]
			/**
			 * 2017-10-09
			 * «A value you specify that uniquely identifies this refund
			 * among refunds you've created for the tender.
			 * If you're unsure whether a particular refund succeeded,
			 * you can reattempt it with the same idempotency key without worrying about duplicating the refund.
			 * See Idempotency keys for more information:
			 * https://docs.connect.squareup.com/api/connect/v2#idempotencykeys »
			 * Type: string.
			 */
			,'idempotency_key' => uniqid()
			# 2017-10-09 «A description of the reason for the refund. Default value: "Refund via API".»
			# Type: string.
			,'reason' => ''
			# 2017-10-09
			# Note 1.
			# «The ID of the tender to refund.
			# A Transaction has one or more tenders (i.e., methods of payment) associated with it,
			# and you refund each tender separately with the Connect API.»
			# Type: string.
			# Note 2. It is a string like «KnL67ZIwXCPtzOrqj0HrkxMF».
			,'tender_id' => $tender['id']
		]);
	}

	/**
	 * 2017-10-10
	 * Note 1.
	 * The method returns:
	 * 		`true` if $id is an ID of a previously saved bank card.
	 * 		`false` if $id is a new card token.
	 * Note 2.
	 * A card ID looks like «82e66bb3-36ab-51cd-45e7-f9f251c73b08» (36 characters)
	 * A token looks like «CBASENWtEICAkOK1sTnHg48psEAgAQ» (30 characters)
	 * «[Square] An example of a response to `POST /v2/customers/{customer_id}/cards`»: https://mage2.pro/t/4652
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::tokenIsNew()
	 * @used-by \Df\StripeClone\Payer::tokenIsNew()
	 * @param string $id
	 * @return bool
	 */
	function tokenIsNew($id) {return 36 !== strlen($id);}

	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «VoidTransaction»
	 * https://docs.connect.squareup.com/api/connect/v2#endpoint-voidtransaction
	 * @override
	 * @see \Df\StripeClone\Facade\Charge::void()
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @param string $id
	 * @return null
	 */
	function void($id) {return (new T)->void_($id);}
}