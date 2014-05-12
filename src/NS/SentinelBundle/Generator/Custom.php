<?php

namespace NS\SentinelBundle\Generator;

use Doctrine\ORM\Id\AbstractIdGenerator;
use NS\SentinelBundle\Interfaces\IdentityAssignmentInterface;
use Doctrine\ORM\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

/**
 *
 * @author gnat
 */
class Custom extends AbstractIdGenerator
{
    static $cache = array();

    public function generate(ObjectManager $em, $entity)
    {
        if(!$entity instanceOf IdentityAssignmentInterface)
            throw new \InvalidArgumentException("Entity must implement IdentityAssignmentInterface");

        $site = $entity->getSite();

        if(is_null($site))
            throw new \UnexpectedValueException("Can't generate an id for meningitis entities without an assigned site");

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('NS\SentinelBundle\Entity\Site', 's');
        $rsm->addFieldResult('s', 'currentCaseId', 'currentCaseId');

        $em->beginTransaction();
        $id = $em->createNativeQuery('SELECT s.currentCaseId FROM sites s WHERE s.id = '.$site->getId(), $rsm)->getResult(Query::HYDRATE_SINGLE_SCALAR);
        $em->getConnection()->executeUpdate('UPDATE sites SET currentCaseId = currentCaseId +1 WHERE id = :id', array('id'=>$site->getId()));
        $em->commit();

        return $entity->getFullIdentifier($id);
    }
}
