<?php
namespace Dfe\Square;
# 2016-09-28
/** @method static Settings s() */
final class Settings extends \Df\StripeClone\Settings {
	/**
	 * 2016-09-28 «Personal Access Token»
	 * @used-by \Dfe\Square\API\Client::headers()
	 */
	function accessToken():string {return $this->testableP();}

	/**
	 * 2016-10-06
	 * @used-by \Dfe\Square\API\Facade\LocationBased::prefix()
	 */
	function location():string {return $this->testable();}

	/**
	 * 2016-11-12 «Application ID»
	 * @override
	 * @see \Df\API\Settings::publicKey()
	 * @used-by \Df\StripeClone\ConfigProvider::config()
	 */
	function publicKey():string {return $this->testable('applicationID');}
}