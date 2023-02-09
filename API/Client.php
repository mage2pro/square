<?php
namespace Dfe\Square\API;
use Dfe\Square\Settings as S;
use Zend_Http_Client as C;
# 2017-10-08
final class Client extends \Df\API\Client {
	/**
	 * 2017-10-08
	 * «For `POST` and `PUT` requests, you instead provide parameters as JSON in the body of your request.»
	 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Providing parameters» →
	 * «POST and PUT requests»
	 * https://docs.connect.squareup.com/api/connect/v2#postandputrequests
	 * @override
	 * @see \Df\API\Client::_construct()
	 * @used-by \Df\API\Client::__construct()
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2017-10-08
		 * «For `POST` and `PUT` requests, you instead provide parameters as JSON in the body of your request.»
		 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Providing parameters» →
		 * «POST and PUT requests»
		 * https://docs.connect.squareup.com/api/connect/v2#postandputrequests
		 */
		$this->reqJson();
		/**
		 * 2017-10-08
		 * «By default, all endpoint responses provide data as JSON in the response body
		 * and include a `Content-Type: application/json header`.»
		 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Request and response headers»
		 * https://docs.connect.squareup.com/api/connect/v2#requestandresponseheaders
		 */
		$this->resJson();
		/**
		 * 2017-10-08
		 * A response's root tag is just a syntax sugar.
		 * Look at the `GET /v2/locations` response, for example: https://mage2.pro/t/4647
	 	 *		{"locations": [{<...>}, {<...>}, {<...>}]}
		 */
		$this->resStripRoot();
	}
	
	/**
	 * 2017-10-08
	 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Request and response headers»
	 * «`POST` and `PUT` requests must include one additional header: `Content-Type: application/json`»
	 * https://docs.connect.squareup.com/api/connect/v2#requestandresponseheaders
	 * @override
	 * @see \Df\API\Client::headers()
	 * @used-by \Df\API\Client::__construct()
	 * @used-by \Df\API\Client::p()
	 * @return array(string => string)
	 */
	protected function headers():array {/** @var S $s */$s = dfps($this); return
		['Accept' => 'application/json', 'Authorization' => "Bearer {$s->accessToken()}"]
		+ (!in_array($this->method(), [C::POST, C::PUT]) ? [] : ['Content-Type' => 'application/json'])
	;}

	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Client::responseValidatorC()
	 * @used-by \Df\API\Client::_p()
	 */
	protected function responseValidatorC():string {return \Dfe\Square\API\Validator::class;}

	/**
	 * 2017-10-08 «Connect API v2 Reference» → «Connect API v2 Conventions» → «Endpoint paths»
	 * https://docs.connect.squareup.com/api/connect/v2#endpointpaths
	 * @override
	 * @see \Df\API\Client::urlBase()
	 * @used-by \Df\API\Client::__construct()
	 * @used-by \Df\API\Client::url()
	 */
	protected function urlBase():string {return 'https://connect.squareup.com/v2';}
}