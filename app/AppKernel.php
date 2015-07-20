<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

            new JMS\TranslationBundle\JMSTranslationBundle(),

            new NS\SentinelBundle\NSSentinelBundle(),
            new NS\SecurityBundle\NSSecurityBundle(),
            new NS\UtilBundle\NSUtilBundle(),
            new NS\AceBundle\NSAceBundle(),
            new NS\TranslateBundle\NSTranslateBundle(),
            new NS\SonataBundle\NSSonataBundle(),
            new NS\ApiDocBundle\NSApiDocBundle(),
            new NS\FlashBundle\NSFlashBundle(),

            new Sonata\CoreBundle\SonataCoreBundle(),
          //new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),

            new PunkAve\FileUploaderBundle\PunkAveFileUploaderBundle(),
            new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Lunetics\LocaleBundle\LuneticsLocaleBundle(),

            new NS\ApiBundle\NSApiBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new NS\ImportBundle\NSImportBundle(),

            new Hpatoio\DeployBundle\DeployBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
            $bundles[] = new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getCacheDir()
    {
        if (in_array($this->environment, array('dev', 'test')))
            return '/dev/shm/nuvi/cache/' .  $this->environment;

        return parent::getCacheDir();
    }

    public function getLogDir()
    {
        if (in_array($this->environment, array('dev', 'test')))
            return '/dev/shm/nuvi/logs';

        return parent::getLogDir();
    }

    protected function initializeContainer()
    {
        static $first = true;

        if ('test' !== $this->getEnvironment())
            return parent::initializeContainer();

        $debug = $this->debug;

        // disable debug mode on all but the first initialization
        if (!$first)
            $this->debug = false;

        // will not work with --process-isolation
        $first = false;

        try
        {
            parent::initializeContainer();
        }
        catch (\Exception $e)
        {
            $this->debug = $debug;
            throw $e;
        }

        $this->debug = $debug;
    }
}
