<?php namespace Djordje\Filebrowser\Repositories;

use DirectoryIterator;
use Djordje\Filebrowser\Entity\File;
use Djordje\Filebrowser\Repositories\AbstractRepository;
use Djordje\Filebrowser\Repositories\RepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class LocalFilesystem extends \Djordje\Filebrowser\Repositories\AbstractRepository implements RepositoryInterface {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	protected $filesystem;

	public function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	/**
	 * Check if path is directory.
	 * Simple wrapper to PHP's is_dir() with ability to prepend repository location.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function isDir($path)
	{
		return is_dir($this->getLocation($path));
	}

	/**
	 * List directory and return collection of File entities or false if path isn't directory.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string|null $path
	 * @return \Illuminate\Support\Collection|bool
	 */
	public function ls($path = null)
	{
		if ( ! $this->isDir($path))
		{
			return false;
		}

		$directory = new DirectoryIterator($this->getLocation($path));
		$collection = new Collection();

		foreach ($directory as $file)
		{
			if ($file->isDot())
			{
				continue;
			}
			$collection->put($file->getFilename(), new File(array(
				'name' => $file->getFilename(),
				'path' => rtrim($file->getPath()),
				'ext'  => $file->getExtension(),
				'size' => ($file->isDir()) ? 0 : $file->getSize(),
				'mode' => substr(sprintf('%o', $file->getPerms()), -4),
				'dir'  => $file->isDir()
			), $this));
		}

		return $collection;
	}

	/**
	 * Creates directory at given path.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function mkdir($path)
	{
		$this->filesystem->mkdir($this->getLocation($path));

		return true;
	}

	/**
	 * Removes file or directory at given path.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function remove($path)
	{
		$this->filesystem->remove($this->getLocation($path));

		return true;
	}

	/**
	 * Copies file or directory from source to destination.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $source
	 * @param string $destination
	 * @return bool
	 */
	public function copy($source, $destination)
	{
		$sourcePath= $this->getLocation($source);
		$destinationPath = $this->getLocation($destination);

		if ($this->isDir($source))
		{
			$this->filesystem->mirror($sourcePath, $destinationPath);
		}
		else
		{
			$this->filesystem->copy($sourcePath, $destinationPath);
		}

		return true;
	}

	/**
	 * Moves file or directory from source to destination.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $source
	 * @param string $destination
	 * @return bool
	 */
	public function move($source, $destination)
	{
		$source = $this->getLocation($source);
		$destination = $this->getLocation($destination);

		$this->filesystem->rename($source, $destination);

		return true;
	}

	/**
	 * Move correctly uploaded file do desired destination.
	 *
	 * @param array $file
	 * @param string|null $destination
	 * @return bool
	 */
	public function upload(array $file, $destination = null)
	{
		if ( ! $this->isDir($destination))
		{
			return false;
		}
		if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK)
		{
			$destination = $this->getLocation($destination).'/'.$file['name'];

			if ( ! file_exists($destination) && file_exists($file['tmp_name']))
			{
				return @rename($file['tmp_name'], $destination);
			}
		}

		return false;
	}
}