<?php

namespace NS\SentinelBundle\Entity\Types;

class AlternateTripleChoice extends \NS\SentinelBundle\Entity\Types\TripleChoice
{
    protected $convert_class = 'NS\SentinelBundle\Form\Types\AlternateTripleChoice';

    public function getName()
    {
        return 'AlternateTripleChoice';
    }   
}

