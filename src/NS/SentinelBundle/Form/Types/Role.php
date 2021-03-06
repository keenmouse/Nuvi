<?php

namespace NS\SentinelBundle\Form\Types;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContext;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use NS\UtilBundle\Form\Types\TranslatableArrayChoice;

/**
 * Description of Role
 *
 * @author gnat
 */
class Role extends TranslatableArrayChoice implements TranslationContainerInterface
{
    private $tokenStorage;

    const REGION = 1;
    const COUNTRY = 2;
    const SITE = 3;
    const LAB = 4;
    const RRL_LAB = 5;
    const NL_LAB = 6;

    // These are all deprecated and will be removed in a future version
    const REGION_API = 10;
    const COUNTRY_API = 11;
    const SITE_API = 12;

    const REGION_IMPORT = 20;
    const COUNTRY_IMPORT = 21;
    const SITE_IMPORT = 22;

    protected $values = [
        self::REGION => 'Region',
        self::COUNTRY => 'Country',
        self::SITE => 'Site',
        self::LAB => 'Lab',
        self::RRL_LAB => 'RRL',
        self::NL_LAB => 'NL',
        // These are all deprecated and will be removed in a future version

        self::REGION_API => 'Region API',
        self::COUNTRY_API => 'Country API',
        self::SITE_API => 'Site API',
        self::REGION_IMPORT => 'Region Import/Export',
        self::COUNTRY_IMPORT => 'Country Import/Export',
        self::SITE_IMPORT => 'Site Import/Export',
    ];

    protected $roleMap = [
        'ROLE_REGION' => self::REGION,
        'ROLE_COUNTRY' => self::COUNTRY,
        'ROLE_SITE' => self::SITE,
        'ROLE_LAB' => self::LAB,
        'ROLE_RRL_LAB' => self::RRL_LAB,
        'ROLE_NL_LAB' => self::NL_LAB,
        // These are all deprecated and will be removed in a future version

        'ROLE_REGION_API' => self::REGION_API,
        'ROLE_COUNTRY_API' => self::COUNTRY_API,
        'ROLE_SITE_API' => self::SITE_API,
        'ROLE_REGION_IMPORT' => self::REGION_IMPORT,
        'ROLE_COUNTRY_IMPORT' => self::COUNTRY_IMPORT,
        'ROLE_SITE_IMPORT' => self::SITE_IMPORT,
    ];

    /**
     *
     * @param string $value
     * @return Role
     * @throws \UnexpectedValueException
     */
    public function __construct($value = null)
    {
        if (is_string($value) && strstr($value, 'ROLE_') !== false) {
            if (isset($this->roleMap[$value])) {
                $value = $this->roleMap[$value];
            } else {
                throw new \UnexpectedValueException("$value is not a valid role mapping");
            }
        }

        parent::__construct($value);
    }

    /**
     * @return array
     */
    public function getAsCredential()
    {
        switch ($this->current) {
            case self::REGION:
                return ['ROLE_REGION'];
            case self::COUNTRY:
                return ['ROLE_COUNTRY'];
            case self::SITE:
                return ['ROLE_SITE'];
            case self::LAB:
                return ['ROLE_LAB'];
            case self::RRL_LAB:
                return ['ROLE_RRL_LAB'];
            case self::NL_LAB:
                return ['ROLE_NL_LAB'];
            // These are all deprecated and will be removed in a future version
            case self::REGION_API:
                return ['ROLE_REGION_API', 'ROLE_CAN_CREATE_CASE', 'ROLE_CAN_CREATE_LAB', 'ROLE_CAN_CREATE_NL_LAB'];
            case self::COUNTRY_API:
                return ['ROLE_COUNTRY_API', 'ROLE_CAN_CREATE_CASE', 'ROLE_CAN_CREATE_LAB', 'ROLE_CAN_CREATE_NL_LAB'];
            case self::SITE_API:
                return ['ROLE_SITE_API', 'ROLE_CAN_CREATE_CASE', 'ROLE_CAN_CREATE_LAB'];
            case self::REGION_IMPORT:
                return ['ROLE_REGION_IMPORT'];
            case self::COUNTRY_IMPORT:
                return ['ROLE_COUNTRY_IMPORT'];
            case self::SITE_IMPORT:
                return ['ROLE_SITE_IMPORT'];
            default:
                return [];
        }
    }

    /**
     * @return string
     */
    public function getClassMatch()
    {
        $class = 'NS\SentinelBundle\Entity';
        switch ($this->current) {
            case self::REGION:
            case self::REGION_API:
            case self::REGION_IMPORT:
                return $class . '\Region';
            case self::COUNTRY:
            case self::COUNTRY_API:
            case self::COUNTRY_IMPORT:
            case self::NL_LAB:
            case self::RRL_LAB:
                return $class . '\Country';
            case self::LAB:
            case self::SITE:
            case self::SITE_API:
            case self::SITE_IMPORT:
                return $class . '\Site';
            default:
                return null;
        }
    }

    /**
     *
     * @param array $roles
     * @return integer
     */
    public function getHighest(array $roles)
    {
        $highest = null;

        foreach ($roles as $role) {
            if (isset($this->roleMap[$role->getRole()])) {
                if ($highest === null) {
                    $highest = $this->roleMap[$role->getRole()];
                } elseif ($highest > $this->roleMap[$role->getRole()]) { //highest is actually 1...
                    $highest = $this->roleMap[$role->getRole()];
                }
            }
        }

        return $highest;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @return Role
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $highest = $this->getHighest($this->tokenStorage->getToken()->getRoles());

        if (!is_null($highest) && $highest != self::REGION) {
            $values = $this->values;
            foreach (array_keys($values) as $key) {
                if ($key < $highest) {
                    unset($values[$key]);
                }
            }

            $resolver->setDefaults([
                'choices' => array_flip($values),
                'placeholder' => 'Please Select...',
            ]);
        } else {
            $resolver->setDefaults([
                'choices' => array_flip($this->values),
                'placeholder' => 'Please Select...',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function equal($compared)
    {
        if (is_numeric($compared)) {
            return ($compared == $this->current);
        } elseif (is_string($compared)) {
            return ([$compared] == $this->getAsCredential());
        } elseif ($compared instanceof Role) {
            return ($compared->getValue() == $this->current);
        }

        return false;
    }
}
