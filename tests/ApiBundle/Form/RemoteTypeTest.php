<?php

namespace NS\ApiBundle\Tests\Form;

use NS\ApiBundle\Form\RemoteType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Description of RemoteTypeTest
 *
 * @author gnat
 */
class RemoteTypeTest extends TypeTestCase
{
    public function testForm()
    {
        $formData = [
            'name'          => 'The Name',
            'clientId'      => 'TheClientId',
            'clientSecret'  => 'TheClientSecret',
            'tokenEndpoint' => 'tokenEndpoint',
            'authEndpoint'  => 'authEndpoint',
            'redirectUrl'   => 'redirectUrl',
        ];

        $form = $this->factory->create(RemoteType::class);
        $form->submit($formData);

        $data = $form->getData();
        $this->assertInstanceOf('NS\ApiBundle\Entity\Remote', $data);
        $this->assertEquals('The Name', $data->getName());
        $this->assertEquals('TheClientId', $data->getClientId());
        $this->assertEquals('TheClientSecret', $data->getClientSecret());
        $this->assertEquals('tokenEndpoint', $data->getTokenEndpoint());
        $this->assertEquals('authEndpoint', $data->getAuthEndpoint());
        $this->assertEquals('redirectUrl', $data->getRedirectUrl());
    }
}
