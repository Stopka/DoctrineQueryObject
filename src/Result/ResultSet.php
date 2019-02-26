<?php declare(strict_types=1);

namespace Pixidos\Doctrine\Queries\Result;

use ArrayIterator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Pixidos\Doctrine\Queries\AbstractQueryObject;

/**
 * Class ResultSet
 * @package Pixidos\Doctrine\Queries\Result
 * @author Ondra Votava <ondra@votava.it>
 */
class ResultSet implements ResultSetInterface
{

    /**
     * @var ArrayIterator|null iterator
     */
    private $iterator;
    /**
     * @var Query query
     */
    private $query;
    /**
     * @var AbstractQueryObject queryObject
     */
    private $queryObject;

    /**
     * ResultSet constructor.
     *
     * @param Query               $query
     * @param AbstractQueryObject $queryObject
     */
    public function __construct(Query $query, AbstractQueryObject $queryObject)
    {
        $this->query = $query;
        $this->queryObject = $queryObject;
    }


    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        if ($this->iterator !== null) {
            return $this->iterator;
        }

        $this->query->setHydrationMode(AbstractQuery::HYDRATE_OBJECT);

        $this->iterator = new ArrayIterator($this->query->execute());
        $this->queryObject->_postFetch($this->iterator);

        return $this->iterator;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

}
