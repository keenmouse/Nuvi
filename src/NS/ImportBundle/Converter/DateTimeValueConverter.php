<?php

namespace NS\ImportBundle\Converter;

use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter as BaseDateTimeValueConverter;

/**
 * Description of DataeTimeValueConverter
 *
 * @author gnat
 */
class DateTimeValueConverter extends BaseDateTimeValueConverter implements NamedValueConverterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @param string $inputFormat
     * @param string $outputFormat
     */
    public function __construct($inputFormat = null, $outputFormat = null)
    {
        parent::__construct($inputFormat, $outputFormat);

        $this->name = 'Date: ' . $inputFormat;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->description) {
            return sprintf('%s (%s)', $this->name, $this->description);
        }

        return $this->name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
