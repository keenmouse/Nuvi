<?php

namespace NS\SentinelBundle\Filter\Type\IBD;

use NS\SentinelBundle\Filter\Entity\IBD;
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
            'data_class' => IBD::class
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
