<?php

/**
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/embedi
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
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
