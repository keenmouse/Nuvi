<?php

namespace NS\SentinelBundle\Repository;

use NS\SecurityBundle\Doctrine\SecuredEntityRepository;
use NS\UtilBundle\Service\AjaxAutocompleteRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;

/**
 * BaseLab
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseLab extends SecuredEntityRepository implements AjaxAutocompleteRepositoryInterface
{

    protected $parentClass = null;

    public function getForAutoComplete($fields, array $value, $limit)
    {
        $alias        = 'd';
        $queryBuilder = $this->createQueryBuilder($alias)->setMaxResults($limit);

        if (!empty($value) && $value['value'][0] == '*')
            return $this->secure($queryBuilder)->getQuery();

        if (!empty($value))
        {
            if (is_array($fields))
            {
                foreach ($fields as $f)
                {
                    $field = "$alias.$f";
                    $queryBuilder->addOrderBy($field)
                        ->orWhere("$field LIKE :param")->setParameter('param', $value['value'] . '%');
                }
            }
            else
            {
                $field = "$alias.$fields";
                $queryBuilder->orderBy($field)->andWhere("$field LIKE :param")->setParameter('param', $value['value'] . '%');
            }
        }

        return $this->secure($queryBuilder)->getQuery();
    }

    public function findOrCreateNew($id)
    {
        try
        {
            $reference    = $this->_em->getReference($this->parentClass, $id);
            $queryBuilder = $this->createQueryBuilder('r')
                ->where('r.case = :case')
                ->setParameter('case', $reference);

            $r = $this->secure($queryBuilder)->getQuery()->getSingleResult();

            if ($r)
                return $r;
        }
        catch (UnexpectedResultException $e)
        {
            if ($e instanceof NoResultException || $e instanceof NonExistentCase)
            {
                $class  = $this->getClassName();
                $record = new $class();
                $m      = $this->_em->getRepository($this->parentClass)->checkExistence($id);
                $record->setCase($m);

                return $record;
            }

            throw $e;
        }
    }

    public function find($objId)
    {
        try
        {
            $queryBuilder = $this->createQueryBuilder('m')
                ->where('m.case = :case')
                ->setParameter('case', $this->_em->getReference($this->parentClass, $objId));

            return $this->secure($queryBuilder)->getQuery()->getSingleResult();
        }
        catch (NoResultException $e)
        {
            throw new NonExistentCase("This case does not exist!");
        }
    }

}
