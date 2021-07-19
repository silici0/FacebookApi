<?php

namespace silici0\FacebookApi\Facades;

use Illuminate\Support\Facades\Facade;

class FacebookApi extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'facebookapi';
	}
}