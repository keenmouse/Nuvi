<?php

namespace NS\SentinelBundle\Tests\Services;

use NS\SentinelBundle\Entity\Site;
use NS\SentinelBundle\Entity\Country;
use NS\SentinelBundle\Entity\Region;
use NS\SentinelBundle\Services\SerializedSites;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * Description of SerializedSiteTest
 *
 * @author gnat
 */
class SerializedSiteTest extends \PHPUnit_Framework_TestCase
{
    public function testSitesCanBeSerialized()
    {
        $sites           = $this->getObjects();
        $serializedSites = serialize($sites);
        $unSites         = unserialize($serializedSites);

        $this->assertEquals(count($sites), count($unSites), "We have the same number of");

        foreach ($unSites as $s) {
            $this->assertGreaterThan(0, $s->getId(), "We still have an id");
        }
    }

    public function testSerializedSitesHasMultipleSites()
    {
        $session        = new Session(new MockFileSessionStorage());
        $session->start();
        $em             = $this->getMockObjectManager();
        $siteSerializer = new SerializedSites($session, $em);
        $site           = $siteSerializer->getSite();

        $this->assertTrue($siteSerializer->hasMultipleSites(), "Has multiple sites");
        $this->assertEquals($site->getId(), 1);
    }

    public function testSerializedSitesHasIds()
    {
        $session        = new Session(new MockFileSessionStorage());
        $session->start();
        $em             = $this->getMockObjectManager();
        $siteSerializer = new SerializedSites($session, $em);

        $this->assertTrue($siteSerializer->hasMultipleSites(), "Has multiple sites");
        foreach ($siteSerializer->getSites() as $s) {
            $this->assertGreaterThan(0, $s->getId(), "Id is greater than 0");
        }
    }

    private function getMockObjectManager()
    {
        $obj  = $this->getObjects();
        $repo = $this->createMock('NS\SentinelBundle\Repository\SiteRepository');

        $repo->expects($this->any())
             ->method('getChain')
             ->will($this->returnValue($obj));

        $em = $this->createMock('Doctrine\Common\Persistence\ObjectManager');

        $em->expects($this->any())
           ->method('getRepository')
           ->with('NS\SentinelBundle\Entity\Site')
           ->will($this->returnValue($repo));

        $em->expects($this->any())
           ->method('contains')
           ->will($this->returnValue(true));

        return $em;
    }

    private function getObjects()
    {
        $region = new Region(1, 'Region Name');

        $country = new Country(1, 'Country Name');
        $country->setRegion($region);

        $site1 = new Site(1, 'Site 1');
        $site1->setCountry($country);

        $site2 = new Site(2, 'Site 2');
        $site2->setCountry($country);

        $site3 = new Site(3, 'Site 3');
        $site3->setCountry($country);

        return [$site1, $site2, $site3];
    }

    public function testRegisterSite()
    {
        $region = new Region('rCode', 'RegionName');
        $country = new Country('cCode', 'CountryName');
        $country->setRegion($region);
        $site = new Site('sId', 'SiteName');
        $site->setCountry($country);

        $mockUoW = $this->createMock('Doctrine\ORM\UnitOfWork');

        $mockUoW->expects($this->at(0))
            ->method('registerManaged')
            ->with($site,   ['code' => 'sId'], ['code' => 'sId']);

        $mockUoW->expects($this->at(1))
            ->method('registerManaged')
            ->with($country, ['code' => 'cCode'], ['code'=>'cCode']);

        $mockUoW->expects($this->at(2))
            ->method('registerManaged')
            ->with($region, ['code' => 'rCode'], ['code'=>'rCode']);

        $mockEntityMgr = $this->createMock('Doctrine\ORM\EntityManager');

        $mockEntityMgr->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($mockUoW);

        $session        = new Session(new MockFileSessionStorage());
        $session->start();

        $serializedSites = new SerializedSites($session, $mockEntityMgr);
        $serializedSites->registerSite($site);
    }
}
