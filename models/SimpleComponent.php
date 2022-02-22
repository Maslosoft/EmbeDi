<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace EmbeDiTest\Models;

use Maslosoft\EmbeDi\EmbeDi;

/**
 * SimpleComponent
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class SimpleComponent
{

	public $name = 'Simple';
	public $doNastyThings = false;
	public $true = true;

	public static $somethingStatic = 'This should not be configured';

	/**
	 * DI instance
	 * @var EmbeDi
	 */
	protected $_di = null;

	public function __construct($instanceId = EmbeDi::DefaultInstanceId)
	{
		$this->_di = new EmbeDi($instanceId);
		$this->_di->configure($this);
	}

	public function init()
	{
		$this->_di->store($this);
	}

}
