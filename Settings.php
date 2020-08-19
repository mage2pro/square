<?php
namespace Dfe\Square;
# 2016-09-28
/** @method static Settings s() */
final class Settings extends \Df\StripeClone\Settings {
	/**
	 * 2016-09-28 «Personal Access Token»
	 * @used-by \Dfe\Square\API\Client::headers()
	 * @return string
	 */
	function accessToken() {return $this->testableP();}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\API\Facade\LocationBased::prefix()
	 * @return string
	 */
	function location() {return $this->testable();}

	/**
	 * 2016-11-12 «Application ID»
	 * @override
	 * @see \Df\API\Settings::publicKey()
	 * @used-by \Df\StripeClone\ConfigProvider::config()
	 * @return string
	 */
	function publicKey() {return $this->testable('applicationID');}
}