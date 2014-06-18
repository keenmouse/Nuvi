<?php

namespace NS\SentinelBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Common\Persistence\ObjectManager;
use NS\SentinelBundle\Interfaces\SerializedSitesInterface;
use \NS\SentinelBundle\Entity\Region;
use \NS\SentinelBundle\Entity\Country;
use \NS\SentinelBundle\Entity\Site;

/**
 * Description of SerializedSites
 *
 * @author gnat
 */
class SerializedSites implements SerializedSitesInterface
{
    private $sites;
    private $em;
    private $isInitialized = false;
    private $session;

    public function __construct(Session $session, ObjectManager $em)
    {
        $this->session = $session;
        $this->em      = $em;
        $this->initialize();
    }

    public function hasMultipleSites()
    {
        if(!$this->isInitialized)
            $this->initialize();

        return (count($this->sites) > 1);
    }

    public function setSites(array $sites)
    {
        if(!$this->isInitialized)
            $this->initialize();

        $this->sites = $sites;
    }

    public function getSites()
    {
        if(!$this->isInitialized)
            $this->initialize();

        return $this->sites;
    }

    public function getSite($managed = true)
    {
        if(!$this->isInitialized)
            $this->initialize();

        $site = current($this->sites);

        if($managed && !$this->em->contains($site))
        {
            $uow = $this->em->getUnitOfWork();
            $c   = $site->getCountry();
            $r   = $c->getRegion();

            $uow->registerManaged($site,array('id'=>$site->getId()),array('id'=>$site->getId(),'code'=>$site->getCode()));
            $uow->registerManaged($c,array('id'=>$c->getId()),array('id'=>$c->getId(),'code'=>$c->getCode()));
            $uow->registerManaged($r,array('id'=>$r->getId()),array('id'=>$r->getId(),'code'=>$r->getCode()));
        }

        return $site;
    }

    public function initialize()
    {
        if(!$this->session->isStarted() || $this->isInitialized)
            return;

        $sites = unserialize($this->session->get('sites'));

        if(!$sites || count($sites) == 0) // empty session site array so build and store
        {
            $sites = array();
            foreach($this->em->getRepository('NS\SentinelBundle\Entity\Site')->getChain() as $site)
            {
                $r = new Region();
                $r->setName($site->getCountry()->getRegion()->getName());
                $r->setId($site->getCountry()->getRegion()->getId());
                $r->setCode($site->getCountry()->getRegion()->getcode());

                $c = new Country();
                $c->setId($site->getCountry()->getId());
                $c->setName($site->getCountry()->getName());
                $c->setCode($site->getCountry()->getCode());
                $c->setLanguage($site->getCountry()->getLanguage());
                $c->setRegion($r);

                $s = new Site();
                $s->setId($site->getId());
                $s->setName($site->getName());
                $s->setCode($site->getCode());
                $s->setCountry($c);

                $sites[] = $s;
            }

            $this->session->set('sites',serialize($sites));
        }

        $this->sites = $sites;
        $this->isInitialized = true;
    }
}