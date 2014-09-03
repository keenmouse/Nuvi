<?php

namespace NS\ImportBundle\Entity;

/**
 * Description of Import
 *
 * @author gnat
 */
class Import
{
    private $map;
    private $file;

    public function getMap()
    {
        return $this->map;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setMap($map)
    {
        $this->map = $map;
        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}