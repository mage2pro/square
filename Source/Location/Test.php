<?php
// 2016-10-06
namespace Dfe\Square\Source\Location;
use Dfe\Square\Settings as S;
class Test extends \Dfe\Square\Source\Location {
	/**
	 * @override
	 * @return string
	 */
	protected function token() {return S::s()->v('testAccessToken');}
}