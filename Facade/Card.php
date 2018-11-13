<?php
namespace Dfe\Square\Facade;
/**
 * 2017-10-07
 * «Connect API v2 Reference» → «API Data Types» → «Card»
 * https://docs.connect.squareup.com/api/connect/v2#type-card
 */
final class Card extends \Df\StripeClone\Facade\Card {
	/**
	 * 2017-10-07
	 * @used-by \Df\StripeClone\Facade\Card::create()
	 * @param array(string => string) $p
	 */
	function __construct($p) {$this->_p = $p;}

	/**
	 * 2017-10-07 «The card's brand (such as  VISA). See CardBrand for all possible values.»
	 * https://docs.connect.squareup.com/api/connect/v2#type-cardbrand
	 * 		`AMERICAN_EXPRESS`
	 * 		`CHINA_UNIONPAY`
	 * 		`DISCOVER`
	 * 		`DISCOVER_DINERS`
	 * 		`JCB`
	 * 		`MASTERCARD`
	 * 		`OTHER_BRAND`
	 * 		`SQUARE_GIFT_CARD`
	 * 		`VISA`
	 * Type: string.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::brand()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @return string
	 */
	function brand() {return dftr($this->_p['card_brand'], [
		'AMERICAN_EXPRESS' => 'American Express'
		,'CHINA_UNIONPAY' => 'China UnionPay'
		,'DISCOVER' => 'Discover'
		,'DISCOVER_DINERS' => 'Diners Club'
		,'JCB' => 'JCB'
		,'MASTERCARD' => 'MasterCard'
		,'OTHER_BRAND' => 'Other Brand'
		,'SQUARE_GIFT_CARD' => 'Square Gift Card'
		,'VISA' => 'Visa'
	]);}

	/**
	 * 2017-10-07
	 * Note 1. `address`:
	 * «The card's billing address.
	 * This value is present only if this object represents a customer's card on file.»
	 * Note 2. `country`: «The address's country, in ISO 3166-1-alpha-2 format»
	 * «Connect API v2 Reference» → «API Data Types» → «Address»
	 * https://docs.connect.squareup.com/api/connect/v2#type-address
	 * Note 3. It should be an ISO-2 code or `null`.
	 * Type: string.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::country()
	 * @used-by \Df\StripeClone\CardFormatter::country()
	 * @return string
	 */
	function country() {return dfa_deep($this->_p, 'address/country');}

	/**
	 * 2017-10-07
	 * «The month of the card's expiration date. This value is always between 1 and 12, inclusive.»
	 * Type: integer.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::expMonth()
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return int
	 */
	function expMonth() {return $this->_p['exp_month'];}

	/**
	 * 2017-10-07 «The four-digit year of the card's expiration date»
	 * Type: integer.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::expYear()
	 * @used-by \Df\StripeClone\CardFormatter::exp()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return int
	 */
	function expYear() {return $this->_p['exp_year'];}

	/**
	 * 2017-10-07 «The card's unique ID, if any»
	 * Type: string.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::id()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Facade\Customer::cardIdForJustCreated()
	 * @return string
	 */
	function id() {return $this->_p['id'];}

	/**
	 * 2017-10-07 «The last 4 digits of the card's number»
	 * Type: string.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::last4()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @used-by \Df\StripeClone\CardFormatter::label()
	 * @return string
	 */
	function last4() {return $this->_p['last_4'];}

	/**
	 * 2017-10-07 «The cardholder name.
	 * This value is present only if this object represents a customer's card on file.»
	 * Type: string.
	 * @override
	 * @see \Df\StripeClone\Facade\Card::owner()
	 * @used-by \Df\StripeClone\CardFormatter::ii()
	 * @return string|null
	 */
	function owner() {return dfa($this->_p, 'cardholder_name');}

	/**
	 * 2017-10-07
	 * @var array(string => string)
	 */
	private $_p;
}