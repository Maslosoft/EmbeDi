<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi\Storage;

use Maslosoft\EmbeDi\Interfaces\IAdapter;
use Maslosoft\EmbeDi\StaticStorage;

/**
 * EmbeDiStore
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class EmbeDiStore extends StaticStorage
{

	public $stored = false;

	/**
	 * Adapters
	 * @var IAdapter[]
	 */
	public $adapters = [];
}
