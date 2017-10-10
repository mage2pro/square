<?php
namespace Dfe\Square\API\Facade;
use Dfe\Square\Settings as S;
/**
 * 2017-10-08
 * «Connect API v2 Reference» → «Connect API v2 Conventions» → «Endpoint paths»
 * https://docs.connect.squareup.com/api/connect/v2#endpointpaths
 */
abstract class LocationBased extends \Df\API\Facade {
	/**
	 * 2017-10-08
	 * @override
	 * @see \Df\API\Facade::prefix()
	 * @used-by \Df\API\Facade::p()
	 * @return string
	 */
	protected function prefix() {/** @var S $s */$s = dfps($this); return "locations/{$s->location()}";}
}