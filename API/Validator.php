<?php
namespace Dfe\Square\API;
/**
 * 2017-10-08
 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Handling errors»
 * https://docs.connect.squareup.com/api/connect/v2#handlingerrors
 * @used-by \Dfe\Square\API\Client::responseValidatorC()
 * @used-by \Dfe\Square\Source\Location::exception()
 */
final class Validator extends \Df\API\Response\Validator {
	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Response\Validator::long()
	 * @used-by \Df\API\Client::_p()
	 */
	function long():string {return df_json_encode($this->errors());}

	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Exception::short()
	 * @used-by \Df\API\Client::_p()
	 * @used-by \Df\API\Exception::message()
	 * @used-by \Dfe\Square\Source\Location::exception()
	 */
	function short():string {return df_cc_br(array_column($this->errors(), 'detail'));}

	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Response\Validator::valid()
	 * @used-by \Df\API\Client::_p()
	 */
	function valid():bool {return !$this->errors();}

	/**
	 * 2017-10-08
	 * All Connect v2 endpoints include an errors array in their response body
	 * if any errors occurred during a request. The response body has the following structure:
	 *	{
	 *		"errors": [
	 *			{
	 *				"category": "AUTHENTICATION_ERROR"
	 *				,"code": "UNAUTHORIZED"
	 *				,"detail": "This request could not be authorized."
	 *			}
	 *		]
	 *	}
	 * Each error in the array has the following fields:
	 * `category`:
	 * 		indicates which high-level category the error falls into.
	 * 		This value never changes for a particular error.
	 * 		See `ErrorCategory` for possible values:
	 * 		https://docs.connect.squareup.com/api/connect/v2#type-errorcategory
	 * `code`:
	 * 		indicates the exact type of error that occurred.
	 * 		This value never changes for a particular error.
	 * 		See `ErrorCode` for possible values:
	 * 		https://docs.connect.squareup.com/api/connect/v2#type-errorcode
	 * `detail`:
	 * 		is a human-readable string that will help you diagnose the error.
	 * 		This value can change for a particular error.
	 * @used-by self::long()
	 * @used-by self::short()
	 * @used-by self::valid()
	 * @return array(array(string => string))|null
	 */
	private function errors():array {return $this->r('errors');}
}