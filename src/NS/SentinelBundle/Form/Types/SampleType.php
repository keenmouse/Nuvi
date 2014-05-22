<?php

namespace NS\SentinelBundle\Form\Types;

use \NS\UtilBundle\Form\Types\SetChoice;
use NS\UtilBundle\Form\Types\TranslatableArrayChoice;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

/**
 * Description of SampleType
 *
 */
class SampleType extends SetChoice
{
    const CSF        = 1;
    const ISOLATE    = 2;
    const WHOLE      = 3;
    const BROTH      = 4;
    const PLEURAL    = 5;
    const INOCULATED = 6;

    protected $set = array(
                                self::CSF        => 'CSF',
                                self::ISOLATE    => 'Isolate',
                                self::WHOLE      => 'Whole Blood',
                                self::BROTH      => 'Blood culture broth',
                                self::PLEURAL    => 'Pleural Fluid',
                                self::INOCULATED => 'Inoculated',
                             );

    public function getName()
    {
        return 'SampleType';
    }
}
