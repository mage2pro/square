<?php
namespace Dfe\Square\Test\CaseT;
use Dfe\Square\API\Facade\Location as L;
# 2017-10-08
final class Location extends \Dfe\Square\Test\CaseT {
	/** @test 2017-10-08 */
	function t00() {}

	/**
	 * 2017-10-08
	 * [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647
	 */
	function t01_all() {
		try {
			print_r((new L)->all()->j());
		}
		catch (\Exception $e) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $e;
		}
	}

	/** 2017-10-08 */
	function t02_map() {
		try {
			print_r((new L)->map());
		}
		catch (\Exception $e) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $e;
		}
	}
}

