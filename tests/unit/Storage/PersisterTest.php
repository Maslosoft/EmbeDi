<?php
namespace Storage;

use Codeception\Test\Unit;
use Maslosoft\EmbeDi\DiStore;
use Maslosoft\EmbeDi\EmbeDi;
use Maslosoft\EmbeDi\StoragePersister;
use UnitTester;


class PersisterTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
		 @mkdir('runtime');
    }

    protected function _after()
    {
    }

    // tests
    public function testIfWillPersistConfiguration()
    {
		 $storage = new DiStore($this, EmbeDi::DefaultInstanceId);

		 $storage->stored = true;
		 
		 $persister = new StoragePersister($storage, 'runtime');
		 $persister->save();
		 $storage->removeAll();

		 $persister->load();

		 $this->assertTrue($storage->stored);
    }

}