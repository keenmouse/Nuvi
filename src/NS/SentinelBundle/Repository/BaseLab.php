<?php

namespace NS\SentinelBundle\Repository;

use Doctrine\ORM\Query;
use NS\SecurityBundle\Doctrine\SecuredEntityRepository;
use NS\SentinelBundle\Exceptions\NonExistentCaseException;
use NS\UtilBundle\Service\AjaxAutocompleteRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;
use NS\SentinelBundle\Entity\Site;

/**
 * BaseLab
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseLab extends SecuredEntityRepository implements AjaxAutocompleteRepositoryInterface
{
    /**
     * @var null
     */
    protected $parentClass = null;

    /**
     * @param $fields
     * @param array $value
     * @param $limit
     * @return mixed
     */
    public function getForAutoComplete($fields, array $value, $limit)
    {
        $alias = 'd';
        $queryBuilder = $this->createQueryBuilder($alias)->setMaxResults($limit);

        if (!empty($value) && $value['value'][0] == '*') {
            return $this->secure($queryBuilder)->getQuery();
        }

        if (!empty($value)) {
            if (is_array($fields)) {
                foreach ($fields as $f) {
                    $field = "$alias.$f";
                    $queryBuilder->addOrderBy($field)
                        ->orWhere("$field LIKE :param")->setParameter('param', $value['value'] . '%');
                }
            } else {
                $field = "$alias.$fields";
                $queryBuilder->orderBy($field)->andWhere("$field LIKE :param")->setParameter('param', $value['value'] . '%');
            }
        }

        return $this->secure($queryBuilder)->getQuery();
    }

    /**
     * @param $id
     * @return mixed
     * @throws UnexpectedResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOrCreateNew($id)
    {
        try {
            $reference = $this->_em->getReference($this->parentClass, $id);
            $queryBuilder = $this->createQueryBuilder('r')
                ->where('r.caseFile = :case')
                ->setParameter('case', $reference);

            $result = $this->secure($queryBuilder)->getQuery()->getSingleResult();

            if ($result) {
                return $result;
            }
        } catch (UnexpectedResultException $exception) {
            if ($exception instanceof NoResultException || $exception instanceof NonExistentCaseException) {
                $class = $this->getClassName();
                $record = new $class();
                $case = $this->_em->getRepository($this->parentClass)->checkExistence($id);
                $record->setCaseFile($case);

                return $record;
            }

            throw $exception;
        }
    }

    /**
     * @param $site
     * @param $caseId
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function findBySiteAndCaseId($site, $caseId)
    {
        $siteParam = (!$site instanceof Site) ? $this->_em->getReference('NS\SentinelBundle\Entity\Site', $site) : $site;
        $queryB = $this->createQueryBuilder('sl')
            ->innerJoin('sl.caseFile', 'c')
            ->where('c.case_id = :caseId AND c.site = :site')
            ->setParameters(['caseId' => $caseId, 'site' => $siteParam]);

        return $this->secure($queryB)->getQuery()->getSingleResult();
    }

    /**
     * @param mixed $objId
     * @return mixed
     * @throws NonExistentCaseException
     * @throws \Doctrine\ORM\ORMException
     */
    public function find($objId)
    {
        try {
            $queryBuilder = $this->createQueryBuilder('m')
                ->where('m.caseFile = :case')
                ->setParameter('case', $this->_em->getReference($this->parentClass, $objId));

            return $this->secure($queryBuilder)->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getSingleResult();
        } catch (NoResultException $e) {
            throw new NonExistentCaseException("This case does not exist!");
        }
    }
}
