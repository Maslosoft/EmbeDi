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

namespace Maslosoft\EmbeDi\Interfaces;

/**
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
interface AdapterInterface
{

	/**
	 * Get configuration for specified class and instance id
	 * @param string $class
	 * @param string $instanceId
	 * @param string $presetId
	 */
	public function getConfig($class, $instanceId, $presetId = null);
}
