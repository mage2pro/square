<?php
namespace Dfe\Square\Test\CaseT;
use Dfe\Square\API\Facade\Location as L;
# 2017-10-08
final class Location extends \Dfe\Square\Test\CaseT {
	/** 2017-10-08 @test */
	function t00():void {}

	/** 2017-10-08 [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647 */
	function t01_all():void {
		try {
			print_r((new L)->all()->j());
		}
		catch (\Throwable $t) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $t;
		}
	}

	/** 2017-10-08 */
	function t02_map():void {
		try {
			print_r((new L)->map());
		}
		catch (\Throwable $t) {
			if (function_exists('xdebug_break')) {
				xdebug_break();
			}
			throw $t;
		}
	}
}

