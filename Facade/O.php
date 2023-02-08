<?php
namespace Dfe\Square\Facade;
use Df\API\Operation;
# 2017-10-09
final class O extends \Df\StripeClone\Facade\O {
	/**
	 * 2017-10-09
	 * @override
	 * @see \Df\StripeClone\Facade\O::toArray()
	 * @used-by \Df\StripeClone\Method::transInfo()
	 * @param Operation $o
	 * @return array(string => mixed)
	 */
	function toArray($o):array {return $o->a();}
}