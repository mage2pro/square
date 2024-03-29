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
	 * @override
	 * @see \Df\API\Facade::__construct()
	 * @used-by \Dfe\Square\Facade\Customer::cardAdd()
	 */
	function __construct(string $customerId) {$this->_customerId = $customerId; parent::__construct();}

	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Facade::prefix()
	 * @used-by \Df\API\Facade::path()
	 */
	protected function prefix():string {return "customers/{$this->_customerId}";}

	/**
	 * 2017-10-08
	 * @used-by self::__construct()
	 * @used-by self::prefix()
	 * @var string
	 */
	private $_customerId;
}