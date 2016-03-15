<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/embedi
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 */

namespace Maslosoft\EmbeDi\Traits;

use Exception;

trait FlyTrait
{

	private static $instances = [];

	public static function fly($instanceId = null)
	{
		if (null === $instanceId)
		{
			if (!defined(sprintf('%s::%s', static::class, 'DefaultInstanceId')))
			{
				throw new Exception(sprintf('Class `%s` must define constant `%s` (%1$s::%2$s)', static::class, 'DefaultInstanceId'));
			}
			$instanceId = static::DefaultInstanceId;
		}
		if (!isset(self::$instances[static::class][$instanceId]))
		{
			self::$instances[static::class][$instanceId] = new static($instanceId);
		}
		return self::$instances[static::class][$instanceId];
	}

}
