<?php

namespace NS\SentinelBundle\Repository;

use NS\SentinelBundle\Repository\Common as CommonRepository;

/**
 * Site
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SiteRepository extends CommonRepository
{
    /**
     * @param null $codes
     * @param bool $includeInactive
     * @return array
     */
    public function getChain($codes = null, $includeInactive = false)
    {
        $queryBuilder = $this->getChainQueryBuilder($includeInactive);

        if (is_array($codes)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('s.code', '?1'))->setParameter(1, $codes);
        } elseif ($codes !== null) {
            $queryBuilder->andWhere('s.code = :code')->setParameter('code', $codes);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     *
     * @param bool $includeInactive
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getChainQueryBuilder($includeInactive = false)
    {
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->from($this->getEntityName(), 's', 's.code')
            ->select('s,c,r')
            ->innerJoin('s.country', 'c')
            ->innerJoin('c.region', 'r');

        if (!$includeInactive) {
            $queryBuilder->where('s.active = :isActive')->setParameter('isActive', true);
        }

        return $this->secure($queryBuilder);
    }

    /**
     * @param $codes
     * @return array
     */
    public function getChainByCode($codes)
    {
        $queryBuilder = $this->getChainQueryBuilder();

        if (is_array($codes)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('s.code', '?1'))->setParameter(1, $codes);
        } elseif (is_string($codes)) {
            $queryBuilder->andWhere('s.code = :codes')->setParameter('codes', $codes);
        } else {
            throw new \InvalidArgumentException(sprintf("Must provide an array of codes or single code. Received: %s", gettype($codes)));
        }

        return $this->secure($queryBuilder)->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->secure($this->_em->createQueryBuilder('s')->select('s')->from('NS\SentinelBundle\Entity\Site','s','s.code')->where('s.active = :isActive')->setParameter('isActive', true)->orderBy('s.name', 'ASC'))->getQuery()->getResult();
    }

    /**
     * @param $alias
     * @param $caseClass
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getWithCasesForDate($alias, $caseClass)
    {
        return $this->secure($this->_em->createQueryBuilder()
            ->select("$alias, s, c, r, COUNT($alias) as totalCases")
            ->from($caseClass, $alias)
            ->innerJoin("$alias.site", 's', 's.code')
            ->innerJoin('s.country', 'c')
            ->innerJoin('c.region', 'r')
            ->groupBy("$alias.site")
            ->addOrderBy('r.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('s.name', 'ASC'));
    }

    /**
     * @inheritDoc
     */
    public function getAllSecuredQueryBuilder($alias = 'o')
    {
        $countryAlias = ($alias == 'c') ? 'cntry':'c';
        return parent::getAllSecuredQueryBuilder($alias)
            ->addSelect($countryAlias)
            ->innerJoin(sprintf('%s.country',$alias),$countryAlias)
            ->orderBy("$countryAlias.name,$alias.name",'ASC');
    }

    /**
     * @param $fields
     * @param array $value
     * @param $limit
     * @return \Doctrine\ORM\Query
     */
    public function getForAutoComplete($fields, array $value, $limit)
    {
        $alias = 's';
        $queryBuilder = $this->createQueryBuilder($alias)
            ->addSelect('c')
            ->innerJoin('s.country','c')
            ->setMaxResults($limit);

        if (!empty($value) && $value['value'][0] == '*') {
            return $queryBuilder->getQuery();
        }

        if (!empty($value)) {
            if (is_array($fields)) {
                foreach ($fields as $f) {
                    $field = "$alias.$f";
                    $queryBuilder->addOrderBy($field)
                        ->orWhere("$field LIKE :param")->setParameter('param', '%'.$value['value'].'%');
                }
            } else {
                $field = "$alias.$fields";
                $queryBuilder->orderBy($field)->andWhere("$field LIKE :param")->setParameter('param', '%'.$value['value'].'%');
            }
        }

        return $this->secure($queryBuilder)->getQuery();
    }
}
