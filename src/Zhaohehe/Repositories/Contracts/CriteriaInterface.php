<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Contracts;

use Zhaohehe\Repositories\Criteria\Criteria;

interface CriteriaInterface
{
    /**
     * @param bool $status
     *
     * @return mixed
     */
    public function skipCriteria($status = true);

    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param Criteria $criteria
     *
     * @return mixed
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * @param Criteria $criteria
     *
     * @return mixed
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @return mixed
     */
    public function applyCriteria();
}