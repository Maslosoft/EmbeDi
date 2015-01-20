<?php

/**
 * This software package is licensed under New BSD license.
 *
 * @package maslosoft/embedi
 * @licence New BSD
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 *
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
