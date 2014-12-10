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

	private $_instanceId = '';

	/**
	 * Storage container
	 * @var StaticStorage
	 */
	private $_storage = null;

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
		$this->_storage = new StaticStorage($object, $this->_instanceId);

		// Do not modify stored instance
		if ($this->_storage->stored)
		{
			foreach($this->_storage->data as $name => $value)
			{
				if(array_key_exists('__class__', $value))
				{
					$object->$name = new $value['__class__'];
					unset($value['__class__']);
					$this->configure($object->$name);
				}
				else
				{
					$object->$name = $value;
				}
			}
		}
	}

	public function store($object, $fields = [])
	{
		$this->_storage = new StaticStorage($object, $this->_instanceId);

		// Do not modify stored instance
		if ($this->_storage->stored)
		{
			return $this->_storage;
		}

		$data = [];
		foreach ($this->_getFields($object, $fields) as $name => $value)
		{
			// If object, recurse
			if (is_object($value))
			{
				$data[$name] = $this->store($value);
			}
			else
			{
				$data[$name] = $value;
			}
		}
		$this->_storage->stored = true;
		$this->_storage->data = $data;
		$this->_storage->class = get_class($object);
		return $data;
	}

	private function _getFields($object, $fields)
	{
		if (!$fields)
		{
			foreach ((new ReflectionObject($object))->getProperties(ReflectionProperty::IS_PUBLIC & ReflectionProperty::IS_STATIC) as $property)
			{
				$fields[] = $property->name;
			}
		}
		return $fields;
	}

}
