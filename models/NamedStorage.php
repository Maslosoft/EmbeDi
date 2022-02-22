<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace EmbeDiTest\Models;

use Maslosoft\EmbeDi\StaticStorage;

/**
 * NamedStorage
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class NamedStorage extends StaticStorage
{

	const FooValue = 'This is foo default value';

	public $foo = self::FooValue;

}
