   ```php
    public function withCategories(): self
    {
        
        $this->onPostFetch[] = function (QueryBuilder $qb, Iterator $iterator) {
            $qb->select('partial client.{id}')->from(Client::class, 'client')
               ->leftJoin('client.categories', 'categories')->addSelect('categories')
               ->andWhere('client.id IN (:ids)')
               ->setParameter('ids', $this->getIds($iterator))
               ->getQuery()->getResult();
        };
        
        
        return $this;
    }
    
    
    public function hasCategories(array $ids): self
    {
        if (empty($ids)) {
            throw new LogicException(sprintf('parameter %s::$ids can not be empty', __CLASS__));
        }
        
        $this->filters[] = function (QueryBuilder $qb) use ($ids) {
            $qb->andWhere('client.categorie IN (:catogeries)')
               ->setParameter('catogeries', $ids);
        };
        
        return $this;
    }
        
        
        
    protected function doCreateQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder->select('client')
                     ->from(Client::class, 'client', 'client.id');
        
        $this->processSelects($queryBuilder);
        $this->processFilters($queryBuilder);
        
        return $queryBuilder;
    }
```
