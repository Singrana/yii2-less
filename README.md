yii2-less
=========

Yii2 less support

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist singrana/yii2-less "*"
```

or add

```
"singrana/yii2-less": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Add or modify your Yii2 application config


```php
'components'    =>
[
	'assetManager'					=>
	[
		'converter'					=>
		[
			'class'					=>	'singrana\assets\Converter',
		],
	...
	],
	...
];
```

after this, you can usage in you bundles, for example:

```php

	public $css =
	[
		'css/style.less',
	];
```