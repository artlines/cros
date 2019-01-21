<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ApiEntityRepository extends EntityRepository
{
    public function apiFindBy(array $criteria)
    {
        $qb = $this->createQueryBuilder('a');

        $query = $qb->select('a');

        $count = false;
        $limit = $offset = null;

        $i = 1;
        $parameters = [];
        foreach ($criteria as $key => $value) {
            $_param = 'param_'.$i;

            if (preg_match("/^@(\w+)(?:\-([\w|\-]+))?/", $key, $match)) {
                $operation = $match[1];
                $prop = $match[2] ?? null;

                switch ($operation) {
                    case 'count':
                        $count = true;
                        break;
                    case 'limit':
                        $limit = (int) $value;
                        break;
                    case 'offset':
                        $offset = (int) $value;
                        break;
                    case 'sort':
                        $query->addOrderBy($prop, ":$_param");
                        $parameters[$_param] = $value;
                        break;
                    case 'like':
                        $j = 1;
                        foreach ($value as $field => $val) {
                            $__param = $_param."_$j";
                            $query->andWhere($qb->expr()->like("LOWER(a.$field)", "'%:$__param%'"));
                            $parameters[$__param] = $val;
                        }
                        break;
                    default:
                        // nothing
                        break;
                }
            } elseif (preg_match("/^(\w+)\-(\w+)/", $key, $match)) {
                $name = $match[0];
                $operation = $match[1];

                switch ($operation) {
                    case 'lte':
                    case 'lt':
                    case 'gt':
                    case 'gte':
                        $query->andWhere($qb->expr()->{$operation}($name, ":$_param"));
                        $parameters[$_param] = $value;
                        break;
                    case 'not':
                        $query->andWhere("$name IS NOT :$_param");
                        $parameters[$_param] = $value;
                        break;
                    default:
                        // nothing
                        break;
                }
            } elseif (is_array($value)) {
                $query->andWhere($qb->expr()->in('a.'.$key, ":$_param"));
                $parameters[$_param] = $value;
            } else {
                $query->andWhere($qb->expr()->eq('a.'.$key, ":$_param"));
                $parameters[$_param] = $value;
            }

            $i++;
        }

        $query->setParameters($parameters);

        /**
         * Check for COUNT query
         */
        if ($count) {
            $query->select('count(a.id)');
        } else {
            $query->setMaxResults($limit);
            $query->setFirstResult($offset);
        }


        $queryDQL = $query->getQuery();

        return $count ? $queryDQL->getSingleScalarResult() : $queryDQL->getResult();
    }
}