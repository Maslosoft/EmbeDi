<?php

/**
 * This software package is licensed under New BSD license.
 *
 * @package maslosoft/embedi
 * @licence New BSD
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 */

namespace Maslosoft\EmbeDi;

use InvalidArgumentException;
use Maslosoft\EmbeDi\Interfaces\IAdapter;
use Maslosoft\EmbeDi\Storage\EmbeDiStore;
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
	 * @var EmbeDiStore
	 */
	private $storage = null;

	/**
	 *
	 * @var IAdapter
	 */
	private $adapters = [];

	/**
	 * Create container with provided id
	 * @param string $instanceId
	 */
	public function __construct($instanceId = EmbeDi::DefaultInstanceId, $config = [])
	{
		$this->_instanceId = $instanceId;
		if ($config)
		{
			$this->apply($config, $this);
		}
		$this->storage = new EmbeDiStore(__CLASS__, 'embedi');
	}
	
	public function __get($name)
	{
		$methodName = sprintf('get%s', ucfirst($name));
		return $this->$methodName();
	}

	public function __set($name, $value)
	{
		$methodName = sprintf('set%s', ucfirst($name));
		return $this->$methodName($value);
	}

	public function getAdapters()
	{
		return $this->storage->adapters;
	}

	public function setAdapters($adapters)
	{
		$instances = [];
		foreach ($adapters as $adapter)
		{
			// Assuming class name
			if (is_string($adapter))
			{
				$instances[] = new $adapter;
				continue;
			}
			// Set directly
			if ($adapter instanceof IAdapter)
			{
				$instances[] = $adapter;
				continue;
			}
			else
			{
				throw new InvalidArgumentException(sprintf('Adapter of `%s->adapters` is of type `%s`, string (class name) or `%s` required', __CLASS__, gettype($adapter) == 'object' ? get_class($adapter) : gettype($adapter), IAdapter::class));
			}
		}
		$this->storage->adapters = $instances;
		return $this;
	}

	public function addAdapter(IAdapter $adapter)
	{
		// Workaround for indirect modification of overloaded property
		$adapters = $this->storage->adapters;
		$adapters[] = $adapter;
		$this->storage->adapters = $adapters;
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
			return;
		}

		// Try to find configuration in adapters
		foreach ($this->storage->adapters as $adapter)
		{
			$config = $adapter->getConfig(get_class($object), $this->_instanceId);
			if ($config)
			{
				$this->apply($config, $object);
				return;
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
		$data = [];
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
