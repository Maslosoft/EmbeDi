<?php

use Codeception\Test\Unit;
use Maslosoft\EmbeDi\StaticStorage;

class StaticStorageTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 *
	 * @var StaticStorage
	 */
	public $storage = null;
	public $instanceId = 'default';

	// executed before each test
	protected function _before()
	{
		$this->storage = new StaticStorage($this, $this->instanceId);
	}

	// executed after each test
	protected function _after()
	{
		$this->assertTrue($this->storage instanceof StaticStorage);
		$this->storage->destroy();
	}

	public function testCanStoreArrayValues()
	{
		$this->storage['test'] = 'foo';
		$this->storage['test2'] = 'bar';
		$this->assertSame($this->storage['test'], 'foo');
		$this->assertSame($this->storage['test2'], 'bar');
	}

	public function testIfCanUnset()
	{
		$this->storage['test'] = 'foo';
		$this->storage->test2 = 'bar';
		
		$this->assertTrue(isset($this->storage['test']));
		unset($this->storage['test']);
		$this->assertFalse(isset($this->storage['test']));

		$this->assertTrue(isset($this->storage->test2));
		unset($this->storage->test2);
		$this->assertFalse(isset($this->storage->test2));
	}

	public function testCanAccessAsFieldValues()
	{
		$this->storage->test3 = 'foo';
		$this->storage->test4 = 'bar';
		$this->assertSame($this->storage->test3, 'foo');
		$this->assertSame($this->storage->test4, 'bar');
		$this->assertSame($this->storage['test3'], 'foo');
		$this->assertSame($this->storage['test4'], 'bar');
	}

	public function testWillRemoveValues()
	{
		$this->storage->removing = 1;
		$this->assertSame($this->storage->removing, 1);
		$this->storage->removeAll();
		$this->assertFalse(isset($this->storage['removing']));
	}

	public function testCanSerialize()
	{
		$data = [
			'foo' => 1,
			'bar' => 'baz'
		];
		$this->storage->foo = $data['foo'];
		$this->storage->bar = $data['bar'];
		$serialized = serialize($this->storage);
		$unserialized = unserialize($serialized);
		$this->assertSame($unserialized['foo'], $data['foo']);
		$this->assertSame($unserialized['bar'], $data['bar']);
		$this->assertTrue($unserialized instanceof StaticStorage);
	}

	public function testCanCastToArray()
	{
		$data = [
			'foo' => 1,
			'bar' => 'baz'
		];
		$this->storage->foo = $data['foo'];
		$this->storage->bar = $data['bar'];
	}

	public function testCanDoForeach()
	{
		$data = [
			'foo' => 1,
			'bar' => 'baz'
		];
		$this->storage->foo = $data['foo'];
		$this->storage->bar = $data['bar'];
		foreach ($this->storage as $key => $value)
		{
			$this->assertSame($value, $data[$key]);
		}
	}

	public function testCanUnset()
	{
		$data = [
			'foo' => 1,
			'bar' => 'baz'
		];
		$this->storage->foo = $data['foo'];
		$this->storage->bar = $data['bar'];
		unset($this->storage['foo']);
		$this->assertFalse(isset($this->storage['foo']));
		$this->assertFalse(isset($this->storage->foo));
		unset($this->storage['bar']);
		$this->assertFalse(isset($this->storage['bar']));
		$this->assertFalse(isset($this->storage->bar));
	}

	public function testCount()
	{
		$data = [
			'foo' => 1,
			'bar' => 'baz'
		];
		$this->storage->foo = $data['foo'];
		$this->storage->bar = $data['bar'];
		$this->assertSame(count($this->storage), 2);
	}
}
