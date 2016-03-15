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

	public function getConfig($class, $instanceId)
	{
		$app = Yii::app();
		if (empty($app))
		{
			return false;
		}
		$config = $app->getComponents(false);
		if (isset($config[$instanceId]))
		{
			if (is_object($config[$instanceId]))
			{
				return (new YiiEmbeDi())->export($config[$instanceId]);
			}
			if (isset($config[$instanceId]['class']) && $config[$instanceId]['class'] == $class)
			{
				return $config[$instanceId];
			}
		}
		return false;
	}

}
