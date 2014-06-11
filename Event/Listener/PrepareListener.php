<?php

namespace Lexik\Bundle\FormFilterBundle\Event\Listener;

use Lexik\Bundle\FormFilterBundle\Event\PrepareEvent;
use Lexik\Bundle\FormFilterBundle\Filter\Doctrine\ORMQuery;
use Lexik\Bundle\FormFilterBundle\Filter\Doctrine\DBALQuery;
use Lexik\Bundle\FormFilterBundle\Filter\Doctrine\MongoDBQuery;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;

/**
 * Prepare listener event
 */
class PrepareListener
{
    /**
     * @var boolean
     */
    protected $forceCaseInsensitivity = null;

    public function setForceCaseInsensitivity($value)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException("Expected a boolean");
        }
        $this->forceCaseInsensitivity = $value;

        return $this;
    }

    public function getForceCaseInsensitivity($qb)
    {
        if (isset($this->forceCaseInsensitivity)) {
            return $this->forceCaseInsensitivity;
        }

        if (class_exists('\Doctrine\ORM\QueryBuilder') && $qb instanceof \Doctrine\ORM\QueryBuilder) {
            return $qb->getEntityManager()->getConnection()->getDatabasePlatform()
                instanceof PostgreSqlPlatform;
        }

        if (class_exists('\Doctrine\DBAL\Query\QueryBuilder') && $qb instanceof \Doctrine\DBAL\Query\QueryBuilder) {
            return  $qb->getConnection()->getDatabasePlatform() instanceof PostgreSqlPlatform;
        }
    }

    /**
     * Filter builder prepare event
     *
     * @param PrepareEvent $event
     */
    public function onFilterBuilderPrepare(PrepareEvent $event)
    {
        $qb = $event->getQueryBuilder();

        if (class_exists('\Doctrine\ORM\QueryBuilder') && $qb instanceof \Doctrine\ORM\QueryBuilder) {
            $event->setFilterQuery(new ORMQuery(
                $qb,
                $this->getForceCaseInsensitivity($qb)
            ));
            $event->stopPropagation();

            return;
        }

        if (class_exists('\Doctrine\DBAL\Query\QueryBuilder') && $qb instanceof \Doctrine\DBAL\Query\QueryBuilder) {
            $event->setFilterQuery(new DBALQuery(
                $qb,
                $this->getForceCaseInsensitivity($qb)
            ));
            $event->stopPropagation();

            return;
        }
        
        if (class_exists('Doctrine\ODM\MongoDB\Query\Builder') && $qb instanceof \Doctrine\ODM\MongoDB\Query\Builder) {
            $event->setFilterQuery(new MongoDBQuery(
                $qb,
                $this->getForceCaseInsensitivity($qb)
            ));
            $event->stopPropagation();

            return;
        }
    }
}
