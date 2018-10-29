<?php declare(strict_types=1);

namespace Pixidos\Doctrine\Queries\Result;


use Doctrine\ORM\Query;
use Pixidos\Doctrine\Queries\AbstractQueryObject;

/**
 * Class ResultSetFactory
 * @package Pixidos\Doctrine\Queries\Result
 * @author Ondra Votava <ondra@votava.it>
 */
interface ResultSetFactoryInterface
{
    public function create(Query $query, AbstractQueryObject $queryObject);
}
