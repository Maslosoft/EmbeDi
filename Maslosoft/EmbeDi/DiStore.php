<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Maslosoft\EmbeDi;

/**
 * EmbeDi static storage namespace
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class DiStore extends StaticStorage
{

	/**
	 * Whenever it is stored
	 * @var bool
	 */
	public $stored = false;

	/**
	 * fields values
	 * @var mixedp[]
	 */
	public $data = [];

	/**
	 * Class names for fields, ampty string if field value is scalar
	 * @var string[]
	 */
	public $classes = [];

	/**
	 * Current class name
	 * @var string
	 */
	public $class = '';

}
