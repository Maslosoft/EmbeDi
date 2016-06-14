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

use Exception;
use Maslosoft\EmbeDi\Interfaces\AdapterInterface;
use Yii;

/**
 * YiiAdapter
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class YiiAdapter implements AdapterInterface
{

	public function __construct()
	{
		if (!class_exists('Yii', false))
		{
			throw new Exception(sprintf('Adapter `%s` requires `Yii`', __CLASS__));
		}
	}

	public function getConfig($class, $instanceId, $presetId = null)
	{
		$app = Yii::app();
		if (empty($app))
		{
			return false;
		}
		$config = $app->getComponents(false);
		if (!empty($config[$instanceId]))
		{
			if (!empty($presetId) && empty($config[$instanceId][$presetId]))
			{
				// Preset is provided, but no configuration for preset found, skip
				return false;
			}
			if (!empty($presetId))
			{
				$config = $config[$instanceId][$presetId];
			}
			else
			{
				$config = $config[$instanceId];
			}
			if (is_object($config))
			{
				return (new YiiEmbeDi())->export($config);
			}

			// No class defined, skip
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
