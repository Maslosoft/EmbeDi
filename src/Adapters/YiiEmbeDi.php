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

namespace Maslosoft\EmbeDi\Adapters;

use Maslosoft\EmbeDi\EmbeDi;

/**
 * YiiEmbeDi
 *
 * @author Piotr Maselkowski <pmaselkowski at gmail.com>
 */
class YiiEmbeDi extends EmbeDi
{

	public function __construct($instanceId = EmbeDi::DefaultInstanceId, $config = [])
	{
		parent::__construct($instanceId, $config);
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
