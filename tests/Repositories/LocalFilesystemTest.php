<?php namespace Djordje\Filebrowser\tests\Repositories;

use Djordje\Filebrowser\Repositories\LocalFilesystem;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Tests\FilesystemTestCase;

class LocalFilesystemTest extends FilesystemTestCase {

	protected $repository;

	public function setUp()
	{
		parent::setUp();
		$this->repository = new LocalFilesystem(new Filesystem());
		$this->repository->configure(array(
			'location' => $this->workspace
		));
		mkdir($this->workspace.'/home');
		mkdir($this->workspace.'/home/subdir');
		mkdir($this->workspace.'/empty-dir');
		file_put_contents($this->workspace.'/home/Test.txt', 'Hello World!');
		file_put_contents($this->workspace.'/Test.txt', 'Hello World!');
	}

	public function testIsDir()
	{
		$this->assertFalse($this->repository->isDir('not-created'));
		$this->assertFalse($this->repository->isDir('Test.txt'));
		$this->assertTrue($this->repository->isDir('empty-dir'));
		$home = $this->repository->ls()->first();
		$this->assertTrue($this->repository->isDir($home));
	}

	public function testDirectoryListing()
	{
		$expected = array(
			'home' => array(
				'name' => 'home',
				'path' => $this->workspace,
				'ext'  => null,
				'size' => 0,
				'mode' => substr(sprintf('%o', fileperms($this->workspace.'/home')), -4),
				'dir'  => true
			),
			'empty-dir' => array(
				'name' => 'empty-dir',
				'path' => $this->workspace,
				'ext'  => null,
				'size' => 0,
				'mode' => substr(sprintf('%o', fileperms($this->workspace.'/empty-dir')), -4),
				'dir'  => true
			),
			'Test.txt' => array(
				'name' => 'Test.txt',
				'path' => $this->workspace,
				'ext'  => 'txt',
				'size' => filesize($this->workspace.'/Test.txt'),
				'mode' => substr(sprintf('%o', fileperms($this->workspace.'/Test.txt')), -4),
				'dir'  => false,
			),
		);

		$this->assertEquals($expected, $this->repository->ls()->toArray());

		$this->assertEmpty($this->repository->ls('empty-dir')->toArray());

		$this->assertFalse($this->repository->ls('undefined'));
	}

	public function testDirectoryCreation()
	{
		$this->assertTrue($this->repository->mkdir('new-dir'));
		$this->assertTrue($this->repository->mkdir('new-dir-2/new-subdir'));
	}

	public function testDirectoryAndFileRemoving()
	{
		$this->assertTrue($this->repository->remove('Test.txt'));
		$this->assertTrue($this->repository->remove('empty-dir'));
		$this->assertTrue($this->repository->remove('home'));
	}

	public function testDirectoryAndFileCoping()
	{
		$this->assertTrue($this->repository->copy('Test.txt', 'Test-new.txt'));
		$this->assertTrue($this->repository->copy('empty-dir', 'empty-dir-new'));
		$this->assertTrue($this->repository->copy('home', 'home-new'));
	}

	public function testDirectoryAndFileMoving()
	{
		$this->assertTrue($this->repository->move('Test.txt', 'Test-new.txt'));
		$this->assertTrue($this->repository->move('empty-dir', 'empty-dir-new'));
		$this->assertTrue($this->repository->move('home', 'home-new'));
	}

	public function testFileUpload()
	{
		$file = array(
			'name'     => 'Upload.txt',
			'error'    => UPLOAD_ERR_OK,
			'tmp_name' => $this->workspace.'/uploaded.tmp'
		);

		file_put_contents($file['tmp_name'], 'Content of uploaded file...');
		$this->assertTrue($this->repository->upload($file));

		file_put_contents($file['tmp_name'], 'Content of uploaded file...');
		$this->assertTrue($this->repository->upload($file, 'home'));

		file_put_contents($file['tmp_name'], 'Content of uploaded file...');
		$this->assertFalse($this->repository->upload($file, 'Test.txt'));

		$file['error'] = UPLOAD_ERR_NO_FILE;
		$this->assertFalse($this->repository->upload($file, 'home'));

		unset($file['error']);
		$this->assertFalse($this->repository->upload($file, 'home'));

		$file['error'] = UPLOAD_ERR_OK;
		$file['name'] = 'Test.txt';
		$this->assertFalse($this->repository->upload($file));
	}

}