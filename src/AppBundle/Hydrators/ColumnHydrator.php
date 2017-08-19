<?php

namespace AppBundle\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Custom hydrator for doctrine to use PDO::FETCH_COLUMN:
 * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html#custom-hydration-modes
 *
 * @subpackage Hydrators
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ColumnHydrator extends AbstractHydrator
{
	/**
	 * {@inheritdoc}
	 * @return [type] [description]
	 */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}