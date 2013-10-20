<?php namespace Djordje\Filebrowser\Repositories;

interface RepositoryInterface {

	/**
	 * Check if path is directory.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function isDir($path);

	/**
	 * List directory and return collection of File entities or false if path isn't directory.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string|null $path
	 * @return \Illuminate\Support\Collection|bool
	 */
	public function ls($path = null);

	/**
	 * Creates directory at given path.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function mkdir($path);

	/**
	 * Removes file or directory at given path.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $path
	 * @return bool
	 */
	public function remove($path);

	/**
	 * Copies file or directory from source to destination.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $source
	 * @param string $destination
	 * @return bool
	 */
	public function copy($source, $destination);

	/**
	 * Moves file or directory from source to destination.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $source
	 * @param string $destination
	 * @return bool
	 */
	public function move($source, $destination);

	/**
	 * Move correctly uploaded file do desired destination.
	 *
	 * @param array $file
	 * @param string|null $destination
	 * @return bool
	 */
	public function upload(array $file, $destination = null);

}