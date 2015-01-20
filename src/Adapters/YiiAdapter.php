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

namespace Maslosoft\EmbeDi\Adapters;

use Exception;
use Maslosoft\EmbeDi\EmbeDi;
use Maslosoft\EmbeDi\Interfaces\IAdapter;
use Yii;

/**
 * YiiAdapter
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class YiiAdapter implements IAdapter
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
		$config = Yii::app()->getComponents(false);
		if (isset($config[$instanceId]))
		{
			if(is_object($config[$instanceId]))
			{
				return (new EmbeDi())->export($config[$instanceId]);
			}
			if(isset($config[$instanceId]['class']) && $config[$instanceId]['class'] == $class)
			{
				return $config[$instanceId];
			}
		}
		return false;
	}

}
