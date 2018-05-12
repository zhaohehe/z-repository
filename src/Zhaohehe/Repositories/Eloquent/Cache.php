<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Cache
{
	/**
	 * @var array
	 */
	public static $cache = [];

	/**
	 * @param $model
	 * @return string
	 */
	private static function generateKey($model)
	{
		return md5(get_class($model));
	}

	/**
	 * @param Model $model
	 */
	public static function cacheModel(Model $model)
	{
		$key = self::generateKey($model);

		if (!isset(self::$cache[$key])) {
			self::$cache[$key] = [];
		}

		$cacheId = $model->getKey();

		self::$cache[$key][$cacheId] = $model;
	}

	/**
	 * @param $model
	 * @param $id
	 * @return null
	 */
	public static function fetchModel($model, $id)
	{
		$key = self::generateKey($model);

		return self::$cache[$key][$id] ?? null;
	}
}