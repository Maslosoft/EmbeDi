<?php

/**
 * This software package is licensed under New BSD license.
 *
 * @package maslosoft/embedi
 * @licence New BSD
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 */

namespace Maslosoft\EmbeDi\Interfaces;

/**
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
interface IAdapter
{
	/**
	 * Get configuration for specified class and instance id
	 * @param string $class
	 * @param string $instanceId
	 */
	public function getConfig($class, $instanceId);
}
