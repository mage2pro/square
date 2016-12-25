<?php
// 2016-09-28
namespace Dfe\Square;
/** @method static Settings s() */
final class Settings extends \Df\StripeClone\Settings {
	/**
	 * 2016-09-28
	 * «Personal Access Token»
	 * @return string
	 */
	public function accessToken() {return $this->testableP();}

	/**
	 * 2016-10-06
	 * @return string
	 */
	public function location() {return $this->testable();}

	/**
	 * 2016-11-12
	 * «Mage2.PRO» → «Payment» → «Square» → «Application ID»
	 * @override
	 * @see \Df\StripeClone\Settings::publicKey()
	 * @return string
	 */
	public function publicKey() {return $this->testable('applicationID');}
}


