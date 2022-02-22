<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace EmbeDiTest\Models;

use Maslosoft\EmbeDi\EmbeDi;

/**
 * CompoundComnent
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class PresetedComponentFly
{

	public $scalar = '';

	/**
	 *
	 * @var FooSubComponent
	 */
	public $foo = null;

	/**
	 *
	 * @var BarSubComponent
	 */
	public $bar = null;

	/**
	 * EmbeDi instance
	 * @var EmbeDi
	 */
	private $_di = null;

	public function __construct($instanceId = EmbeDi::DefaultInstanceId, $presetId = null)
	{
		$this->_di = EmbeDi::fly($instanceId, $presetId);
		$this->_di->configure($this);
	}

	public function init()
	{
		$this->_di->store($this);
	}

}
