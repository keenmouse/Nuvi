<?php

namespace NS\ImportBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of Result
 *
 * @author gnat
 */
class Result extends EntityRepository
{
    public function getResultsForUser(UserInterface $user, $alias ='r')
    {
        return $this->createQueryBuilder($alias)
                    ->where(sprintf('%s.user = :user',$alias))
                    ->setParameter('user', $this->_em->getReference(get_class($user), $user->getId()))
                    ->orderBy($alias.'.id','DESC');
    }

    public function findForUser(UserInterface $user, $id)
    {
        return $this->createQueryBuilder('r')
                    ->where('r.user = :user AND r.id = :id')
                    ->setParameters(array('user'=>$this->_em->getReference(get_class($user), $user->getId()),'id'=>$id))
                    ->getQuery()
                    ->getSingleResult();
    }
}
