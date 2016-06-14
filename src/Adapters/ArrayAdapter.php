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

namespace Maslosoft\EmbeDi\Adapters;

use Maslosoft\EmbeDi\Interfaces\AdapterInterface;

/**
 * ArrayAdapter
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class ArrayAdapter implements AdapterInterface
{

	private $config = [];

	/**
	 * Configuration source for later use
	 * Config should have keys of component id and values of config.
	 * Example:
	 * ```
	 * [
	 * 		'logger' => [
	 * 			'class' => Monolog\Logger\Logger,
	 * 		],
	 * 		'mangan' => [
	 * 			'@logger' => 'logger'
	 * 		]
	 * ]
	 * ```
	 * Attributes starting with `@` denotes that link to other
	 * config component should be used. In example above, mangan field `logger`
	 * will be configured with monolog logger.
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	public function getConfig($class, $instanceId, $presetId = null)
	{
		if (isset($this->config[$instanceId]))
		{
			if (!empty($presetId) && empty($this->config[$instanceId][$presetId]))
			{
				// Preset is provided, but no configuration for preset found, skip
				return false;
			}
			$config = $this->config[$instanceId];

			if (!empty($presetId))
			{
				// Use preset
				$config = $config[$presetId];
			}

			if (is_object($config))
			{
				return (new YiiEmbeDi())->export($config);
			}
			if (empty($config['class']))
			{
				return false;
			}

			// Direct class
			if ($config['class'] == $class)
			{
				return $config;
			}

			// Subclass
			$info = new \ReflectionClass($class);
			if ($info->isSubclassOf($config['class']))
			{
				return $config;
			}

			// Interface
			if ($info->implementsInterface($config['class']))
			{
				return $config;
			}
		}
		return false;
	}

}
