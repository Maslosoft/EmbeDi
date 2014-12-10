<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi;

/**
 * EmbeDi
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class EmbeDi
{

	const DefaultInstanceId = 'default';

	private $_storage = null;

	public function __construct($object, $instanceId = EmbeDi::DefaultInstanceId, $fields = [])
	{
		$this->_storage = new StaticStorage($object, $instanceId);
	}

	public function configure($object)
	{
		
	}
}
