<?php

namespace StaticStorage;

use Codeception\Test\Unit as Test;
use EmbeDiTest\Models\NamedStorage;
use UnitTester;

class NamedStorageTest extends Test
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 *
	 * @var NamedStorage
	 */
	public $storage = null;

	public $instanceId = 'default';

	// executed before each test
	protected function _before()
	{
		$this->storage = new NamedStorage($this, $this->instanceId);
	}

	// executed after each test
	protected function _after()
	{
		$this->assertTrue($this->storage instanceof NamedStorage);
		$this->storage->destroy();
	}

	// tests
	public function testIfNamedStorageKeepsDefaultValues()
	{
		$this->storage = new NamedStorage($this, $this->instanceId);
		$this->assertSame($this->storage->foo, NamedStorage::FooValue);
	}

}
