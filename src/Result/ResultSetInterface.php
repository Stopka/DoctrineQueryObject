<?php declare(strict_types=1);

namespace Pixidos\Doctrine\Queries\Result;


use Countable;
use IteratorAggregate;

/**
 * Class ResultSet
 * @package OnlinePujcka\Queries
 * @author Ondra Votava <ondrej.votava@mediafactory.cz>
 */
interface ResultSetInterface extends Countable, IteratorAggregate
{
    /**
     * @return bool
     */
    public function isEmpty(): bool;

}
