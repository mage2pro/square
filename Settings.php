<?php
// 2016-09-28
namespace Dfe\Square;
/** @method static Settings s() */
final class Settings extends \Df\Payment\Settings\BankCard {
	/**
	 * 2016-09-28
	 * @return string
	 */
	public function applicationID() {return $this->testable();}

	/**
	 * 2016-09-28
	 * «Mage2.PRO» → «Payment» → «Square» → «Application ID»
	 * @return string
	 */
	protected function liveApplicationID() {return $this->v();}

	/**
	 * 2016-09-28
	 * «Mage2.PRO» → «Payment» → «Square» → «Personal Access Token»
	 * @return string
	 */
	protected function liveAccessToken() {return $this->p();}

	/**
	 * 2016-09-28
	 * «Mage2.PRO» → «Payment» → «Square» → «Sandbox Application ID»
	 * @return string
	 */
	protected function testApplicationID() {return $this->v();}

	/**
	 * 2016-09-28
	 * «Mage2.PRO» → «Payment» → «Square» → «Sandbox Access Token»
	 * @return string
	 */
	protected function testAccessToken() {return $this->p();}

	/**
	 * 2016-09-28
	 * @return string
	 */
	private function accessToken() {return $this->testable();}
}


