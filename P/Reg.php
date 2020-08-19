<?php
namespace Dfe\Square\P;
# 2017-10-10 «Connect API v2 Reference» → «Endpoints» → «Customers» → «CreateCustomer»
# https://docs.connect.squareup.com/api/connect/v2#endpoint-createcustomer
final class Reg extends \Df\StripeClone\P\Reg {
	/**
	 * 2017-10-10
	 * Note 1. The key name of a bank card token.
	 * Note 2. Square supports a card saving, but requires an additional step to do it:
	 * @see \Dfe\Square\Facade\Customer::addCardInASeparateStepForNewCustomers()
	 * @override
	 * @see \Df\StripeClone\P\Reg::k_CardId()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @return string
	 */
	protected function k_CardId() {return null;}

	/**
	 * 2017-10-10
	 * @override
	 * @see \Df\StripeClone\P\Reg::k_Description()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @return string
	 */
	protected function k_Description() {return 'note';}

	/**
	 * 2017-10-10 «The customer's email address»
	 * @override
	 * @see \Df\StripeClone\P\Reg::k_Email()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @return string
	 */
	protected function k_Email() {return 'email_address';}

	/**
	 * 2017-10-10
	 * @override
	 * @see \Df\StripeClone\P\Reg::p()
	 * @used-by \Df\StripeClone\P\Reg::request()
	 * @return array(string => mixed)
	 */
	protected function p() {return [
		# 2017-10-10 «The customer's physical address». Type: Address.
		# https://docs.connect.squareup.com/api/connect/v2#type-address
		'address' => Address::sg()->shipping()
		# 2017-10-10 «The name of the customer's company». Type: string.
		,'company_name' => $this->addressBS()->getCompany()
		# 2017-10-10 «The customer's family (i.e., last) name». Type: string.
		,'family_name' => $this->customerNameL()
		# 2017-10-10 «The customer's given (i.e., first) name». Type: string.
		,'given_name' => $this->customerNameF()
		# 2017-10-10 «A nickname for the customer». Type: string.
		,'nickname' => ''
		# 2017-10-10 «The customer's phone number». Type: STUB.
		,'phone_number' => $this->customerPhone()
	];}
}