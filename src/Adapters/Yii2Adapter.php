<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi\Adapters;

use Maslosoft\EmbeDi\EmbeDi;
use SebastianBergmann\GlobalState\Exception;
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
		if(Yii::$app->has($instanceId))
		{
			return (new EmbeDi())->export(Yii::$app->get($instanceId));
		}
	}

}
