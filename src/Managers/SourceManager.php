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

namespace Maslosoft\EmbeDi\Managers;

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
	 * Preset ID
	 * @var string
	 */
	private $_presetId = '';

	/**
	 * Source storage
	 * @var SourceStorage
	 */
	private $storage = null;

	public function __construct($instanceId = EmbeDi::DefaultInstanceId, $presetId = null)
	{
		$this->_instanceId = $instanceId;
		$this->_presetId = $presetId;
		$this->storage = new SourceStorage(__CLASS__, $instanceId, $presetId);
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
					return EmbeDi::fly($this->_instanceId, $this->_presetId)->apply($config);
				}
			}
		}
	}

}
