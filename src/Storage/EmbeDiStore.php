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

namespace Maslosoft\EmbeDi\Storage;

use Maslosoft\EmbeDi\Interfaces\AdapterInterface;
use Maslosoft\EmbeDi\StaticStorage;

/**
 * EmbeDiStore
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class EmbeDiStore extends StaticStorage
{

	/**
	 * This is required for adapters
	 */
	const StoreId = 'embedi';

	public $stored = false;

	/**
	 * Adapters
	 * @var AdapterInterface[]
	 */
	public $adapters = [];

}
