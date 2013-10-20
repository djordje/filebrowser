<?php namespace Djordje\Filebrowser\tests\Entity;

use Mockery as m;
use Djordje\Filebrowser\Entity\File;

class FileTest extends \PHPUnit_Framework_TestCase {

	protected $repository;

	public function setUp()
	{
		$this->repository = m::mock('Djordje\Filebrowser\Repositories\RepositoryInterface');
	}

	public function tearDown()
	{
		m::close();
	}

	/**
	 * @expectedException Djordje\Filebrowser\Exceptions\InvalidFileNameOrPath
	 */
	public function testEntityCreationWithoutName()
	{
		new File(array('path' => '/'));
	}

	/**
	 * @expectedException Djordje\Filebrowser\Exceptions\InvalidFileNameOrPath
	 */
	public function testEntityCreationWithoutPath()
	{
		new \Djordje\Filebrowser\Entity\File(array('name' => 'Test.txt'));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testEntityCreationWithInvalidRepository()
	{
		new File(array('path' => '/', 'name' => 'Test.txt'), new \stdClass());
	}

	public function testEntityGetters()
	{
		$file = new File(array(
			'path' => '/home',
			'name' => 'Test.txt',
			'ext'  => 'txt',
			'size' => 27,
			'mode' => '0666',
			'dir'  => false
		));

		$this->assertEquals('/home', $file->getPath());
		$this->assertEquals('Test.txt', $file->getName());
		$this->assertEquals('txt', $file->getExtension());
		$this->assertEquals(27, $file->getSize());
		$this->assertEquals('0666', $file->getMode());
		$this->assertFalse($file->isDir());

		$this->assertEquals('/home/Test.txt', $file);
	}

	/**
	 * @expectedException Djordje\Filebrowser\Exceptions\RepositoryNotDefined
	 */
	public function testRepositoryGetter()
	{
		$file = new \Djordje\Filebrowser\Entity\File(array(
			'path' => '/home',
			'name' => 'Test.txt'
		));

		$file->getRepository();

		$file = new \Djordje\Filebrowser\Entity\File(
			array(
				'path' => '/home',
				'name' => 'Test.txt'
			),
			$this->repository
		);

		$this->assertEquals($this->repository, $file->getRepository());
	}

	public function testIsDirectoryCheckingOnRepository()
	{
		$file = new \Djordje\Filebrowser\Entity\File(
			array(
				'path' => '/home',
				'name' => 'Test.txt'
			),
			$this->repository
		);
		$this->repository->shouldReceive('isDir')->with($file)->once()->andReturn(false);

		$this->assertFalse($file->isDir());

		$file = new \Djordje\Filebrowser\Entity\File(
			array(
				'path' => '/home',
				'name' => 'test'
			),
			$this->repository
		);
		$this->repository->shouldReceive('isDir')->with($file)->once()->andReturn(true);

		$this->assertTrue($file->isDir());
	}

	public function testExtensionGetterIfExtensionNotProvidedToEntity()
	{
		$file = new \Djordje\Filebrowser\Entity\File(array(
			'path' => '/home',
			'name' => 'Test.txt',
			'dir'  => false
		));

		$this->assertEquals('txt', $file->getExtension());
	}

	public function testExtensionGetterIfExtensionAndIsDirNotProvidedToEntity()
	{
		$file = new \Djordje\Filebrowser\Entity\File(
			array(
				'path' => '/home',
				'name' => 'Test.txt'
			),
			$this->repository
		);
		$this->repository->shouldReceive('isDir')->with($file)->once()->andReturn(false);

		$this->assertEquals('txt', $file->getExtension());

		$file = new \Djordje\Filebrowser\Entity\File(
			array(
				'path' => '/home',
				'name' => 'test'
			),
			$this->repository
		);
		$this->repository->shouldReceive('isDir')->with($file)->once()->andReturn(true);

		$this->assertNull($file->getExtension());
	}

	public function testArrayAndJsonConversion()
	{
		$expectedArray = array(
			'name' => 'Test.txt',
			'path' => '/home',
			'ext'  => 'txt',
			'size' => 27,
			'mode' => '0666',
			'dir'  => false
		);
		$expectedJson = json_encode($expectedArray);
		$file = new \Djordje\Filebrowser\Entity\File($expectedArray);

		$this->assertEquals($expectedArray, $file->toArray());
		$this->assertEquals($expectedJson, $file->toJson());
	}
}