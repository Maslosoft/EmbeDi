<?php

namespace Maslosoft\EmbeDi\Traits;

trait FlyTrait
{
	private static $instances = [];
	
	public static function fly($instanceId)
	{
		if(!isset(self::$instances[static::class][$instanceId]))
		{
			self::$instances[static::class][$instanceId] = new static($instanceId);
		}
		return self::$instances[static::class][$instanceId];
	}
}