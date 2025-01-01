<?php

namespace Source;

use Codeception\Test\Unit;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;

class ApplyTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	public function testIfWIllApplyConfigurationFromArrayWithExternalSource()
	{
		$source = [
			'fooComponent' => [
				'blah' => 'new',
				'class' => 'EmbeDiTest\\Models\\FooSubComponent',
			],
			'barComponent' => [
				'name' => 'Tequila',
				'class' => 'EmbeDiTest\\Models\\BarSubComponent',
			],
		];
		$config = [
			'scalar' => 'one',
			'@foo' => 'fooComponent',
			'@bar' => 'barComponent',
			'class' => 'EmbeDiTest\\Models\\CompoundComponent',
		];

		$di = new EmbeDi();
		$di->addConfig($source);
		$comp = $di->apply($config);
		/* @var $comp CompoundComponent */
		$this->assertInstanceOf(CompoundComponent::class, $comp);

		$this->assertSame($config['scalar'], $comp->scalar);

		$this->assertInstanceOf(FooSubComponent::class, $comp->foo);
		$this->assertSame($source['fooComponent']['blah'], $comp->foo->blah);

		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
		$this->assertSame($source['barComponent']['name'], $comp->bar->name);
	}

}
