<?php

namespace EmbeDi;

use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;

class FlyTest extends \Codeception\TestCase\Test
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
	public function testIfWIllApplyConfigurationFromArrayUsingFlyweightInstances()
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

		foreach ([1, 2, 3] as $id)
		{
			$config['scalar'] = $id;
			$comp = EmbeDi::fly()->apply($config);
			/* @var $comp CompoundComponent */
			$this->assertInstanceOf(CompoundComponent::class, $comp);

			$this->assertSame($config['scalar'], $comp->scalar);

			$this->assertInstanceOf(FooSubComponent::class, $comp->foo);
			$this->assertSame($config['foo']['blah'], $comp->foo->blah);

			$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
			$this->assertSame($config['bar']['name'], $comp->bar->name);
		}

		// Configure second instance
		// This instance have different `classField`
		new EmbeDi('second', null, [
			'classField' => '_cls'
		]);

		$config = [
			'scalar' => 'one',
			'foo' =>
			[
				'blah' => 'new',
				'_cls' => 'EmbeDiTest\\Models\\FooSubComponent',
			],
			'bar' =>
			[
				'name' => 'Tequila',
				'_cls' => 'EmbeDiTest\\Models\\BarSubComponent',
			],
			'_cls' => 'EmbeDiTest\\Models\\CompoundComponent',
		];

		foreach ([1, 2, 3] as $id)
		{
			$config['scalar'] = $id;
			$comp = EmbeDi::fly('second')->apply($config);
			/* @var $comp CompoundComponent */
			$this->assertInstanceOf(CompoundComponent::class, $comp);

			$this->assertSame($config['scalar'], $comp->scalar);

			$this->assertInstanceOf(FooSubComponent::class, $comp->foo);
			$this->assertSame($config['foo']['blah'], $comp->foo->blah);

			$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
			$this->assertSame($config['bar']['name'], $comp->bar->name);
		}
	}

}
