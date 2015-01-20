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
use Yii;

/**
 * Yii2Adapter
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class Yii2Adapter
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
		/**
		 * TODO Figure out if there is some better way to obtain config
		 */
		if (Yii::$app->has($instanceId))
		{
			return (new EmbeDi())->export(Yii::$app->get($instanceId));
		}
	}

}
