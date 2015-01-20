<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi\Adapters;

use Maslosoft\EmbeDi\EmbeDi;
use Maslosoft\EmbeDi\Interfaces\IAdapter;
use SebastianBergmann\GlobalState\Exception;
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
