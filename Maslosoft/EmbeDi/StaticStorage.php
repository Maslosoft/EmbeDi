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
	 * Instance id
	 * @var string
	 */
	private $instanceId = '';

	/**
	 * Owner Id
	 * @var string
	 */
	private $ownerId = '';

	/**
	 * Stored values
	 * @var mixed[][]
	 */
	private static $values = [];

	public function __construct($owner, $instanceId)
	{
		assert(is_object($owner));
		$this->ownerId = get_class($owner);
		$this->instanceId = $instanceId;
		self::$values[$this->ownerId] = [];
	}

	public function removeAll()
	{
		self::$values[$this->ownerId][$this->instanceId] = [];
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
		return self::$values[$this->ownerId][$this->instanceId][$name];
	}

	public function __set($name, $value)
	{
		self::$values[$this->ownerId][$this->instanceId][$name] = $value;
	}

// <editor-fold defaultstate="collapsed" desc="Interfaces implementation">

	public function count($mode = 'COUNT_NORMAL')
	{
		return count(self::$values[$this->ownerId][$this->instanceId]);
	}

	public function current()
	{
		return current(self::$values[$this->ownerId][$this->instanceId]);
	}

	public function key()
	{
		return key(self::$values[$this->ownerId][$this->instanceId]);
	}

	public function next()
	{
		return next(self::$values[$this->ownerId][$this->instanceId]);
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, self::$values[$this->ownerId][$this->instanceId]);
	}

	public function offsetGet($offset)
	{
		return self::$values[$this->ownerId][$this->instanceId][$offset];
	}

	public function offsetSet($offset, $value)
	{
		self::$values[$this->ownerId][$this->instanceId][$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset(self::$values[$this->ownerId][$this->instanceId][$offset]);
	}

	public function rewind()
	{
		rewind(self::$values[$this->ownerId][$this->instanceId][$offset]);
	}

	public function serialize()
	{
		serialize(self::$values[$this->ownerId][$this->instanceId][$offset]);
	}

	public function unserialize($serialized)
	{
		self::$values[$this->ownerId][$this->instanceId][$offset] = unserialize($serialized);
	}

	public function valid()
	{
		return $this->offsetExists($this->key());
	}

// </editor-fold>
}
