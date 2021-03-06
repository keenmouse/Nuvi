<?php

namespace NS\SentinelBundle\Filter\Type\RotaVirus;

use NS\SentinelBundle\Filter\Entity\RotaVirus;
use NS\SentinelBundle\Filter\Type\BaseReportFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of ReportFilterType
 *
 * @author gnat
 */
class ReportFilterType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RotaVirus::class
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return BaseReportFilterType::class;
    }
}
