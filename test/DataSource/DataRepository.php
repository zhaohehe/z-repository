<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Test\DataSource;

use Zhaohehe\Repositories\Eloquent\Repository;

class DataRepository extends Repository
{
	public function model()
	{
		return 'Test\DataSource\DataModel';
	}
}