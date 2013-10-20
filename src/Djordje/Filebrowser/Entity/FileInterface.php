<?php namespace Djordje\Filebrowser\Entity;

interface FileInterface {

	public function getName();

	public function getPath();

	public function getSize();

	public function getMode();

	public function getExtension();

	public function isDir();
	
	public function getRepository();

	public function __toString();

}