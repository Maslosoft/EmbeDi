<?php

namespace EmbeDi;

use Codeception\TestCase\Test;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;

class ApplyTest extends Test
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
	public function testIfWIllApplyConfigurationFromArray()
	{
		$config = [
			'scalar' => 'one',
			'foo' =>
			[
				'blah' => 'new',
				'class' => 'EmbeDiTest\\Models\\FooSubComponent',
			],
			'bar' =>
			[
				'name' => 'Tequila',
				'class' => 'EmbeDiTest\\Models\\BarSubComponent',
			],
			'class' => 'EmbeDiTest\\Models\\CompoundComponent',
		];

		$di = new EmbeDi();
		$comp = $di->apply($config);
		/* @var $comp CompoundComponent */
		$this->assertInstanceOf(CompoundComponent::class, $comp);

		$this->assertSame($config['scalar'], $comp->scalar);

		$this->assertInstanceOf(FooSubComponent::class, $comp->foo);
		$this->assertSame($config['foo']['blah'], $comp->foo->blah);

		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
		$this->assertSame($config['bar']['name'], $comp->bar->name);
	}

}
