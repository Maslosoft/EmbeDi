<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package   maslosoft/embedi
 * @license   AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
 */

namespace Maslosoft\EmbeDi;

use InvalidArgumentException;
use Maslosoft\EmbeDi\Interfaces\AdapterInterface;
use Maslosoft\EmbeDi\Managers\SourceManager;
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

	/**
	 * This is default instance name, and component name.
	 */
	const DefaultInstanceId = 'embedi';

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
	 * Preset ID
	 * @var string
	 */
	private $_presetId = '';

	/**
	 * Storage container
	 * @var EmbeDiStore
	 */
	private $storage = null;

	/**
	 * Configs source manager
	 * @var SourceManager
	 */
	private $sm = null;

	/**
	 * Flyweight instances of EmbeDi
	 * @var EmbeDi[]
	 */
	private static $_instances = [];

	/**
	 * Version holder
	 * @var ?string
	 */
	private static ?string $versionNumber = null;

	/**
	 * Create container with provided id
	 * @param string      $instanceId
	 * @param string|null $presetId If set will lookup configuration in deeper array level
	 * @param array       $config   Configuration of EmbeDi
	 */
	public function __construct(string $instanceId = EmbeDi::DefaultInstanceId, string $presetId = null, array $config = [])
	{
		$this->_instanceId = $instanceId;
		$this->_presetId = $presetId;
		if (!empty($config))
		{
			$this->apply($config, $this);
		}
		$this->storage = new EmbeDiStore(__CLASS__, EmbeDiStore::StoreId);

		/**
		 * TODO Pass $this as second param
		 */
		$this->sm = new SourceManager($instanceId, $presetId);
		if (!empty($presetId))
		{
			$key = $instanceId . '.' . $presetId;
		}
		else
		{
			$key = $instanceId;
		}
		// Assign flyweight instance
		if (empty(self::$_instances[$key]))
		{
			self::$_instances[$key] = $this;
		}
	}

	public function __get($name)
	{
		$methodName = sprintf('get%s', ucfirst($name));
		return $this->{$methodName}();
	}

	public function __set($name, $value)
	{
		$methodName = sprintf('set%s', ucfirst($name));
		return $this->{$methodName}($value);
	}

	/**
	 * Get flyweight instance of embedi.
	 * This will create instance only if `$instanceId` insntace id does not exists.
	 * If named instance exists, or was ever create - existing instance will be used.
	 * Use this function especially when require many `EmbeDi` calls,
	 * for instance when creating `EmbeDi` in loops:
	 * ```php
	 * foreach($configs as $config)
	 * {
	 *        (new EmbeDi)->apply($config);
	 * }
	 * ```
	 * In abowe example at each loop iteration new `EmbeDi` instance is created.
	 * While it is still lightweight, it's unnessesary overhead.
	 *
	 * This can be made in slightly more optimized way by using `fly` function:
	 * ```php
	 * foreach($configs as $config)
	 * {
	 *        EmbeDi::fly()->apply($config);
	 * }
	 * ```
	 * In above example only one instance of `EmbeDi` is used.
	 * @param string $instanceId
	 * @return EmbeDi
	 */
	public static function fly($instanceId = EmbeDi::DefaultInstanceId, $presetId = null)
	{
		if (!empty($presetId))
		{
			$key = $instanceId . '.' . $presetId;
		}
		else
		{
			$key = $instanceId;
		}
		if (empty(self::$_instances[$key]))
		{
			self::$_instances[$key] = new static($instanceId, $presetId);
		}
		return self::$_instances[$key];
	}

	public function getAdapters()
	{
		return $this->storage->adapters;
	}

	/**
	 * TODO Create AdaptersManager
	 */
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
			if ($adapter instanceof AdapterInterface)
			{
				$instances[] = $adapter;
				continue;
			}
			else
			{
				throw new InvalidArgumentException(sprintf('Adapter of `%s->adapters` is of type `%s`, string (class name) or `%s` required', __CLASS__, gettype($adapter) == 'object' ? get_class($adapter) : gettype($adapter), AdapterInterface::class));
			}
		}
		$this->storage->adapters = $instances;
		return $this;
	}

	/**
	 * Add configuration adapter
	 * TODO Create AdaptersManager
	 * @param AdapterInterface $adapter
	 */
	public function addAdapter(AdapterInterface $adapter)
	{
		if (null === $this->storage->adapters)
		{
			$this->storage->adapters = [];
		}
		array_unshift($this->storage->adapters, $adapter);
	}

	/**
	 * Add configuration source for later use
	 * Config should have keys of component id and values of config.
	 * Example:
	 * ```
	 * [
	 *        'logger' => [
	 *            'class' => Monolog\Logger\Logger,
	 *        ],
	 *        'mangan' => [
	 *            '@logger' => 'logger'
	 *        ]
	 * ]
	 * ```
	 * Attributes starting with `@` denotes that link to other
	 * config component should be used. In example above, mangan field `logger`
	 * will be configured with monolog logger.
	 * @param mixed[] $source
	 * @deprecated Use Maslosoft\EmbeDi\Adapters\ArrayAdapter instead
	 */
	public function addConfig($source)
	{
		$this->sm->add($source);
	}

	/**
	 * Check whenever current configuration is stored.
	 * @return bool
	 */
	public function isStored($object)
	{
		return (new DiStore($object, $this->_instanceId, $this->_presetId))->stored;
	}

	/**
	 * Configure existing object from previously stored configuration.
	 * Typically this will will be called in your class constructor.
	 * Will try to find configuration in adapters if it's not stored.
	 * TODO Use SourceManager here, before adapters
	 * TODO Create AdaptersManager and use here
	 * @param object $object
	 * @return object
	 */
	public function configure($object)
	{
		$storage = new DiStore($object, $this->_instanceId, $this->_presetId);

		// Only configure if stored
		if ($this->isStored($object))
		{
			/**
			 * TODO Use apply() here
			 */
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
			$config = $adapter->getConfig(get_class($object), $this->_instanceId, $this->_presetId);
			if ($config)
			{
				$this->apply($config, $object);
				return;
			}
		}
	}

	/**
	 * Apply configuration to object from array.
	 *
	 * This can also create object if passed configuration array have `class` field.
	 *
	 * Example of creating object:
	 * ```
	 * $config = [
	 *        'class' => Vendor\Component::class,
	 *        'title' => 'bar'
	 * ];
	 * (new Embedi)->apply($config);
	 * ```
	 *
	 * Example of applying config to existing object:
	 * ```
	 * $config = [
	 *        'title' => 'bar'
	 * ];
	 * (new Embedi)->apply($config, new Vendor\Component);
	 * ```
	 *
	 * If `$configuration` arguments is string, it will simply instantiate class:
	 * ```
	 * (new Embedi)->apply('Vendor\Package\Component');
	 * ```
	 *
	 * @param string|mixed[][] $configuration
	 * @param object           $object Object to configure, set to null to create new one
	 * @return object
	 */
	public function apply($configuration, $object = null)
	{
		if (is_string($configuration))
		{
			return new $configuration;
		}
		if (null === $object && array_key_exists($this->classField, $configuration))
		{
			$className = $configuration[$this->classField];
			unset($configuration[$this->classField]);
			$object = new $className;
		}
		foreach ($configuration as $name => $value)
		{
			if ($name === $this->classField)
			{
				continue;
			}
			if (strpos($name, '@') === 0)
			{
				$name = substr($name, 1);
				$object->$name = $this->sm->get($value);
				continue;
			}
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
	 * @param object   $object
	 * @param string[] $fields
	 * @return mixed[][]
	 */
	public function export($object, $fields = [])
	{
		$data = [];
		foreach ($this->_getFields($object, $fields) as $name)
		{
			// If object, recurse
			if (!isset($object->$name))
			{
				continue;
			}
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

	/**
	 * Store object configuration.
	 *
	 * This will be typically called in init method of your component.
	 * After storing config, configuration will be available in `configure` method.
	 * `configure` method should be called in your class constructor.
	 *
	 * If you store config and have `configure` method call,
	 * after subsequent creations of your component will be configured by EmbeDi.
	 *
	 * Both methods could be called in constructor, if you don't need additional
	 * initialization code after configuring object.
	 *
	 * Example workflow:
	 * ```
	 * class Component
	 * {
	 *        public $title = '';
	 *
	 *        public function __construct()
	 *        {
	 *            (new EmbeDi)->configure($this);
	 *        }
	 *
	 *        public function init()
	 *        {
	 *            (new EmbeDi)->store($this);
	 *        }
	 * }
	 *
	 * $c1 = new Component();
	 * $c1->title = 'foo';
	 * $c1->init();
	 *
	 * $c2 = new Component();
	 *
	 * echo $c2->title; // 'foo'
	 * ```
	 *
	 * Parameter `$fields` tell's EmbeDi to store only subset of class fields.
	 * Example:
	 * ```
	 * (new EmbeDi)->store($this, ['title']);
	 * ```
	 *
	 * Parameter `$update` tell's EmbeDi to update existing configuration.
	 * By default configuration is not ovveriden on subsequent `store` calls.
	 * This is done on purpose, to not mess basic configuration.
	 *
	 * @param object   $object Object to store
	 * @param string[] $fields Fields to store
	 * @param bool     $update Whenever to update existing configuration
	 * @return mixed[] Stored data
	 */
	public function store($object, $fields = [], $update = false)
	{
		$storage = new DiStore($object, $this->_instanceId);

		// Do not modify stored instance
		if ($this->isStored($object) && !$update)
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

	/**
	 * Get class fields of object. By default, all public and non-static fields are returned.
	 * This can be overridden by passing `$fields` names of fields. These are not checked for existence.
	 * @param object   $object
	 * @param string[] $fields
	 * @return string[]
	 */
	private function _getFields($object, $fields): array
	{
		if (empty($fields))
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

	public function getVersion(): string
	{
		if (null === self::$versionNumber)
		{
			self::$versionNumber = require __DIR__ . '/version.php';
		}
		return self::$versionNumber;
	}

}
