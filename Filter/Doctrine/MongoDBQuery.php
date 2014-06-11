<?php

namespace Lexik\Bundle\FormFilterBundle\Filter\Doctrine;

use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Doctrine\Expression\MongoDBExpressionBuilder;

use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 * @author Javier Sampedro <jsampedro77@gmail.com>
 */
class MongoDBQuery implements QueryInterface
{
    /**
     * @var QueryBuilder $queryBuilder
     */
    private $queryBuilder;

    /**
     * @var MongoDBExpressionBuilder $expr
     */
    private $expressionBuilder;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(
        QueryBuilder $queryBuilder,
        $forceCaseInsensitivity = false)
    {
        $this->queryBuilder      = $queryBuilder;
        $this->expressionBuilder = new MongoDBExpressionBuilder(
            $this->queryBuilder->expr(),
            $forceCaseInsensitivity
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getEventPartName()
    {
        return 'mongodb';
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Get QueryBuilder expr.
     *
     * @return \Doctrine\ODM\MongoDB\Query\Expr
     */
    public function getExpr()
    {
        return $this->queryBuilder->expr();
    }

    /**
     * Get root alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return  '';
    }

    /**
     * Get expr class.
     *
     * @return \Lexik\Bundle\FormFilterBundle\Filter\Doctrine\Expression\ExpressionBuilder
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }
}
