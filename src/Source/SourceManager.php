<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi\Source;

use Maslosoft\EmbeDi\EmbeDi;
use Maslosoft\EmbeDi\Storage\SourceStorage;

/**
 * SourceManager
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class SourceManager
{

	/**
	 * Instance id
	 * @var string
	 */
	private $_instanceId = '';

	/**
	 * Source storage
	 * @var SourceStorage
	 */
	private $storage = null;

	public function __construct($instanceId = EmbeDi::DefaultInstanceId)
	{
		$this->_instanceId = $instanceId;
		$this->storage = new SourceStorage(__CLASS__, $instanceId);
	}

	public function add($source)
	{
		$sources = $this->storage->sources;
		$sources[] = $source;
		$this->storage->sources = $sources;
	}

	public function get($configName)
	{
		foreach ($this->storage->sources as $configs)
		{
			foreach ($configs as $name => $config)
			{
				if ($name === $configName)
				{
					return (new EmbeDi($this->_instanceId))->apply($config);
				}
			}
		}
	}

}
