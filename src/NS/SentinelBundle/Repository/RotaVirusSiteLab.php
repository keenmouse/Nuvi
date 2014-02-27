<?php

namespace NS\SentinelBundle\Repository;

use NS\SecurityBundle\Doctrine\SecuredEntityRepository;
use NS\UtilBundle\Service\AjaxAutocompleteRepositoryInterface;
use NS\SentinelBundle\Exceptions\NonExistentCase;
use Doctrine\ORM\NoResultException;

/**
 * SiteLab
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RotaVirusSiteLab extends SecuredEntityRepository implements AjaxAutocompleteRepositoryInterface
{
    public function getForAutoComplete($fields, array $value, $limit)
    {
        $alias = 'd';
        $qb    = $this->_em->createQueryBuilder()
                              ->select($alias)
                              ->from($this->getClassName(), $alias)
                              ->setMaxResults($limit);

        
        if(!empty($value) && $value['value'][0]=='*')
            return $this->secure($qb)->getQuery();
        
        if(!empty($value))
        {
            if(is_array($fields))
            {
                foreach ($fields as $f)
                {
                    $field = "$alias.$f";
                    $qb->addOrderBy($field)
                       ->orWhere("$field LIKE :param")->setParameter('param',$value['value'].'%');
                }
            }
            else
            {
                $field = "$alias.$fields";
                $qb->orderBy($field)->andWhere("$field LIKE :param")->setParameter('param',$value['value'].'%');
            }
        }

        return $this->secure($qb)->getQuery();        
    }

    public function findOrCreateNew($id)
    {
        try
        {
            $r = null;

            if(is_numeric($id))
                $r = $this->find($id);
            else
            {
                $qb = $this->_em
                           ->createQueryBuilder()
                           ->select('r')
                           ->from($this->getClassName(),'r')
                           ->where('r.case = :case')
                           ->setParameter('case',$this->_em->getReference('NSSentinelBundle:RotaVirusSiteLab',$id));

                $r = $this->secure($qb)->getQuery()->getSingleResult();
            }

            if($r)
                return $r;
        }
        catch(NonExistentCase $e)
        {
            $record = new \NS\SentinelBundle\Entity\RotaVirusSiteLab();
            $m      = $this->_em->getRepository('NSSentinelBundle:RotaVirus')->checkExistence($id);
            $record->setCase($m);

            return $record;
        }
    }

    public function find($id)
    {
        try
        {
            $qb = $this->_em
                       ->createQueryBuilder()
                       ->select('m')
                       ->from($this->getClassName(),'m')
                       ->where('m.id = :id')
                       ->setParameter('id', $id);

            return $this->secure($qb)->getQuery()->getSingleResult();    
        }
        catch(NoResultException $e)
        {
            throw new NonExistentCase("This case does not exist!");
        }
    }    
}