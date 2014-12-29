<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi;

use ArrayAccess;
use Countable;
use Iterator;
use ReflectionObject;
use ReflectionProperty;
use Serializable;

/**
 * Static Storage class
 * This stores variables in static var, depending on owner and instance id
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class StaticStorage implements Countable, Iterator, Serializable, ArrayAccess
{

	/**
	 * Namespace, current container class name
	 * @var string
	 */
	private $ns = '';

	/**
	 * Owner Id
	 * @var string
	 */
	private $ownerId = '';

	/**
	 * Instance id
	 * @var string
	 */
	private $instanceId = '';

	/**
	 * Stored values
	 * @var mixed[][]
	 */
	public static $values = [];

	public function __construct($owner, $instanceId)
	{
		
		assert(is_object($owner));
		$this->ns = get_class($this);
		$this->ownerId = get_class($owner);
		$this->instanceId = $instanceId;
		// Gracefully init - this is required for subsequent constructor calls
		if (!array_key_exists($this->ns, self::$values))
		{
			self::$values[$this->ns] = [];
		}
		if (!array_key_exists($this->ownerId, self::$values[$this->ns]))
		{
			self::$values[$this->ns][$this->ownerId] = [];
		}
		if (!array_key_exists($instanceId, self::$values[$this->ns][$this->ownerId]))
		{
			self::$values[$this->ns][$this->ownerId][$instanceId] = [];
		}
		$this->_propertize();
	}

	public function getAll()
	{
		return self::$values[$this->ns][$this->ownerId][$this->instanceId];
	}

	public function removeAll()
	{
		self::$values[$this->ns][$this->ownerId][$this->instanceId] = [];
	}

	/**
	 * Destroy all data in all containers
	 */
	public function destroy()
	{
		self::$values = [];
	}

	public function __get($name)
	{
		return self::$values[$this->ns][$this->ownerId][$this->instanceId][$name];
	}

	public function __set($name, $value)
	{
		self::$values[$this->ns][$this->ownerId][$this->instanceId][$name] = $value;
	}

	public function __unset($name)
	{
		unset(self::$values[$this->ns][$this->ownerId][$this->instanceId][$name]);
	}

	public function __isset($name)
	{
		return isset(self::$values[$this->ns][$this->ownerId][$this->instanceId][$name]);
	}

// <editor-fold defaultstate="collapsed" desc="Interfaces implementation">

	public function count($mode = 'COUNT_NORMAL')
	{
		return count(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function current()
	{
		return current(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function key()
	{
		return key(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function next()
	{
		return next(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function offsetGet($offset)
	{
		return self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset];
	}

	public function offsetSet($offset, $value)
	{
		self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset(self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset]);
	}

	public function rewind()
	{
		reset(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function serialize()
	{
		return serialize(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function unserialize($serialized)
	{
		return self::$values[$this->ns][$this->ownerId][$this->instanceId] = unserialize($serialized);
	}

	public function valid()
	{
		return $this->offsetExists($this->key());
	}

// </editor-fold>

	/**
	 * This unsets class fields and turns them into storage-aware properties
	 * @return void
	 */
	private function _propertize()
	{
		foreach ((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			// http://stackoverflow.com/a/15784768/133408
			if (!$property->isStatic())
			{
				$name = $property->name;
				if(!array_key_exists($name, self::$values[$this->ns][$this->ownerId][$this->instanceId]))
				{
					$this->__set($name, $this->$name);
				}
				unset($this->$name);
			}
		}
	}

}
