<?php

use Codeception\Test\Unit as Test;
use EmbeDiTest\Models\SimpleComponent;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;

class ConfigureSimpleComponentTest extends Test
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before()
	{
		(new DiStore($this, 'whatever'))->destroy();
	}

	protected function _after()
	{
		(new DiStore($this, 'whatever'))->destroy();
	}

	// tests

	public function testIfStorageIsSaved()
	{
		$comp = new SimpleComponent();
		$comp->init();
		$store = new DiStore($comp, EmbeDi::DefaultInstanceId);
		$this->assertTrue(isset($store->stored));
		$this->assertTrue($store->stored);
	}

	public function testIfFieldValueIsSaved()
	{
		$comp = new SimpleComponent();
		$comp->name = 'saved';
		$comp->init();
		$store = new DiStore($comp, EmbeDi::DefaultInstanceId);
		$this->assertSame($comp->name, $store->data['name']);
	}

	public function testIfWillConfigureSimpleComponentValue()
	{
		// Configure only one instance
		$comp = new SimpleComponent();
		$comp->name = 'Configured';
		$comp->doNastyThings = true;
		$comp->true = false;
		$comp->init();

		// This instance should have same config
		$comp2 = new SimpleComponent();
		$comp2->init();

		$this->assertSame($comp->name, $comp2->name);
		$this->assertSame($comp->doNastyThings, $comp2->doNastyThings);
		$this->assertSame($comp->true, $comp2->true);
	}

}
