<?php
/**
 * @author: Singrana
 * @email: singrana@singrana.com
 * Date: 27.04.2014
 */
namespace singrana\assets\converter;

use Yii;
use yii\caching\FileCache;
use singrana\assets\Parser;

class Less extends Parser
{
	public $auto = false;

	/**
	 * Parse a Less file to CSS
	 */
	public function parse($src, $dst, $options)
	{
		$this->auto = isset($options['auto']) ? $options['auto'] : $this->auto;

		try
		{
			if ($this->auto)
			{
				/* @var FileCache $cacheMgr */
				$cacheMgr = Yii::createObject('yii\caching\FileCache');
				$cacheMgr->init();
				$cacheId = 'less#' . $dst;
				$cache = $cacheMgr->get($cacheId);
				if ($cache === false || (@filemtime($dst) < @filemtime($src)))
				{
					$cache = $src;
				}
				$less = new \lessc();
				$newCache = $less->cachedCompile($cache);

				if (!is_array($cache) || ($newCache["updated"] > $cache["updated"]))
				{
					$cacheMgr->set($cacheId, $newCache);
					file_put_contents($dst, $newCache['compiled']);
				}
			}
			else
			{
				$less = new \lessc();
				$less->compileFile($src, $dst);
			}
		}
		catch (Exception $e)
		{
			throw new Exception(__CLASS__ . ': Failed to compile less file : ' . $e->getMessage() . '.');
		}
	}
}