<?php

namespace NS\SentinelBundle\Form\IBD\Types;

use \NS\SentinelBundle\Form\IBD\Transformer\CTValueTransformer;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of CTValue
 *
 * @author gnat
 */
class CTValueType extends AbstractType
{
    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            -3 => 'No CT Value',
            -2 => 'Negative',
            -1 => 'Undetermined',
        ];

        $builder
            ->add('choice', 'choice', ['choices' => $choices, 'placeholder' => "[Enter value]", 'label' => 'Non-result choices', 'attr' => ['class' => 'inputOrSelect']])
            ->add('number', 'number', ['precision' => 2, 'label' => '', 'attr' => ['class' => 'inputOrSelect' ,'placeholder'=>'Enter value or Select']])
            ->addModelTransformer(new CTValueTransformer());
    }
}
