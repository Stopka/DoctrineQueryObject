<?php declare(strict_types=1);

namespace Pixidos\Doctrine\Queries\Result;

use Doctrine\ORM\Query;
use Pixidos\Doctrine\Queries\AbstractQueryObject;
use Pixidos\Doctrine\Queries\Exceptions\InvalidClassException;
use function in_array;

/**
 * Class ResultSetFactory
 * @package Pixidos\Doctrine\Queries\Result
 * @author Ondra Votava <ondra@votava.it>
 */
class ResultSetFactory implements ResultSetFactoryInterface
{
    /**
     * @var string class
     */
    private $class;

    public function __construct(string $class)
    {
        if (!in_array(ResultSetInterface::class, class_implements($class), true)) {
            throw new InvalidClassException(sprintf('You must set class what implement "%s" interface', ResultSetInterface::class));
        }

        $this->class = $class;
    }

    public function create(Query $query, AbstractQueryObject $queryObject)
    {
        return new $this->class($query, $queryObject);
    }
}

