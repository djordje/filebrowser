## PHP Filebrowser - manipulate your files from PHP

[![Build Status](https://travis-ci.org/djordje/filebrowser.png?branch=master)](https://travis-ci.org/djordje/filebrowser)

Filebrowser is simple interface for manipulating files within your PHP application.

The idea is to enable easy manipulation with familiar syntax:

```php

$repository->ls($path);
$repository->mkdir($path);
$repository->remove($path);
$repository->isDir($path);
$repository->copy($source, $destination);
$repository->move($source, $destination);
$repository->upload($uploadedFile, $destination);

```

##### Installation

Recommanded installation is trough *composer*, add to your `composer.json`:

```json

"require": {
	"djordje/filebrowser": "dev-master"
}

```

#### Currently supported repositories:

* LocalFilesystem

###### Released under MIT licence.