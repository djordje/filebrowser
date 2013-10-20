<?php namespace Djordje\Filebrowser\Entity;

use Djordje\Filebrowser\Entity\FileInterface;
use Djordje\Filebrowser\Repositories\RepositoryInterface;
use Djordje\Filebrowser\Exceptions\RepositoryNotDefined;
use Djordje\Filebrowser\Exceptions\InvalidFileNameOrPath;
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class File implements FileInterface, JsonableInterface, ArrayableInterface {

	/**
	 * File name including extension.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Path to file.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * File extension.
	 *
	 * @var null|string
	 */
	protected $ext = null;

	/**
	 * File size in bytes.
	 *
	 * @var int
	 */
	protected $size = 0;

	/**
	 * Unix file mode.
	 *
	 * @var string
	 */
	protected $mode = '0777';

	/**
	 * Store is entity directory or file.
	 *
	 * @var null|boolean
	 */
	protected $dir = null;

	/**
	 * @var \Djordje\Filebrowser\Repositories\RepositoryInterface
	 */
	protected $repository;

	public function __construct(array $file, RepositoryInterface $repository = null)
	{
		if ( ! isset($file['name']) || ! isset($file['path']))
		{
			throw new InvalidFileNameOrPath;
		}

		$this->name = $file['name'];

		$this->path = rtrim($file['path'], '/');

		switch (true)
		{
			case isset($file['ext']):
				$this->ext = $file['ext'];

			case isset($file['size']):
				$this->size = $file['size'];

			case isset($file['mode']):
				$this->mode = $file['mode'];

			case isset($file['dir']):
				$this->dir = $file['dir'];
		}

		$this->repository = $repository;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getMode()
	{
		return $this->mode;
	}

	/**
	 * Get extension if file is directory, if not defined on entity creation return chars after last dot.
	 *
	 * @return boolean|null
	 */
	public function getExtension()
	{
		$dir = $this->isDir();

		if ( ! $dir && is_null($this->ext))
		{
			$name = explode('.', $this->name);

			$this->ext = end($name);
		}

		return $this->ext;
	}

	/**
	 * Check is file directory, if not defined on entity creation pass question to repository and store result.
	 *
	 * @return boolean|null
	 */
	public function isDir()
	{
		if (is_null($this->dir))
		{
			$this->dir = $this->getRepository()->isDir($this);
		}

		return $this->dir;
	}

	/**
	 * Get instance of repository that returned this file instance.
	 *
	 * @return \Djordje\Filebrowser\Repositories\RepositoryInterface
	 * @throws \Djordje\Filebrowser\Exceptions\RepositoryNotDefined
	 */
	public function getRepository()
	{
		if ($this->repository)
		{
			return $this->repository;
		}
		
		throw new RepositoryNotDefined;
	}

	/**
	 * Convert object to its string representation.
	 * This return absolute path to file, and enable passing file entity instance as source or destination path.
	 *
	 * @return string Absolute file path
	 */
	public function __toString()
	{
		return $this->path .'/'. $this->name;
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'name' => $this->getName(),
			'path' => $this->getPath(),
			'ext'  => $this->getExtension(),
			'size' => $this->getSize(),
			'mode' => $this->getMode(),
			'dir'  => $this->isDir()
		);
	}
}