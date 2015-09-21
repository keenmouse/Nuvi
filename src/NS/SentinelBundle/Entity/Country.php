<?php

namespace NS\SentinelBundle\Entity;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use \NS\SecurityBundle\Annotation\Secured;
use \NS\SecurityBundle\Annotation\SecuredCondition;
use JMS\Serializer\Annotation\Groups;
use NS\SentinelBundle\Form\Types\TripleChoice;

/**
 * Country
 *
 * @ORM\Table(name="countries",uniqueConstraints={@ORM\UniqueConstraint(name="code_idx", columns={"code"})})
 * @ORM\Entity(repositoryClass="\NS\SentinelBundle\Repository\CountryRepository")
 * @Secured(conditions={
 *      @SecuredCondition(roles={"ROLE_REGION"},relation="region",class="NSSentinelBundle:Region"),
 *      @SecuredCondition(roles={"ROLE_COUNTRY","ROLE_RRL_LAB","ROLE_NL_LAB"},field="id"),
 *      @SecuredCondition(roles={"ROLE_SITE","ROLE_LAB"},relation="site",class="NSSentinelBundle:Site"),
 *      })
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Country implements \Serializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=4)
     * @Groups({"api"})
     */
    private $code;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="active",type="boolean")
     */
    private $active;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"api"})
     */
    private $name;

    /**
     * @var TripleChoice
     * @ORM\Column(name="gaviEligible",type="TripleChoice",nullable=true)
     */
    private $gaviEligible;
    
    /**
     * @var string $hibVaccineIntro
     * @ORM\Column(name="hibVaccineIntro",type="string",nullable=true)
     */
    private $hibVaccineIntro;

    /**
     * @var string $pcvVaccineIntro
     * @ORM\Column(name="pcvVaccineIntro",type="string",nullable=true)
     */
    private $pcvVaccineIntro;

    /**
     * @var string $rvVaccineIntro
     * @ORM\Column(name="rvVaccineIntro",type="string",nullable=true)
     */
    private $rvVaccineIntro;

    /**
     * @var Site
     * 
     * @ORM\OneToMany(targetEntity="Site", mappedBy="country")
     */
    private $sites;
    
    /**
     * @var Region
     * 
     * @ORM\ManyToOne(targetEntity="Region",inversedBy="countries")
     */
    private $region;

    /**
     * @var boolean $tracksPneumonia
     * @ORM\Column(name="tracksPneumonia",type="boolean")
     */
    private $tracksPneumonia = true;

    /**
     * @var boolean $hasReferenceLab
     * @ORM\Column(name="hasReferenceLab",type="boolean")
     */
    private $hasReferenceLab;

    /**
     * @var boolean $hasNationalLab
     * @ORM\Column(name="hasNationalLab",type="boolean")
     */
    private $hasNationalLab;

    /**
     * @var string $language
     * @ORM\Column(name="language",type="string",nullable=true)
     */
    private $language;

    /**
     * @var ReferenceLab $referenceLab
     * @ORM\ManyToOne(targetEntity="ReferenceLab",inversedBy="country")
     */
    private $referenceLab;

    /**
     * @param null $id
     * @param null $name
     */
    public function __construct($id = null, $name = null, $code = null)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->code  = $code;
        $this->sites = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add sites
     *
     * @param \NS\SentinelBundle\Entity\Site $sites
     * @return Country
     */
    public function addSite(\NS\SentinelBundle\Entity\Site $sites)
    {
        $this->sites[] = $sites;
    
        return $this;
    }

    /**
     * Remove sites
     *
     * @param \NS\SentinelBundle\Entity\Site $sites
     */
    public function removeSite(\NS\SentinelBundle\Entity\Site $sites)
    {
        $this->sites->removeElement($sites);
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Set region
     *
     * @param \NS\SentinelBundle\Entity\Region $region
     * @return Country
     */
    public function setRegion(\NS\SentinelBundle\Entity\Region $region = null)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return \NS\SentinelBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Country
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set gaviEligible
     *
     * @param TripleChoice $gaviEligible
     * @return Country
     */
    public function setGaviEligible(TripleChoice $gaviEligible)
    {
        $this->gaviEligible = $gaviEligible;
    
        return $this;
    }

    /**
     * Get gaviEligible
     *
     * @return TripleChoice
     */
    public function getGaviEligible()
    {
        return $this->gaviEligible;
    }

    /**
     * Get tracksPneumonia
     *
     * @return boolean
     */
    public function getTracksPneumonia()
    {
        return $this->tracksPneumonia;
    }

    /**
     * Set tracksPneumonia
     *
     * @param boolean $tracksPneumonia
     * @return Country
     */
    public function setTracksPneumonia($tracksPneumonia)
    {
        $this->tracksPneumonia = $tracksPneumonia;

        return $this;
    }

    /**
     * Get hasReferenceLab
     *
     * @return boolean
     */
    public function hasReferenceLab()
    {
        return $this->hasReferenceLab;
    }

    /**
     * Get hasNationalLab
     *
     * @return boolean
     */
    public function hasNationalLab()
    {
        return $this->hasNationalLab;
    }

    /**
     * Set hasReferenceLab
     * 
     * @param boolean $hasReferenceLab
     * @return Country
     */
    public function setHasReferenceLab($hasReferenceLab)
    {
        $this->hasReferenceLab = $hasReferenceLab;
        return $this;
    }

    /**
     * Set hasNationalLab
     *
     * @param boolean $hasNationalLab
     * @return Country
     */
    public function setHasNationalLab($hasNationalLab)
    {
        $this->hasNationalLab = $hasNationalLab;
        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->code,
            $this->active,
            $this->name,
            $this->gaviEligible,
            $this->region,
            $this->hasNationalLab,
            $this->hasReferenceLab,
            $this->tracksPneumonia,
            $this->language,
            $this->hibVaccineIntro,
            $this->pcvVaccineIntro,
            $this->rvVaccineIntro,
            ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->id,
            $this->code,
            $this->active,
            $this->name,
            $this->gaviEligible,
            $this->region,
            $this->hasNationalLab,
            $this->hasReferenceLab,
            $this->tracksPneumonia,
            $this->language,
            $this->hibVaccineIntro,
            $this->pcvVaccineIntro,
            $this->rvVaccineIntro,
            ) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getHibVaccineIntro()
    {
        return $this->hibVaccineIntro;
    }

    /**
     * @return string
     */
    public function getPcvVaccineIntro()
    {
        return $this->pcvVaccineIntro;
    }

    /**
     * @return string
     */
    public function getRvVaccineIntro()
    {
        return $this->rvVaccineIntro;
    }

    /**
     * @param $hibVaccineIntro
     * @return $this
     */
    public function setHibVaccineIntro($hibVaccineIntro)
    {
        $this->hibVaccineIntro = $hibVaccineIntro;
        return $this;
    }

    /**
     * @param $pcvVaccineIntro
     * @return $this
     */
    public function setPcvVaccineIntro($pcvVaccineIntro)
    {
        $this->pcvVaccineIntro = $pcvVaccineIntro;
        return $this;
    }

    /**
     * @param $rvVaccineIntro
     * @return $this
     */
    public function setRvVaccineIntro($rvVaccineIntro)
    {
        $this->rvVaccineIntro = $rvVaccineIntro;
        return $this;
    }

    /**
     * @return ReferenceLab
     */
    public function getReferenceLab()
    {
        return $this->referenceLab;
    }

    /**
     * @param ReferenceLab $referenceLab
     * @return $this
     */
    public function setReferenceLab(ReferenceLab $referenceLab)
    {
        $this->referenceLab = $referenceLab;
        return $this;
    }

}
