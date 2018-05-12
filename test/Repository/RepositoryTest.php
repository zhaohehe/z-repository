<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Repository;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Test\DataSource\DataRepository;
use Zhaohehe\Repositories\Eloquent\Cache;
use Zhaohehe\Repositories\Presenter\Presenter;

class RepositoryTest extends TestCase
{
	/**
	 * @var test repository
	 */
	private $repository;

	public function setUp()
	{
		$this->repository = new DataRepository(new Container(), new Collection(), new Presenter());
	}

	public function testFind()
	{
		$firstRecord = $this->repository->find(1);

		$this->assertEquals($firstRecord->title, '关雎');
	}

	public function testFindWithCache()
	{
		$firstRecord = $this->repository->findWithCache(1);

		$this->assertEquals($firstRecord->title, '关雎');
	}
}