<?php
namespace Dfe\Square\P;
use Dfe\Square\Method as M;
use Magento\Sales\Model\Order\Address as A;
/**
 * 2017-10-09
 * «Connect API v2 Reference» → «Data Types» → «Address»
 * https://docs.connect.squareup.com/api/connect/v2#type-address
 * @method M m()
 */
final class Address extends \Df\Payment\Operation {
	/**
	 * 2017-10-09
	 * @used-by \Dfe\Square\Facade\Customer::cardAdd()
	 * @used-by \Dfe\Square\P\Charge::p()
	 * @return array(string => mixed)
	 */
	function billing() {return $this->p($this->addressB(), true);}

	/**
	 * 2017-10-09
	 * @used-by \Dfe\Square\P\Charge::p()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @return array(string => mixed)
	 */
	function shipping() {return $this->p($this->addressS());}

	/**
	 * 2017-10-09 «Connect API v2 Reference» → «Data Types» → «Address»
	 * https://docs.connect.squareup.com/api/connect/v2#type-address
	 * @used-by billing()
	 * @used-by shipping()
	 * @param A|null $a
	 * @param bool $isBilling [optional]
	 * @return array(string => mixed)
	 */
	private function p(A $a = null, $isBilling = false) {/** @var A|null $a */ return !$a ? [] : [
		// 2017-10-09
		// «The first line of the address.
		// Fields that start with `address_line` provide the address's most specific details,
		// like street number, street name, and building name.
		// They do not provide less specific details like city, state/province, or country
		// (these details are provided in other fields).»
		// Type: string.
		'address_line_1' => $a->getStreetLine(1)
		// 2017-10-09 «The second line of the address, if any». Type: string.
		,'address_line_2' => $a->getStreetLine(2)
		// 2017-10-09 «The third line of the address, if any». Type: string.
		,'address_line_3' => ''
		// 2017-10-09 «A civil entity within the address's country. In the US, this is the state».
		// Type: string.
		,'administrative_district_level_1' => $a->getRegion()
		// 2017-10-09
		// «A civil entity within the address's administrative_district_level_1.
		// In the US, this is the county»
		// Type: string.
		,'administrative_district_level_2' => ''
		// 2017-10-09 «A civil entity within the address's administrative_district_level_2, if any»
		// Type: string.
		,'administrative_district_level_3' => ''
		// 2017-10-09 «The address's country, in ISO 3166-1-alpha-2 format». Type: string.
		,'country' => $a->getCountryId()
		// 2017-10-09 «Optional first name when it's representing recipient». Type: string.
		,'first_name' => $a->getFirstname()
		// 2017-10-09 «Optional last name when it's representing recipient». Type: string.
		,'last_name' => $a->getLastname()
		// 2017-10-09 «The city or town of the address». Type: string.
		,'locality' => $a->getCity()
		// 2017-10-09  «Optional organization name when it's representing recipient». Type: string.
		,'organization' => $a->getCompany()
		// 2017-10-09 «The address's postal code». Type: string.
		// 2019-09-21 «The postal code in billing address doesn't match the one used for card nonce creation.»
		,'postal_code' => !$isBilling ? $a->getPostcode() : $this->m()->postalCode()
		// 2017-10-09 «A civil region within the address's locality, if any». Type: string.
		,'sublocality' => ''
		// 2017-10-09  «A civil region within the address's sublocality, if any». Type: string.
		,'sublocality_2' => ''
		// 2017-10-09 «A civil region within the address's sublocality_2, if any». Type: string.
		,'sublocality_3' => ''
	];}

	/**
	 * 2017-10-09
	 * @used-by \Dfe\Square\Facade\Customer::cardAdd()
	 * @used-by \Dfe\Square\P\Charge::p()
	 * @used-by \Dfe\Square\P\Reg::p()
	 * @return self
	 */
	static function sg() {static $r; return $r ? $r : $r = new self(dfpm(__CLASS__));}
}