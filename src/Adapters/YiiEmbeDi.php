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

namespace Maslosoft\EmbeDi\Adapters;

use Maslosoft\EmbeDi\EmbeDi;

/**
 * YiiEmbeDi
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class YiiEmbeDi extends EmbeDi
{

	public function __construct($instanceId = EmbeDi::DefaultInstanceId, $presetId = null, $config = [])
	{
		parent::__construct($instanceId, $presetId, $config);
		$this->setAdapters([new YiiAdapter]);
	}

	/**
	 * Required by Yii
	 */
	public function init()
	{
		// Could init something here
	}

}
