<?php

namespace NS\SentinelBundle\Entity\Types;

use NS\UtilBundle\Entity\Types\ArrayChoice;

/**
 * Class ThreeDoses
 * @package NS\SentinelBundle\Entity\Types
 */
class ThreeDoses extends ArrayChoice
{
    /**
     * @var string
     */
    protected $convert_class = 'NS\SentinelBundle\Form\Types\ThreeDoses';

    /**
     * @return string
     */
    public function getName()
    {
        return 'ThreeDoses';
    }
}
