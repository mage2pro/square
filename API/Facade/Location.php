<?php
namespace Dfe\Square\API\Facade;
/**
 * 2017-10-08
 * Note 1. «Connect API v2 Reference» → «Endpoints» → «Locations»
 * https://docs.connect.squareup.com/api/connect/v2#navsection-locations
 * Note 2. «Connect API v2 Reference» → «Data Types» → «Location»
 * https://docs.connect.squareup.com/api/connect/v2#type-location
 * Note 3. [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647
 */
final class Location extends \Df\API\Facade {
	/**
	 * 2017-10-08 E.g.: ['CBASEC-iHv7Pl3-MI-zj3uIgNm8' => 'Coffee & Toffee NYC']
	 * @used-by \Dfe\Square\Source\Location::fetch()
	 * @return array(string => string)
	 */
	function map() {return df_sort(array_column(
		array_filter($this->all()->a(), function(array $i) {return
			/**
			 * 2017-10-08
			 * Note 1.
			 * «Connect API v2 Reference» → «Data Types» → «Location»
			 * «The location's status. See `LocationStatus` for possible values.»
			 * https://docs.connect.squareup.com/api/connect/v2#type-location
			 * Note 2.
			 * «Connect API v2 Reference» → «Enums» → «LocationStatus»
			 * https://docs.connect.squareup.com/api/connect/v2#type-locationstatus
			 *
			 * `ACTIVE`
			 * 		A fully operational location.
			 * 		The location can be used across all Square products and APIs.
			 *
			 * `INACTIVE`
			 * 		A functionally limited location.
			 * 		The location can only be used via Square APIs.
			 *
			 * NOTE:
			 * We strongly discourage the use of inactive locations.
			 * Making API calls with inactive locations will cause complications
			 * if the restrictions on inactive locations increase in the future.
			 */
			'ACTIVE' === $i['status']
		;})
		,'name', 'id'
	));}
}