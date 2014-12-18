<?php

namespace NS\SentinelBundle\Tests\Controller;

use \NS\SentinelBundle\Tests\BaseWebTestCase;

/**
 * Description of RotaVirusControllerTest
 *
 * @author gnat
 */
class RotaVirusControllerTest extends BaseWebTestCase
{
    const ID = 'CA-ALBCHLD-14-000001';

    public function testRotaEdit()
    {
        // add all your doctrine fixtures classes
        $classes = array(
            // classes implementing Doctrine\Common\DataFixtures\FixtureInterface
            'NS\SentinelBundle\DataFixtures\ORM\LoadRegionData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadReferenceLabsData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadUserData',
            'NS\SentinelBundle\DataFixtures\ORM\LoadRotaVirusCaseData',
        );
        $this->loadFixtures($classes);

        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/edit/' . self::ID);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaIndex()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaShow()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/show/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaLab()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/lab/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaRRL()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/rrl/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaNL()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/nl/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }

    public function testRotaOutcome()
    {
        $client   = $this->login();
        $crawler  = $client->request('GET', '/en/rota/outcome/edit/' . self::ID);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(0, $crawler->filter('div.blockException')->count());
    }
}