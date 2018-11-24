<?php
namespace Dfe\Square\P;
// 2017-10-09 «Connect API v2 Reference» → «Endpoints» → «Transactions» → «Charge»
// https://docs.connect.squareup.com/api/connect/v2#endpoint-charge
final class Charge extends \Df\StripeClone\P\Charge {
	/**
	 * 2017-10-09
	 * Note 1. The key name of a bank card token or of a saved bank card ID.
	 * Note 2. `customer_card_id`:
	 * 		«The ID of the customer card on file to charge.
	 * 		Do not provide a value for this field if you provide a value for `card_nonce`.
	 * 		If you provide this value, you must also provide a value for `customer_id`.»
	 * @override
	 * @see \Df\StripeClone\P\Charge::k_CardId()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	function k_CardId() {return 'customer_card_id';}

	/**
	 * 2017-10-09
	 * @override
	 * @see \Df\StripeClone\P\Charge::amountAndCurrency()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return array(string => string|int)
	 */
	protected function amountAndCurrency() {return ['amount_money' => parent::amountAndCurrency()];}

	/**
	 * 2017-10-09
	 * @override
	 * @see \Df\StripeClone\P\Charge::inverseCapture()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return bool
	 */
	protected function inverseCapture() {return true;}

	/**
	 * 2017-10-09
	 * «If true, the request will only perform an Auth on the provided card.
	 * You can then later perform either a Capture (with the CaptureTransaction endpoint)
	 * or a Void (with the VoidTransaction endpoint).
	 * Default value: `false`.»
	 * @override
	 * @see \Df\StripeClone\P\Charge::k_Capture()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	protected function k_Capture() {return 'delay_capture';}

	/**
	 * 2017-10-09
	 * «The ID of the customer to associate this transaction with.
	 * This field is required if you provide a value for `customer_card_id`, and optional otherwise.»
	 * @see \Df\StripeClone\P\Charge::k_CustomerId()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	protected function k_CustomerId() {return 'customer_id';}

	/**
	 * 2018-11-24 https://docs.connect.squareup.com/api/connect/v2#endpoint-charge
	 * @override
	 * @see \Df\StripeClone\P\Charge::k_Description()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	protected function k_Description() {return 'note';}

	/**
	 * 2017-10-09
	 * @override
	 * @see \Df\StripeClone\P\Charge::p()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return array(string => mixed)
	 */
	protected function p() {$a = Address::sg(); /** @var Address $a */ return [
		// 2017-10-09
		// «The buyer's billing address.
		// This value is optional, but this transaction is ineligible for chargeback protection
		// if neither this parameter nor shipping_address is provided.»
		// Type: Address.
		'billing_address' => $a->billing()
		// 2017-10-10
		// «The buyer's email address, if available.
		// This value is optional,
		// but this transaction is ineligible for chargeback protection if it is not provided.»
		,'buyer_email_address' => $this->customerEmail()
		/**
		 * 2017-10-07
		 * «Connect API v2 Reference» → «Idempotency keys»
		 * «Certain Connect API endpoints (currently `Charge` and `CreateRefund`)
		 * require an `idempotency_key` string parameter.
		 * Any time you want to initiate a new card transaction or refund,
		 * you should provide a new, unique value for this parameter.»
		 * https://docs.connect.squareup.com/api/connect/v2#idempotencykeys
		 */
		,'idempotency_key' => uniqid()
		// 2017-10-09
		// «The buyer's shipping address, if available.
		// This value is optional, but this transaction is ineligible for chargeback protection
		// if neither this parameter nor billing_address is provided.»
		// Type: Address.
		,'shipping_address' => $a->shipping()
	];}

	/**
	 * 2017-10-09
	 * @override
	 * @see \Df\StripeClone\P\Charge::k_DSD()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	protected function k_DSD() {return null;}
}