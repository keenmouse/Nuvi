<?php

namespace NS\ImportBundle\Form;

use NS\AceBundle\Form\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of DateFilterType
 *
 * @author gnat
 */
class DateFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'required'               => false,
                'data_extraction_method' => 'default',
            ])
            ->setAllowedValues('data_extraction_method', ['default'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return DatePickerType::class;
    }
}
