<?php declare(strict_types=1);

namespace Pixidos\Doctrine\Queries;

use ArrayIterator;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Iterator;
use Pixidos\Doctrine\Queries\Result\ResultSetFactoryInterface;
use Pixidos\Doctrine\Queries\Result\ResultSetInterface;

/**
 * Class AbstractQueryObject
 * @package Pixidos\Doctrine\Queries
 * @author Ondra Votava <ondra@votava.it>
 */
abstract class AbstractQueryObject
{

    /**
     * @var Closure[] select
     */
    protected $select = [];

    /**
     * @var Closure[] filters
     */
    protected $filters = [];

    /**
     * @var Closure[] onPostFetch
     */
    protected $onPostFetch = [];

    /**
     * @var EntityManagerInterface entityManager
     */
    private $entityManager;

    /**
     * @var ResultSetFactoryInterface
     */
    private $resultSetFactory;


    /**
     * AbstractQueryObject constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param ResultSetFactoryInterface $resultSetFactory
     */
    public function __construct(EntityManagerInterface $entityManager, ResultSetFactoryInterface $resultSetFactory)
    {
        $this->entityManager = $entityManager;
        $this->resultSetFactory = $resultSetFactory;
    }

    /**
     * @return ResultSetInterface
     */
    public function fetch(): ResultSetInterface
    {
        $query = $this->getQuery();

        return $this->resultSetFactory->create($query, $this);
    }

    /**
     * @return object
     * @throws NoResultException
     */
    public function fetchOne()
    {
        $query = $this->getQuery()
                      ->setMaxResults(1);


        $singleResult = $query->getResult();

        if (!$singleResult) {
            throw new NoResultException(); // simulate getSingleResult()
        }

        $this->postFetch(new ArrayIterator($singleResult));

        /** @noinspection ReturnNullInspection */
        return array_shift($singleResult);
    }

    /**
     * @return null|object
     */
    public function fetchOneOrNull()
    {
        try {

            return $this->fetchOne();

        } catch (NoResultException $exception) {
            return null;
        }
    }

    /**
     * @param Iterator $iterator
     */
    public function postFetch(Iterator $iterator): void
    {
        foreach ($this->onPostFetch as $closure) {
            $closure($this->entityManager->createQueryBuilder(), $iterator);
        }
    }

    /**
     * @param Closure $postFetch
     *
     * @return AbstractQueryObject
     */
    public function addPostFetch(Closure $postFetch): self
    {
        $this->onPostFetch[] = $postFetch;

        return $this;
    }

    /**
     * Told doctrine to index the result by entity.{id}, in the third argument of ->from()
     * is necessary for the proper functioning of postFecth
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    abstract protected function doCreateQuery(QueryBuilder $queryBuilder): QueryBuilder;

    /**
     * @param Closure $filter
     *
     * @return $this
     */
    protected function addFilter(Closure $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param Closure $select
     *
     * @return $this
     */
    protected function addSelect(Closure $select): self
    {
        $this->filters[] = $select;

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function processFilters(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->filters as $modifier) {
            $modifier($queryBuilder);
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function processSelects(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->select as $modifier) {
            $modifier($queryBuilder);
        }

        return $queryBuilder;
    }

    /**
     * @return Query
     */
    protected function getQuery(): Query
    {
        return $this->doCreateQuery(
            $this->entityManager->createQueryBuilder()
        )->getQuery();
    }

    /**
     * @param Iterator $iterator
     *
     * @return array
     */
    protected function getIds(Iterator $iterator): array
    {
        return array_keys(iterator_to_array($iterator, true));
    }
}
