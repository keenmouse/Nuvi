<?php

namespace NS\SentinelBundle\Form\Types;

/**
 * Description of RotaCreateRoles
 *
 * @author gnat
 */
class RotavirusCreateRoles extends CreateRoles
{
    const ROUTE_BASE = 'rotavirus';

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'CreateRoles';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'RotavirusCreateRoles';
    }
}
