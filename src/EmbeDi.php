<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi;

use ReflectionObject;
use ReflectionProperty;

/**
 * Embedded dependency injection container
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class EmbeDi
{

	const DefaultInstanceId = 'default';

	/**
	 * Class field in configuration arrays
	 * @see apply()
	 * @see export()
	 * @var string
	 */
	public $classField = 'class';

	/**
	 * Instance id
	 * @var string
	 */
	private $_instanceId = '';

	/**
	 * Storage container
	 * @var DiStore
	 */
	private $_storage = null;

	/**
	 * Create container with provided id
	 * @param string $instanceId
	 */
	public function __construct($instanceId = EmbeDi::DefaultInstanceId)
	{
		$this->_instanceId = $instanceId;
	}

	/**
	 * Whenever current configuration is stored
	 * @return bool
	 */
	public function isStored()
	{
		return $this->_storage->stored;
	}

	public function configure($object)
	{
		$this->_storage = new DiStore($object, $this->_instanceId);

		// Only configure if stored
		if ($this->isStored())
		{
			foreach ($this->_storage->data as $name => $value)
			{
				$class = $this->_storage->classes[$name];
				if ($class)
				{
					$object->$name = new $class;
					$this->configure($object->$name);
				}
				else
				{
					$object->$name = $value;
				}
			}
		}
	}

	public function apply($object, $configuration)
	{
		// TODO Apply configuration to object from array
	}

	public function export()
	{
		// TODO Export current configuration to array
	}

	public function store($object, $fields = [])
	{
		$this->_storage = new DiStore($object, $this->_instanceId);

		// Do not modify stored instance
		if ($this->isStored())
		{
			return $this->_storage;
		}

		$data = [];
		$classes = [];
		foreach ($this->_getFields($object, $fields) as $name)
		{
			// If object, recurse
			if (is_object($object->$name))
			{
				$data[$name] = $this->store($object->$name);
				$classes[$name] = get_class($object);
			}
			else
			{
				$data[$name] = $object->$name;
				$classes[$name] = '';
			}
		}
		$this->_storage->stored = true;
		$this->_storage->data = $data;
		$this->_storage->classes = $classes;
		$this->_storage->class = get_class($object);
		return $data;
	}

	private function _getFields($object, $fields)
	{
		if (!$fields)
		{
			foreach ((new ReflectionObject($object))->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
			{
				// http://stackoverflow.com/a/15784768/133408
				if (!$property->isStatic())
				{
					$fields[] = $property->name;
				}
			}
		}
		return $fields;
	}

}
