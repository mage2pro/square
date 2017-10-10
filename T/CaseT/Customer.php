<?php
namespace Dfe\Square\T\CaseT;
use Dfe\Square\API\Facade\Customer as C;
// 2017-10-08
final class Customer extends \Dfe\Square\T\CaseT {
	/** @test 2017-10-08 */
	function t00() {}

	/**
	 * 2017-10-08
	 * [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647
	 */
	function t01_all() {
		try {
			print_r((new C)->all()->j());
		}
		catch (\Exception $e) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $e;
		}
	}

	/** @test 2017-10-09 */
	function t02_get() {
		try {
			print_r((new C)->get('111')->j());
		}
		catch (\Exception $e) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $e;
		}
	}
}

