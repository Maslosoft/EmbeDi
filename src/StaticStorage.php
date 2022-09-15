<?php

/**
 * Embedded Dependency Injection container
 *
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/embedi
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 * @link https://maslosoft.com/embedi/
 */

namespace Maslosoft\EmbeDi;

use ArrayAccess;
use Countable;
use Iterator;
use Maslosoft\EmbeDi\Interfaces\MassAssignedInterface;
use ReflectionObject;
use ReflectionProperty;
use Serializable;

/**
 * Static Storage class
 * This stores variables in static var, depending on owner and instance id
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class StaticStorage implements Countable, Iterator, ArrayAccess, MassAssignedInterface
{

	/**
	 * Namespace, current container class name
	 * @var string
	 */
	private string $ns = '';

	/**
	 * Owner Id
	 * @var string
	 */
	private string $ownerId;

	/**
	 * Instance id
	 * @var string
	 */
	private string $instanceId;

	/**
	 * Key for storage
	 * @var string
	 */
	private string $key = '';

	/**
	 * Stored values
	 * @var mixed[][]
	 */
	public static array $values = [];

	/**
	 *
	 * @param object|string $owner
	 * @param string        $instanceId
	 * @param null          $presetId
	 */
	public function __construct($owner, string $instanceId, $presetId = null)
	{
		$this->ns = get_class($this);
		$this->ownerId = is_object($owner) ? get_class($owner) : $owner;
		$this->instanceId = $instanceId;
		if (!empty($presetId))
		{
			$this->key = $this->instanceId . '.' . $presetId;
		}
		else
		{
			$this->key = $this->instanceId;
		}
		// Gracefully init - this is required for subsequent constructor calls
		if (!array_key_exists($this->ns, self::$values))
		{
			self::$values[$this->ns] = [];
		}
		if (!array_key_exists($this->ownerId, self::$values[$this->ns]))
		{
			self::$values[$this->ns][$this->ownerId] = [];
		}
		if (!array_key_exists($this->key, self::$values[$this->ns][$this->ownerId]))
		{
			self::$values[$this->ns][$this->ownerId][$this->key] = [];
		}
		$this->_propertize();
	}

	public function getAll()
	{
		return self::$values[$this->ns][$this->ownerId][$this->key];
	}

	public function setAll($values)
	{
		return self::$values[$this->ns][$this->ownerId][$this->key] = $values;
	}

	public function removeAll(): void
	{
		self::$values[$this->ns][$this->ownerId][$this->key] = [];
	}

	/**
	 * Destroy all data in all containers
	 */
	public function destroy(): void
	{
		self::$values = [];
	}

	public function &__get($name)
	{
		return self::$values[$this->ns][$this->ownerId][$this->key][$name];
	}

	public function __set($name, $value)
	{
		self::$values[$this->ns][$this->ownerId][$this->key][$name] = $value;
	}

	public function __unset($name)
	{
		unset(self::$values[$this->ns][$this->ownerId][$this->key][$name]);
	}

	public function __isset($name)
	{
		return isset(self::$values[$this->ns][$this->ownerId][$this->key][$name]);
	}

// <editor-fold defaultstate="collapsed" desc="Interfaces implementation">

	public function count($mode = 'COUNT_NORMAL'): int
	{
		return count(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	#[\ReturnTypeWillChange]
	public function current()
	{
		return current(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	#[\ReturnTypeWillChange]
	public function key()
	{
		return key(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function next(): void
	{
		next(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset];
	}

	public function offsetSet($offset, $value): void
	{
		self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset] = $value;
	}

	public function offsetUnset($offset): void
	{
		unset(self::$values[$this->ns][$this->ownerId][$this->instanceId][$offset]);
	}

	public function rewind(): void
	{
		reset(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
	}

//	public function serialize(): string
//	{
//		return serialize(self::$values[$this->ns][$this->ownerId][$this->instanceId]);
//	}

	public function __serialize(): array
	{
		$data = [
			'ns' => $this->ns,
			'ownerId' => $this->ownerId,
			'instanceId' => $this->instanceId,
			'data' => self::$values[$this->ns][$this->ownerId][$this->instanceId]
		];
		return $data;
	}

//	public function unserialize($data)
//	{
//		return self::$values[$this->ns][$this->ownerId][$this->instanceId] = unserialize($data, ['allowed_classes' => true]);
//	}

	public function __unserialize(array $data): void
	{
		$this->ns = $data['ns'];
		$this->ownerId = $data['ownerId'];
		$this->instanceId = $data['instanceId'];
		self::$values[$this->ns][$this->ownerId][$this->instanceId] = $data['data'];
	}

	public function valid(): bool
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
				if (!array_key_exists($name, self::$values[$this->ns][$this->ownerId][$this->key]))
				{
					$this->__set($name, $this->$name);
				}
				unset($this->$name);
			}
		}
	}

}
