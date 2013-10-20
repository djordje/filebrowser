<?php namespace Djordje\Filebrowser\Repositories;

use Djordje\Filebrowser\Entity\FileInterface;

abstract class AbstractRepository {

	/**
	 * @var array
	 */
	protected $config = array(
		'location' => '/'
	);

	/**
	 * Merge configuration with current repositories configuration.
	 *
	 * @param array $config
	 * @return $this
	 */
	public function configure(array $config)
	{
		$this->config = $config + $this->config;

		return $this;
	}


	/**
	 * Return complete repository configuration or just desired key.
	 *
	 * @param null $key
	 * @return string|array
	 */
	public function getConfig($key = null)
	{
		if ($key && isset($this->config[$key]))
		{
			return $this->config[$key];
		}
		return $this->config;
	}

	/**
	 * @param \Djordje\Filebrowser\Entity\FileInterface $file
	 * @param string $newName
	 */
	public function rename(FileInterface $file, $newName)
	{
		$destination = $file->getPath().'/'.$newName;

		return $this->move($file, $destination);
	}

	/**
	 * Prepend repository location if input not instance of FileInterface.
	 * FileInterface supports conversion to string that represent absolute path.
	 *
	 * @param \Djordje\Filebrowser\Entity\FileInterface|string $input
	 * @return \Djordje\Filebrowser\Entity\FileInterface|string
	 */
	protected function getLocation($input)
	{
		if ( ! ($input instanceof FileInterface))
		{
			$input = rtrim($this->config['location'], '/').'/'.$input;
		}

		return $input;
	}

}