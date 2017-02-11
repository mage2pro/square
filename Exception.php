<?php
// 2016-10-06
namespace Dfe\Square;
use SquareConnect\ApiException;
/** @method ApiException prev() */
final class Exception extends \Df\Payment\Exception {
	/**
	 * 2016-10-06
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @param ApiException $prev
	 * @param array(string => mixed) $request [optional]
	 */
	function __construct(ApiException $prev, array $request = []) {
		$this->_request = $request;
		parent::__construct($prev);
	}

	/**
	 * 2016-08-19
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @return string
	 */
	function message() {return df_cc_n(
		'The Square request is failed.'
		,$this->prev()->getMessage()
		,!$this->_request ? null : ['Request:', df_json_encode_pretty($this->_request)]
	);}

	/**
	 * 2016-07-17
	 * @override
	 * @see \Df\Core\Exception::messageC()
	 * @return string
	 */
	function messageC() {return dfp_error_message($this->prev()->getMessage());}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\Exception::__construct()
	 * @used-by \Dfe\Square\Exception::message()
	 * @var array(string => mixed)
	 */
	private $_request;
}