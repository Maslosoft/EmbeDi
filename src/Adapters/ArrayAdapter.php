<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

	public function getConfig($class, $instanceId)
	{
		if (isset($this->config[$instanceId]))
		{
			if (is_object($this->config[$instanceId]))
			{
				return (new YiiEmbeDi())->export($this->config[$instanceId]);
			}
			if (isset($this->config[$instanceId]['class']) && $this->config[$instanceId]['class'] == $class)
			{
				return $this->config[$instanceId];
			}
		}
		return false;
	}

}
