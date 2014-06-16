<?php

namespace NS\SentinelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('plainPassword','repeated',
                     array(
                         'type'            => 'password',
                         'invalid_message' => 'The password fields must match.',
                         'options'         => array('attr' => array('class' => 'password-field', 'autocomplete'=>'off')),
                         'required'        => false,
                         'first_options'   => array('label' => 'Password'),
                         'second_options'  => array('label' => 'Repeat Password'),
                         )
                 )
            ->add('language','lunetics_locale',array('label'=>'Preferred Language'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\SentinelBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ns_sentinelbundle_user';
    }
}
