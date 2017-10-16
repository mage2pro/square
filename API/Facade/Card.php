<?php
namespace Dfe\Square\API\Facade;
/**
 * 2017-10-08
 * «Connect API v2 Reference» → «Endpoints» → «Customers» → «CreateCustomerCard»
 * https://docs.connect.squareup.com/api/connect/v2#endpoint-createcustomercard
 */
final class Card extends \Df\API\Facade {
	/**
	 * 2017-10-08
	 * @param string $customerId
	 */
	function __construct($customerId) {$this->_customerId = $customerId;}

	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Facade::prefix()
	 * @used-by \Df\API\Facade::p()
	 * @return string
	 */
	protected function prefix() {return "customers/{$this->_customerId}";}

	/**
	 * 2017-10-08
	 * @used-by __construct()
	 * @used-by prefix()
	 * @var string
	 */
	private $_customerId;
}