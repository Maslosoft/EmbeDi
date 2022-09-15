<?php

/**
 * Embedded Dependency Injection container
 *
 * This software package is licensed under `AGPL, Commercial` license[s].
 *
 * @package maslosoft/embedi
 * @license AGPL, Commercial
 *
 * @copyright Copyright (c) Peter Maselkowski <pmaselkowski@gmail.com>
 * @link https://maslosoft.com/embedi/
 */

namespace Maslosoft\EmbeDi\Interfaces;

/**
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
interface MassAssignedInterface
{
	public function getAll();

	public function setAll($values);

	public function removeAll();
}
