<?php

namespace Adapters;

use Codeception\Test\Unit;
use EmbeDiTest\Models\BarSubComponent;
use EmbeDiTest\Models\CompoundComponent;
use EmbeDiTest\Models\FooSubComponent;
use InvalidArgumentException;
use Maslosoft\EmbeDi\Adapters\YiiAdapter;
use Maslosoft\EmbeDi\Adapters\YiiEmbeDi;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use UnitTester;
use Yii;

class YiiTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before()
	{
		if (!file_exists('protected'))
		{
			mkdir('protected');
		}
		(new DiStore($this, 'whatever'))->destroy();
		// Destroy application if set
		Yii::setApplication(null);
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
	public function testIfWillConfigureFromYii()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		Yii::createConsoleApplication($config);

		$app = Yii::app();

		$fromYii = Yii::app()->compound;
		/* @var $fromYii CompoundComponent */
		$this->assertInstanceOf(CompoundComponent::class, $fromYii);

		$this->assertSame($cfg['scalar'], $fromYii->scalar);

		// This is broken by Yii::createComponent
//		$this->assertInstanceOf(BarSubComponent::class, $fromYii->bar);

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
	}

	public function testIfWillFailOnBogusAdapter()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class,
					'adapters' => [1, 2, 3]
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		try
		{
			Yii::createConsoleApplication($config);
			// Should not get here
			$this->assertFalse(true);
		}
		catch (InvalidArgumentException $ex)
		{
			$this->assertTrue(true);
		}
	}

	public function testIfWillConfigureFromYiiWithOvveridenAdapters()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class,
					'adapters' => [
						YiiAdapter::class
					]
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		Yii::createConsoleApplication($config);


		$this->assertSame(1, count(Yii::app()->embedi->adapters));

		$fromYii = Yii::app()->compound;
		/* @var $fromYii CompoundComponent */
		$this->assertInstanceOf(CompoundComponent::class, $fromYii);

		$this->assertSame($cfg['scalar'], $fromYii->scalar);

		// This is broken by Yii::createComponent
//		$this->assertInstanceOf(BarSubComponent::class, $fromYii->bar);

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
	}

	public function testIfWillConfigureWithDynamicAddedAdapter()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class,
					'adapters' => []
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		Yii::createConsoleApplication($config);

		$this->assertSame(0, count(Yii::app()->embedi->adapters));

		(new EmbeDi)->addAdapter(new YiiAdapter());

		$this->assertSame(1, count(Yii::app()->embedi->adapters));

		$fromYii = Yii::app()->compound;
		/* @var $fromYii CompoundComponent */
		$this->assertInstanceOf(CompoundComponent::class, $fromYii);

		$this->assertSame($cfg['scalar'], $fromYii->scalar);

		// This is broken by Yii::createComponent
//		$this->assertInstanceOf(BarSubComponent::class, $fromYii->bar);

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
	}

	public function testIfWillConfigureFromYiiWithoutUsingAsComponent()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		Yii::createConsoleApplication($config);

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);
	}

	public function testIfWillDealProperlyWithNotConfiguredComponent()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];

		Yii::createConsoleApplication($config);

		$comp = new CompoundComponent('some-for-sure-non-existent-component-instance-id');
		$this->assertSame('', $comp->scalar);
		$this->assertNull($comp->bar);
	}

	public function testIfWillConfigureTwoDifferentInstances()
	{
		$config = [
			'preload' => ['embedi'],
			'components' => [
				'embedi' => [
					'class' => YiiEmbeDi::class
				],
				'compound' => [
					'class' => CompoundComponent::class,
					'scalar' => 'fxx',
					'bar' => [
						'class' => BarSubComponent::class,
						'name' => 'Razmatazz',
					]
				],
				'compoundTwo' => [
					'class' => CompoundComponent::class,
					'scalar' => 'uxxx',
					'bar' => [
						'class' => FooSubComponent::class,
						'name' => 'Zattamar',
					]
				]
			]
		];
		$cfg = $config['components']['compound'];
		$cfg2 = $config['components']['compoundTwo'];

		Yii::createConsoleApplication($config);

		$comp = new CompoundComponent('compound');
		$this->assertSame($cfg['scalar'], $comp->scalar);
		$this->assertInstanceOf(BarSubComponent::class, $comp->bar);

		$comp = new CompoundComponent('compoundTwo');
		$this->assertSame($cfg2['scalar'], $comp->scalar);
		$this->assertInstanceOf(FooSubComponent::class, $comp->bar);
	}

}
