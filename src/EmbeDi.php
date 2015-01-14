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
	private $storage = null;

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
	public function isStored($object)
	{
		return (new DiStore($object, $this->_instanceId))->stored;
	}

	public function configure($object)
	{
		$storage = new DiStore($object, $this->_instanceId);

		// Only configure if stored
		if ($this->isStored($object))
		{
			foreach ($storage->data as $name => $value)
			{
				$class = $storage->classes[$name];
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

	/**
	 * Apply configuration to object from array
	 * @param mixed[][] $configuration
	 * @param object $object
	 * @return object
	 */
	public function apply($configuration, $object = null)
	{
		if (null === $object && array_key_exists($this->classField, $configuration))
		{
			$className = $configuration[$this->classField];
			unset($configuration[$this->classField]);
			$object = new $className;
		}
		foreach ($configuration as $name => $value)
		{
			if (is_array($value) && array_key_exists($this->classField, $value))
			{
				$object->$name = $this->apply($value);
			}
			else
			{
				$object->$name = $value;
			}
		}
		return $object;
	}

	/**
	 * Export object configuration to array
	 * @param object $object
	 * @param string[] $fields
	 * @return mixed[][]
	 */
	public function export($object, $fields = [])
	{
		foreach ($this->_getFields($object, $fields) as $name)
		{
			// If object, recurse
			if (is_object($object->$name))
			{
				$data[$name] = $this->export($object->$name);
			}
			else
			{
				$data[$name] = $object->$name;
			}
		}
		$data[$this->classField] = get_class($object);
		return $data;
	}

	public function store($object, $fields = [])
	{
		$storage = new DiStore($object, $this->_instanceId);

		// Do not modify stored instance
		if ($this->isStored($object))
		{
			return $storage;
		}

		$data = [];
		$classes = [];
		foreach ($this->_getFields($object, $fields) as $name)
		{
			// If object, recurse
			if (is_object($object->$name))
			{
				$data[$name] = $this->store($object->$name);
				$classes[$name] = get_class($object->$name);
			}
			else
			{
				$data[$name] = $object->$name;
				$classes[$name] = '';
			}
		}
		$storage->stored = true;
		$storage->data = $data;
		$storage->classes = $classes;
		$storage->class = get_class($object);
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
