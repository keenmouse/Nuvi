<?php

namespace NS\SentinelBundle\Form\Types;

use NS\UtilBundle\Form\Types\ArrayChoice;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Description of Dehydration
 *
 */
class Dehydration extends ArrayChoice
{
    const NONE    = 0;
    const SEVERE  = 1;
    const SOME    = 2;
    const UNKNOWN = 99;

    protected $values = array(
                                self::NONE      => 'None',
                                self::SEVERE    => 'Severe',
                                self::SOME      => 'Some',
                                self::UNKNOWN   => 'Unknown',
                             );

    public function getName()
    {
        return 'Dehydration';
    }
}