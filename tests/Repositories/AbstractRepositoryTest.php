<?php namespace Djordje\Filebrowser\tests\Repositories;

use Djordje\Filebrowser\Entity\File;

class AbstractRepository extends \Djordje\Filebrowser\Repositories\AbstractRepository
{

	public function move($source, $destination)
	{
		return compact('source', 'destination');
	}

	public function getLocationAccessor($input)
	{
		return $this->getLocation($input);
	}

}

class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase {

	public function testConfigurationSetterAndGetter()
	{
		$repository = new AbstractRepository();

		$config = array(
			'location' => '/'
		);
		$this->assertEquals($config, $repository->getConfig());

		$config = array(
			'location' => '/home',
			'public' => true
		);
		$repository->configure($config);
		$this->assertEquals($config, $repository->getConfig());

		$this->assertEquals(
			array(
				'location' => '/home',
				'public' => false
			),
			$repository->configure(array('public' => false))->getConfig()
		);

		$this->assertEquals('/home', $repository->getConfig('location'));
	}

	public function testGetLocationPath()
	{
		$repository = new AbstractRepository();
		$repository->configure(array(
			'location' => '/home'
		));

		$file = new File(array(
			'path' => '/home',
			'name' => 'Test.txt'
		));
		$expected = '/home/Test.txt';

		$this->assertEquals($expected, $repository->getLocationAccessor('Test.txt'));
		$this->assertEquals($expected, $repository->getLocationAccessor($file));
	}

	public function testRename()
	{
		$repository = new AbstractRepository();
		$file = new File(array(
			'path' => '/home',
			'name' => 'Test.txt'
		));

		$expected = array(
			'source'      => '/home/Test.txt',
			'destination' => '/home/subdir/Newname.txt'
		);

		$this->assertEquals($expected, $repository->rename($file, 'subdir/Newname.txt'));
	}

}