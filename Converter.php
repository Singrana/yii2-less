<?php
/**
 * @author: Singrana
 * @email: singrana@singrana.com
 * Date: 24.04.2014
 */

namespace singrana\assets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


class Converter extends \yii\web\AssetConverter
{

	protected $defaultParsersOptions =
	[

		'less'				=>
		[
			'class'			=>	'singrana\assets\converter\Less',
			'output'		=>	'css',
			'options'		=>
			[
				'auto'		=>	true
			]
		]
	];

	public $parsers = [];

	public $force = false;

	public function convert($asset, $basePath)
	{
		$pos = strrpos($asset, '.');
		if ($pos === false)
			return parent::convert($asset, $basePath);

		$ext = substr($asset, $pos + 1);

		if (!isset($this->parsers[$ext]))
			return parent::convert($asset, $basePath);


		$parserConfig = ArrayHelper::merge($this->defaultParsersOptions[$ext], $this->parsers[$ext]);
		$resultFile = FileHelper::normalizePath('/' . substr($asset, 0, $pos + 1) . $parserConfig['output']);

		$from = $basePath . '/' . $asset;
		$to = $basePath . $resultFile;

		if (!$this->needRecompile($from, $to))
			return str_replace(Yii::getAlias('@webroot'), '', $to);

		$this->checkDestinationDir($basePath, $resultFile);

		$asConsoleCommand = isset($parserConfig['asConsoleCommand']) && $parserConfig['asConsoleCommand'];
		if ($asConsoleCommand)
		{
			if (isset($this->commands[$ext]))
			{
				list ($distExt, $command) = $this->commands[$ext];
				$this->runCommand($command, $basePath, $asset, $resultFile);
			}
		}
		else
		{
			$parser = new $parserConfig['class']($parserConfig['options']);
			$parserOptions = isset($parserConfig['options']) ? $parserConfig['options'] : array();
			$parser->parse($from, $to, $parserOptions);
		}

		if (YII_DEBUG)
			Yii::info("Converted $asset into $resultFile ", __CLASS__);

		$resultFile=str_replace(Yii::getAlias('@webroot'), '', $to);

		return $resultFile;
	}

	public function needRecompile($from, $to)
	{
		return $this->force || (@filemtime($to) < filemtime($from));
	}

	public function checkDestinationDir($basePath, $file)
	{
		$distDir = dirname($basePath . $file);

		if (!is_dir($distDir))
			mkdir($distDir, 0777, true);
	}
}
