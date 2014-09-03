<?php

namespace NS\SentinelBundle\Form\Filters;

use \Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use \NS\SecurityBundle\Role\ACLConverter;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\FormEvent;
use \Symfony\Component\Form\FormEvents;
use \Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Description of BaseFilter
 *
 * @author gnat
 */
class BaseReportFilterType extends AbstractType
{
    private $securityContext;
    private $converter;

    public function __construct(SecurityContextInterface $sc, ACLConverter $converter)
    {
        $this->securityContext = $sc;
        $this->converter       = $converter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('admDate','filter_date_range',array('label'=>'report-filter-form.admitted-between'))
                ->add('createdAt','filter_date_range',array('label'=>'report-filter-form.created-between'))
                ;

        $sc        = $this->securityContext;
        $converter = $this->converter;
        $siteType  = ( isset($options['site_type']) && $options['site_type'] == 'advanced') ? new SiteFilterType():'site';
        
        $builder->addEventListener(
                    FormEvents::PRE_SET_DATA,
                    function(FormEvent $event) use($sc,$converter,$siteType)
                    {
                        $form  = $event->getForm();
                        $token = $sc->getToken();

                        if($sc->isGranted('ROLE_REGION'))
                        {
                            $objectIds = $converter->getObjectIdsForRole($token,'ROLE_REGION');
//                            if(count($objectIds) > 1)
//                                $form->add('region','region');

                            $form->add('country','country');
                            $form->add('site', $siteType);
                        }

                        if($sc->isGranted('ROLE_COUNTRY'))
                        {
                            $objectIds = $converter->getObjectIdsForRole($token,'ROLE_COUNTRY');
//                            if(count($objectIds) > 1)
//                                $form->add('country','country');

                            $form->add('site',$siteType);
                        }

                        if($sc->isGranted('ROLE_SITE'))
                        {
                            $objectIds = $converter->getObjectIdsForRole($token,'ROLE_SITE');
                            if(count($objectIds) > 1)
                                $form->add('site',$siteType);
                        }

                        $form->add('filter','submit',array('icon' => 'icon-search','attr' => array('class'=>'btn btn-sm btn-success')))
                             ->add('export','submit',array('icon' => 'icon-cloud-download','attr' => array('class'=>'btn btn-sm btn-info')))
                             ->add('reset', 'submit',array('icon' => 'icon-times-circle','attr' => array('class'=>'btn btn-sm btn-danger')))
                            ;
                    }
                    );
    }

    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('site_type'));
        $resolver->setAllowedValues(array('site_type'=>array('simple','advanced')));
    }

    public function getName()
    {
        return 'BaseReportFilterType';
    }
}