<?php

namespace Adapters;

use Codeception\TestCase\Test;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\PresetedComponent;
use EmbeDiTest\Models\PresetedComponentFly;
use Maslosoft\EmbeDi\Adapters\ArrayAdapter;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;

class ArrayTest extends Test
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before()
	{
		
	}

	protected function _after()
	{
		if (!file_exists('protected') && is_dir('protected'))
		{
			rmdir('protected');
		}
		(new DiStore($this, 'whatever'))->destroy();
	}

	// tests
	public function testIfWillConfigureFromArray()
	{
		$config = [
			'compound' => [
				'class' => CompoundComponent::class,
				'scalar' => 'fxx',
				'bar' => [
					'class' => BarSubComponent::class,
					'name' => 'Razmatazz',
				]
			]
		];
		// Helper array
		$cfg = $config['compound'];

		EmbeDi::fly()->addAdapter(new ArrayAdapter($config));

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
	}

	// tests
	public function testIfWillConfigureFromArrayWithPreset()
	{
		$config = [
			'preseted' => [
				'one' => [
					'class' => PresetedComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				],
				'two' => [
					'class' => PresetedComponent::class,
					'scalar' => 'ccc',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Cheyenne',
					]
				]
			]
		];
		// Helper arrays
		$cfg1 = $config['preseted']['one'];
		$cfg2 = $config['preseted']['two'];

		EmbeDi::fly()->addAdapter(new ArrayAdapter($config));

		$comp1 = new PresetedComponent('preseted', 'one');
		$this->assertSame($cfg1['scalar'], $comp1->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp1->bar);
		$this->assertSame($cfg1['bar']['name'], $comp1->bar->name);

		$comp2 = new PresetedComponent('preseted', 'two');
		$this->assertSame($cfg2['scalar'], $comp2->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp2->bar);
		$this->assertSame($cfg2['bar']['name'], $comp2->bar->name);
	}

	public function testIfWillConfigureFromArrayWithPresetWithFlyInComponent()
	{
		$config = [
			'preseted' => [
				'one' => [
					'class' => PresetedComponentFly::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				],
				'two' => [
					'class' => PresetedComponentFly::class,
					'scalar' => 'ccc',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Cheyenne',
					]
				]
			]
		];
		// Helper arrays
		$cfg1 = $config['preseted']['one'];
		$cfg2 = $config['preseted']['two'];

		EmbeDi::fly()->addAdapter(new ArrayAdapter($config));

		$comp1 = new PresetedComponentFly('preseted', 'one');
		$this->assertSame($cfg1['scalar'], $comp1->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp1->bar);
		$this->assertSame($cfg1['bar']['name'], $comp1->bar->name);

		$comp2 = new PresetedComponentFly('preseted', 'two');
		$this->assertSame($cfg2['scalar'], $comp2->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp2->bar);
		$this->assertSame($cfg2['bar']['name'], $comp2->bar->name);
	}

}
