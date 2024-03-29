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

namespace Maslosoft\EmbeDi;

use Maslosoft\EmbeDi\Interfaces\MassAssignedInterface;

/**
 * StoragePersister
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class StoragePersister
{

	/**
	 * Mass assigned storage interface
	 * @var MassAssignedInterface
	 */
	private $storage = null;

	/**
	 * Peristence path
	 * @var string
	 */
	private $path = '';

	public function __construct(MassAssignedInterface $storage, $path)
	{
		$this->storage = $storage;
		$this->path = $path;
	}

	/**
	 * Save data to disk
	 */
	public function save()
	{
		$data = $this->storage->getAll();
		$code = sprintf("<?php\n\nreturn %s;", var_export($data, true));
		file_put_contents($this->_getFileName(), $code);
	}

	/**
	 * Read data from disk
	 */
	public function load()
	{
		// file exists not used here as it will exists in next run anyway
		$data = (array)@include $this->_getFileName();
		$this->storage->setAll($data);
	}

	/**
	 * Get storage filename
	 * @return string
	 */
	private function _getFileName()
	{
		return rtrim($this->path, '\\/') . '/' . str_replace('\\', '.', sprintf('%s-%s.php', get_class($this), get_class($this->storage)));
	}

}
