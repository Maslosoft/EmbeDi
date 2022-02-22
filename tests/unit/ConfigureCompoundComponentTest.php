<?php

use Codeception\TestCase\Test;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\StaticStorage;

class ConfigureCompoundComponentTest extends Test
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
	public function testSubComponentsAreSaved()
	{
		$comp = new CompoundComponent();
		$foo = new FooSubComponent();
		$foo->blah = 'new';
		$bar = new BarSubComponent();
		$bar->name = 'Tequila';

		$comp->foo = $foo;
		$comp->bar = $bar;

		$comp->init();

		$comp2 = new CompoundComponent();

//		var_dump(StaticStorage::$values);
//		var_dump($comp2);
//		exit;
		$this->assertSame($comp->foo->blah, $comp2->foo->blah);
		$this->assertSame($comp->bar->name, $comp2->bar->name);
	}

}
