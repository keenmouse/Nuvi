<?php

namespace NS\SentinelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \NS\SecurityBundle\Entity\BaseACL;
use NS\SentinelBundle\Form\Types\Role;
use Symfony\Component\Validator\Constraints as Assert;
use NS\SentinelBundle\Validators as LocalAssert;

/**
 * ACL
 *
 * @ORM\Table(name="acls")
 * @ORM\Entity
 *
 * @LocalAssert\ACL
 */
class ACL extends BaseACL
{
    /**
     * @var User $user
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="acls")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Role $type
     *
     * @ORM\Column(name="type",type="Role")
     */
    protected $type;

    /**
     * @var string $object_id
     *
     * @ORM\Column(name="object_id",type="string",length=15)
     * @Assert\NotBlank(message="Please select a target to restrict this role to")
     */
    protected $object_id;

    /**
     * @var array
     * @ORM\Column(name="options",type="array",nullable=true)
     */
    protected $options = array();

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return sprintf('%s - %s (%d)',$this->type->__toString(), $this->getUser(), $this->object_id);
    }

    /**
     * Set type
     *
     * @param Role $type
     * @return ACL
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return Role
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return ACL
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return ACL
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
}
