<?php

namespace NS\ApiBundle\Tests\Form;

use NS\ApiBundle\Form\Types\AuthorizeFormType;
use NS\ApiBundle\Form\Types\OAuthGrantTypes;
use OAuth2\OAuth2;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Description of TypesTest
 *
 * @author gnat
 */
class TypesTest extends TypeTestCase
{

    public function testAuthorizeFormType()
    {
        $formData = ['allowAccess' => 1];
        $form     = $this->factory->create(AuthorizeFormType::class);
        $form->submit($formData);
        $data     = $form->getData();

        $this->assertInstanceOf('NS\ApiBundle\Form\Model\Authorize', $data);
        $this->assertTrue($data->getAllowAccess());
    }

    public function testDontAuthorizeFormType()
    {
        $formData = [];

        $form     = $this->factory->create(AuthorizeFormType::class);
        $form->submit($formData);
        $data     = $form->getData();

        $this->assertInstanceOf('NS\ApiBundle\Form\Model\Authorize', $data);
        $this->assertFalse($data->getAllowAccess());
    }

    public function testOAuthGrantTypes()
    {
        $formData = [
            OAuth2::GRANT_TYPE_AUTH_CODE          => 'authorization_code',
            OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS => 'client_credentials',
            OAuth2::GRANT_TYPE_REFRESH_TOKEN      => 'refresh_token',
        ];

        $form = $this->factory->create(OAuthGrantTypes::class);
        $form->submit($formData);

        $data = $form->getData();
        $this->assertInternalType('array', $data);
        $this->assertCount(3, $data);
    }
}
