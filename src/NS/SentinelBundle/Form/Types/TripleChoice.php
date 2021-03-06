<?php

namespace NS\SentinelBundle\Form\Types;

use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use NS\UtilBundle\Form\Types\TranslatableArrayChoice;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of TripleChoice
 *
 * @author gnat
 */
class TripleChoice extends TranslatableArrayChoice implements TranslationContainerInterface
{
    const NO      = 0;
    const YES     = 1;
    const UNKNOWN = 99;

    protected $values = [
        self::NO => 'No',
        self::YES => 'Yes',
        self::UNKNOWN => 'Unknown'
    ];
}
