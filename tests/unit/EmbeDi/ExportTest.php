<?php

namespace EmbeDi;

use Codeception\TestCase\Test;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;

class ExportTest extends Test
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
	public function testIfWillExportProperly()
	{
		$comp = new CompoundComponent();
		$comp->scalar = 'one';
		$foo = new FooSubComponent();
		$foo->blah = 'new';
		$bar = new BarSubComponent();
		$bar->name = 'Tequila';

		$comp->foo = $foo;
		$comp->bar = $bar;

		$di = new EmbeDi();
		$exported = $di->export($comp);

		$this->assertSame(CompoundComponent::class, $exported[$di->classField]);
		$this->assertSame($comp->scalar, $exported['scalar']);

		$this->assertSame(FooSubComponent::class, $exported['foo'][$di->classField]);
		$this->assertSame($foo->blah, $exported['foo']['blah']);

		$this->assertSame(BarSubComponent::class, $exported['bar'][$di->classField]);
		$this->assertSame($bar->name, $exported['bar']['name']);
	}

}
