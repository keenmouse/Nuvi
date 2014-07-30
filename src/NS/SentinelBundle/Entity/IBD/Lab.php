<?php

namespace NS\SentinelBundle\Entity\IBD;

use Doctrine\ORM\Mapping as ORM;
use NS\SentinelBundle\Entity\BaseLab;
use NS\SentinelBundle\Form\Types\TripleChoice;
use NS\SentinelBundle\Form\Types\LatResult;
use NS\SentinelBundle\Form\Types\CultureResult;
use NS\SentinelBundle\Form\Types\GramStain;
use NS\SentinelBundle\Form\Types\GramStainOrganism;
use NS\SentinelBundle\Form\Types\BinaxResult;
use NS\SentinelBundle\Form\Types\PCRResult;
use NS\SentinelBundle\Form\Types\CaseStatus;
use NS\SentinelBundle\Form\Types\PathogenIdentifier;
use NS\SentinelBundle\Form\Types\SerotypeIdentifier;
use Symfony\Component\Validator\ExecutionContextInterface;
use NS\SentinelBundle\Form\Types\SpnSerotype;
use NS\SentinelBundle\Form\Types\HiSerotype;
use NS\SentinelBundle\Form\Types\NmSerogroup;
use NS\SentinelBundle\Form\Types\Volume;

use Gedmo\Mapping\Annotation as Gedmo;
use \NS\SecurityBundle\Annotation\Secured;
use \NS\SecurityBundle\Annotation\SecuredCondition;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Lab
 * @author gnat
 * @ORM\Entity(repositoryClass="NS\SentinelBundle\Repository\IBD\Lab")
 * @ORM\Table(name="ibd_labs")
 * @Gedmo\Loggable
 * @Secured(conditions={
 *      @SecuredCondition(roles={"ROLE_REGION","ROLE_REGION_API"},through={"case"},relation="region",class="NSSentinelBundle:Region"),
 *      @SecuredCondition(roles={"ROLE_COUNTRY","ROLE_COUNTRY_API"},through={"case"},relation="country",class="NSSentinelBundle:Country"),
 *      @SecuredCondition(roles={"ROLE_SITE","ROLE_LAB","ROLE_SITE_API"},through="case",relation="site",class="NSSentinelBundle:Site"),
 *      })
 * @Assert\Callback(methods={"validate"})
 */
class Lab extends BaseLab
{
// Common
    /**
     * @ORM\OneToOne(targetEntity="\NS\SentinelBundle\Entity\Meningitis",inversedBy="lab")
     * @ORM\JoinColumn(nullable=false,unique=true)
     */
    protected $case;
    protected $caseClass = '\NS\SentinelBundle\Entity\Meningitis';
//-----------------------------------------
// CSF
    /**
     * @var string $siteCsfId
     * @ORM\Column(name="siteCsfId",type="string",nullable=true)
     */
    private $csfSiteId;

    /**
     * @var string $csfNLId
     * @ORM\Column(name="csfNLId",type="string",nullable=true)
     */
    private $csfNLId;

    /**
     * @var string $csfRRLId
     * @ORM\Column(name="csfRRLId",type="string",nullable=true)
     */
    private $csfRRLId;

    /**
     * @var \DateTime $csfSiteDateTime
     * @ORM\Column(name="csfSiteDateTime",type="datetime",nullable=true)
     * @Assert\DateTime
     */
    private $csfSiteDateTime;

    /**
     * @var \DateTime $csfNLDateTime
     * @ORM\Column(name="csfNLDateTime",type="datetime",nullable=true)
     */
    private $csfNLDateTime;

    /**
     * @var \DateTime $csfRRLDateTime
     * @ORM\Column(name="csfRRLDateTime",type="datetime",nullable=true)
     */
    private $csfRRLDateTime;

    /**
     * @var integer $csfWcc
     * @ORM\Column(name="csfWcc", type="integer",nullable=true)
     * @Assert\Range(min=0,max=9999,minMessage="You cannot have a negative white blood cell count",maxMessage="Invalid value")
     */
    private $csfWcc;

    /**
     * @var integer $csfGlucose
     * @ORM\Column(name="csfGlucose", type="integer",nullable=true)
     * @Assert\GreaterThanOrEqual(value=0,message="Invalid value - value must be greater than 0")
     *
     */
    private $csfGlucose;

    /**
     * @var integer $csfProtein
     * @ORM\Column(name="csfProtein", type="integer",nullable=true)
     * @Assert\GreaterThanOrEqual(value=0,message="Invalid value - value must be greater than 0")
     */
    private $csfProtein;

    /**
     * @var TripleChoice $csfCultDone
     * @ORM\Column(name="csfCultDone",type="TripleChoice",nullable=true)
     */
    private $csfCultDone;

    /**
     * @var TripleChoice $csfGramDone
     * @ORM\Column(name="csfGramDone",type="TripleChoice",nullable=true)
     */
    private $csfGramDone;

    /**
     * @var TripleChoice $csfBinaxDone
     * @ORM\Column(name="csfBinaxDone",type="TripleChoice",nullable=true)
     */
    private $csfBinaxDone;

    /**
     * @var TripleChoice $csfLatDone
     * @ORM\Column(name="csfLatDone",type="TripleChoice",nullable=true)
     */
    private $csfLatDone;

    /**
     * @var TripleChoice $csfPcrDone
     * @ORM\Column(name="csfPcrDone",type="TripleChoice",nullable=true)
     */
    private $csfPcrDone;

    /**
     * @var CultureResult $csfCultResult
     * @ORM\Column(name="csfCultResult",type="CultureResult",nullable=true)
     */
    private $csfCultResult;

    /**
     * @var string $csfCultOther
     * @ORM\Column(name="csfCultOther",type="string",nullable=true)
     */
    private $csfCultOther;

    /**
     * @var GramStain
     * @ORM\Column(name="csfGramResult",type="GramStain",nullable=true)
     */
    private $csfGramResult;

    /**
     * @var GramStainOrganism $csfGramResultOrganism
     * @ORM\Column(name="csfGramResultOrganism",type="GramStainOrganism",nullable=true)
     */
    private $csfGramResultOrganism;

    /**
     * @var string $csfGramOther
     * @ORM\Column(name="csfGramOther",type="string",nullable=true)
     */
    private $csfGramOther;

    /**
     * @var BinaxResult
     * @ORM\Column(name="csfBinaxResult",type="BinaxResult",nullable=true)
     */
    private $csfBinaxResult;

    /**
     * @var LatResult
     * @ORM\Column(name="csfLatResult",type="LatResult",nullable=true)
     */
    private $csfLatResult;

    /**
     * @var string
     * @ORM\Column(name="csfLatOther",type="string",nullable=true)
     */
    private $csfLatOther;

    /**
     * @var PCRResult
     * @ORM\Column(name="csfPcrResult",type="PCRResult",nullable=true)
     */
    private $csfPcrResult;

    /**
     * @var string $csfPcrOther
     * @ORM\Column(name="csfPcrOther",type="string",nullable=true)
     */
    private $csfPcrOther;

    /**
     * @var TripleChoice $csfSentToRRL
     * @ORM\Column(name="csfSentToRRL",type="TripleChoice",nullable=true)
     */
    private $csfSentToRRL;

    /**
     * @var \DateTime $csfDateSentToRRL
     * @ORM\Column(name="csfDateSentToRRL",type="date",nullable=true)
     */
    private $csfDateSentToRRL;

    /**
     * @var TripleChoice $csfSentToNL
     * @ORM\Column(name="csfSentToNL",type="TripleChoice",nullable=true)
     */
    private $csfSentToNL;

    /**
     * @var \DateTime $csfDateSentToNL
     * @ORM\Column(name="csfDateSentToNL",type="date",nullable=true)
     */
    private $csfDateSentToNL;

    /**
     * @var TripleChoice $csfStore
     * @ORM\Column(name="csfStore",type="TripleChoice",nullable=true)
     */
    private $csfStore;

    /**
     * @var TripleChoice $csfIsolStore
     * @ORM\Column(name="csfIsolStore",type="TripleChoice",nullable=true)
     */
    private $csfIsolStore;

    /**
     * @var Volume $csfVolume
     * @ORM\Column(name="csfVolume",type="Volume",nullable=true)
     */
    private $csfVolume;

    /**
     * @var \DateTime $csfSiteDNAExtractionDate
     * @ORM\Column(name="csfSiteDNAExtractionDate",type="date",nullable=true)
     */
    private $csfSiteDNAExtractionDate;

    /**
     * @var integer $csfSiteDNAVolume
     * @ORM\Column(name="csfSiteDNAVolume",type="integer",nullable=true)
     */
    private $csfSiteDNAVolume;

    /**
     * @var \DateTime $csfNLDNAExtractionDate
     * @ORM\Column(name="csfNLDNAExtractionDate",type="date",nullable=true)
     */
    private $csfNLDNAExtractionDate;

    /**
     * @var integer $csfNLDNAVolume
     * @ORM\Column(name="csfNLDNAVolume",type="integer",nullable=true)
     */
    private $csfNLDNAVolume;

    /**
     * @var \DateTime $csfRRLDNAExtractionDate
     * @ORM\Column(name="csfRRLDNAExtractionDate",type="date",nullable=true)
     */
    private $csfRRLDNAExtractionDate;

    /**
     * @var integer $csfRRLDNAVolume
     * @ORM\Column(name="csfRRLDNAVolume",type="integer",nullable=true)
     */
    private $csfRRLDNAVolume;

//-----------------------------------------
// Blood
    /**
     * @var string $bloodSiteId
     * @ORM\Column(name="bloodSiteId",type="string",nullable=true)
     */
    private $bloodSiteId;

    /**
     * @var \DateTime $bloodSiteDateTime
     * @ORM\Column(name="bloodSiteDateTime",type="date",nullable=true)
     * @Assert\DateTime
     */
    private $bloodSiteDateTime;

    /**
     * @var TripleChoice $bloodSentToNL
     * @ORM\Column(name="bloodSentToNL",type="TripleChoice",nullable=true)
     */
    private $bloodSentToNL;

    /**
     * @var string $bloodNLId
     * @ORM\Column(name="bloodNLId",type="string",nullable=true)
     */
    private $bloodNLId;

    /**
     * @var \DateTime $bloodDateSentToNL
     * @ORM\Column(name="bloodDateSentToNL",type="date",nullable=true)
     */
    private $bloodDateSentToNL;

    /**
     * @var \DateTime $bloodNLDateTime
     * @ORM\Column(name="bloodNLDateTime",type="date",nullable=true)
     */
    private $bloodNLDateTime;

    /**
     * @var TripleChoice $bloodSentToRRL
     * @ORM\Column(name="bloodSentToRRL",type="TripleChoice",nullable=true)
     */
    private $bloodSentToRRL;

    /**
     * @var string $bloodRRLId
     * @ORM\Column(name="bloodRRLId",type="string",nullable=true)
     */
    private $bloodRRLId;

    /**
     * @var \DateTime $bloodDateSentToRRL
     * @ORM\Column(name="bloodDateSentToRRL",type="date",nullable=true)
     */
    private $bloodDateSentToRRL;

    /**
     * @var \DateTime $bloodRRLDateTime
     * @ORM\Column(name="bloodRRLDateTime",type="date",nullable=true)
     */
    private $bloodRRLDateTime;

    /**
     * @var TripleChoice $bloodCultDone
     * @ORM\Column(name="bloodCultDone",type="TripleChoice",nullable=true)
     */
    private $bloodCultDone;

    /**
     * @var TripleChoice $bloodGramDone
     * @ORM\Column(name="bloodGramDone",type="TripleChoice",nullable=true)
     */
    private $bloodGramDone;

    /**
     * @var TripleChoice $bloodPcrDone
     * @ORM\Column(name="bloodPcrDone",type="TripleChoice",nullable=true)
     */
    private $bloodPcrDone;

    /**
     * @var CultureResult
     * @ORM\Column(name="bloodCultResult",type="CultureResult",nullable=true)
     */
    private $bloodCultResult;

    /**
     * @var string
     * @ORM\Column(name="bloodCultOther",type="string",nullable=true)
     */
    private $bloodCultOther;

    /**
     * @var GramStain
     * @ORM\Column(name="bloodGramResult",type="GramStain",nullable=true)
     */
    private $bloodGramResult;

    /**
     * @var GramStainOrganism $bloodGramResultOrganism
     * @ORM\Column(name="bloodGramResultOrganism",type="GramStainOrganism",nullable=true)
     */
    private $bloodGramResultOrganism;

    /**
     * @var string $bloodGramOther
     * @ORM\Column(name="bloodGramOther",type="string",nullable=true)
     */
    private $bloodGramOther;

    /**
     * @var PCRResult
     * @ORM\Column(name="bloodPcrResult",type="PCRResult",nullable=true)
     */
    private $bloodPcrResult;

    /**
     * @var string $bloodPcrOther
     * @ORM\Column(name="bloodPcrOther",type="string",nullable=true)
     */
    private $bloodPcrOther;


//----------------------------------------
// Other Fluids

    /**
     * @var string $otherSiteId
     * @ORM\Column(name="otherSiteId",type="string",nullable=true)
     */
    private $otherSiteId;

    /**
     * @var string $otherNLId
     * @ORM\Column(name="otherNLId",type="string",nullable=true)
     */
    private $otherNLId;

    /**
     * @var string $otherRRLId
     * @ORM\Column(name="otherRRLId",type="string",nullable=true)
     */
    private $otherRRLId;

    /**
     * @var \DateTime $otherSiteDateTime
     * @ORM\Column(name="otherSiteDateTime",type="date",nullable=true)
     * @Assert\DateTime
     */
    private $otherSiteDateTime;

    /**
     * @var TripleChoice $otherSentToNL
     * @ORM\Column(name="otherSentToNL",type="TripleChoice",nullable=true)
     */
    private $otherSentToNL;

    /**
     * @var \DateTime $otherDateSentToNL
     * @ORM\Column(name="otherDateSentToNL",type="date",nullable=true)
     * @Assert\Date
     */
    private $otherDateSentToNL;

    /**
     * @var \DateTime $otherNLDateTime
     * @ORM\Column(name="otherNLDateTime",type="date",nullable=true)
     */
    private $otherNLDateTime;

    /**
     * @var TripleChoice $otherSentToRRL
     * @ORM\Column(name="otherSentToRRL",type="TripleChoice",nullable=true)
     */
    private $otherSentToRRL;

    /**
     * @var \DateTime $otherDateSentToRRL
     * @ORM\Column(name="otherDateSentToRRL",type="date",nullable=true)
     * @Assert\Date
     */
    private $otherDateSentToRRL;

    /**
     * @var \DateTime $otherRRLDateTime
     * @ORM\Column(name="otherRRLDateTime",type="date",nullable=true)
     */
    private $otherRRLDateTime;

    /**
     * @var TripleChoice $otherCultDone
     * @ORM\Column(name="otherCultDone",type="TripleChoice",nullable=true)
     */
    private $otherCultDone;

    /**
     * @var CultureResult
     * @ORM\Column(name="otherCultResult",type="CultureResult",nullable=true)
     */
    private $otherCultResult;

    /**
     * @var string
     * @ORM\Column(name="otherCultOther",type="string",nullable=true)
     */
    private $otherCultOther;

//----------------------------------------
// Serotype Results
    /**
     * @var PathogenIdentifier
     * @ORM\Column(name="pathogenIdentifierMethod",type="PathogenIdentifier",nullable=true)
     */
    private $pathogenIdentifierMethod;

    /**
     * @var string
     * @ORM\Column(name="pathogenIdentifierOther", type="string",nullable=true)
     */
    private $pathogenIdentifierOther;

    /**
     * @var SerotypeIdentifier
     * @ORM\Column(name="serotypeIdentifier",type="SerotypeIdentifier",nullable=true)
     */
    private $serotypeIdentifier;

    /**
     * @var string
     * @ORM\Column(name="serotypeIdentifierOther",type="string",nullable=true)
     */
    private $serotypeIdentifierOther;

    /**
     * @var double
     * @ORM\Column(name="lytA",type="decimal",precision=3, scale=1,nullable=true)
     */
    private $lytA;

    /**
     * @var double
     * @ORM\Column(name="sodC",type="decimal",precision=3, scale=1,nullable=true)
     */
    private $sodC;

    /**
     * @var double
     * @ORM\Column(name="hpd",type="decimal",precision=3, scale=1,nullable=true)
     */
    private $hpd;

    /**
     * @var double
     * @ORM\Column(name="rNaseP",type="decimal",precision=3, scale=1,nullable=true)
     */
    private $rNaseP;

    /**
     * @var double
     * @ORM\Column(name="spnSerotype",type="SpnSerotype",nullable=true)
     */
    private $spnSerotype;

    /**
     * @var string $spnSerotypeOther
     * @ORM\Column(name="spnSerotypeOther",type="string",nullable=true)
     */
    private $spnSerotypeOther;

    /**
     * @var double
     * @ORM\Column(name="hiSerotype",type="HiSerotype",nullable=true)
     */
    private $hiSerotype;

    /**
     * @var string $hiSerotypeOther
     * @ORM\Column(name="hiSerotypeOther",type="string",nullable=true)
     */
    private $hiSerotypeOther;

    /**
     * @var double
     * @ORM\Column(name="nmSerogroup",type="NmSerogroup",nullable=true)
     */
    private $nmSerogroup;

    /**
     * @var string $nmSerogroupOther
     * @ORM\Column(name="nmSerogroupOther",type="string",nullable=true)
     */
    private $nmSerogroupOther;

// Other

    public function __construct($case = null)
    {
        if($case instanceof Meningitis)
            $this->case = $case;

        $this->updatedAt = new \DateTime();
        $this->status    = new CaseStatus(CaseStatus::OPEN);

        return $this;
    }

    public function getCsfSiteId()
    {
        return $this->csfSiteId;
    }

    public function getCsfNLId()
    {
        return $this->csfNLId;
    }

    public function getCsfRRLId()
    {
        return $this->csfRRLId;
    }

    public function getCsfSiteDateTime()
    {
        return $this->csfSiteDateTime;
    }

    public function getCsfNLDateTime()
    {
        return $this->csfNLDateTime;
    }

    public function getCsfRRLDateTime()
    {
        return $this->csfRRLDateTime;
    }

    public function getCsfWcc()
    {
        return $this->csfWcc;
    }

    public function getCsfGlucose()
    {
        return $this->csfGlucose;
    }

    public function getCsfProtein()
    {
        return $this->csfProtein;
    }

    public function getCsfCultDone()
    {
        return $this->csfCultDone;
    }

    public function getCsfGramDone()
    {
        return $this->csfGramDone;
    }

    public function getCsfBinaxDone()
    {
        return $this->csfBinaxDone;
    }

    public function getCsfLatDone()
    {
        return $this->csfLatDone;
    }

    public function getCsfPcrDone()
    {
        return $this->csfPcrDone;
    }

    public function getCsfCultResult()
    {
        return $this->csfCultResult;
    }

    public function getCsfCultOther()
    {
        return $this->csfCultOther;
    }

    public function getCsfGramResult()
    {
        return $this->csfGramResult;
    }

    public function getCsfGramResultOrganism()
    {
        return $this->csfGramResultOrganism;
    }

    public function getCsfGramOther()
    {
        return $this->csfGramOther;
    }

    public function getCsfBinaxResult()
    {
        return $this->csfBinaxResult;
    }

    public function getCsfLatResult()
    {
        return $this->csfLatResult;
    }

    public function getCsfLatOther()
    {
        return $this->csfLatOther;
    }

    public function getCsfPcrResult()
    {
        return $this->csfPcrResult;
    }

    public function getCsfPcrOther()
    {
        return $this->csfPcrOther;
    }

    public function getCsfSentToRRL()
    {
        return $this->csfSentToRRL;
    }

    public function getCsfDateSentToRRL()
    {
        return $this->csfDateSentToRRL;
    }

    public function getCsfSentToNL()
    {
        return $this->csfSentToNL;
    }

    public function getCsfDateSentToNL()
    {
        return $this->csfDateSentToNL;
    }

    public function getCsfStore()
    {
        return $this->csfStore;
    }

    public function getCsfIsolStore()
    {
        return $this->csfIsolStore;
    }

    public function getCsfVolume()
    {
        return $this->csfVolume;
    }

    public function getCsfSiteDNAExtractionDate()
    {
        return $this->csfSiteDNAExtractionDate;
    }

    public function getCsfSiteDNAVolume()
    {
        return $this->csfSiteDNAVolume;
    }

    public function getCsfNLDNAExtractionDate()
    {
        return $this->csfNLDNAExtractionDate;
    }

    public function getCsfNLDNAVolume()
    {
        return $this->csfNLDNAVolume;
    }

    public function getCsfRRLDNAExtractionDate()
    {
        return $this->csfRRLDNAExtractionDate;
    }

    public function getCsfRRLDNAVolume()
    {
        return $this->csfRRLDNAVolume;
    }

    public function getBloodSiteId()
    {
        return $this->bloodSiteId;
    }

    public function getBloodSiteDateTime()
    {
        return $this->bloodSiteDateTime;
    }

    public function getBloodSentToNL()
    {
        return $this->bloodSentToNL;
    }

    public function getBloodNLId()
    {
        return $this->bloodNLId;
    }

    public function getBloodDateSentToNL()
    {
        return $this->bloodDateSentToNL;
    }

    public function getBloodNLDateTime()
    {
        return $this->bloodNLDateTime;
    }

    public function getBloodSentToRRL()
    {
        return $this->bloodSentToRRL;
    }

    public function getBloodRRLId()
    {
        return $this->bloodRRLId;
    }

    public function getBloodDateSentToRRL()
    {
        return $this->bloodDateSentToRRL;
    }

    public function getBloodRRLDateTime()
    {
        return $this->bloodRRLDateTime;
    }

    public function getBloodCultDone()
    {
        return $this->bloodCultDone;
    }

    public function getBloodGramDone()
    {
        return $this->bloodGramDone;
    }

    public function getBloodPcrDone()
    {
        return $this->bloodPcrDone;
    }

    public function getBloodCultResult()
    {
        return $this->bloodCultResult;
    }

    public function getBloodCultOther()
    {
        return $this->bloodCultOther;
    }

    public function getBloodGramResult()
    {
        return $this->bloodGramResult;
    }

    public function getBloodGramResultOrganism()
    {
        return $this->bloodGramResultOrganism;
    }

    public function getBloodGramOther()
    {
        return $this->bloodGramOther;
    }

    public function getBloodPcrResult()
    {
        return $this->bloodPcrResult;
    }

    public function getBloodPcrOther()
    {
        return $this->bloodPcrOther;
    }

    public function getOtherSiteId()
    {
        return $this->otherSiteId;
    }

    public function getOtherNLId()
    {
        return $this->otherNLId;
    }

    public function getOtherRRLId()
    {
        return $this->otherRRLId;
    }

    public function getOtherSiteDateTime()
    {
        return $this->otherSiteDateTime;
    }

    public function getOtherSentToNL()
    {
        return $this->otherSentToNL;
    }

    public function getOtherDateSentToNL()
    {
        return $this->otherDateSentToNL;
    }

    public function getOtherNLDateTime()
    {
        return $this->otherNLDateTime;
    }

    public function getOtherSentToRRL()
    {
        return $this->otherSentToRRL;
    }

    public function getOtherDateSentToRRL()
    {
        return $this->otherDateSentToRRL;
    }

    public function getOtherRRLDateTime()
    {
        return $this->otherRRLDateTime;
    }

    public function getOtherCultDone()
    {
        return $this->otherCultDone;
    }

    public function getOtherCultResult()
    {
        return $this->otherCultResult;
    }

    public function getOtherCultOther()
    {
        return $this->otherCultOther;
    }

    public function getPathogenIdentifierMethod()
    {
        return $this->pathogenIdentifierMethod;
    }

    public function getPathogenIdentifierOther()
    {
        return $this->pathogenIdentifierOther;
    }

    public function getSerotypeIdentifier()
    {
        return $this->serotypeIdentifier;
    }

    public function getSerotypeIdentifierOther()
    {
        return $this->serotypeIdentifierOther;
    }

    public function getLytA()
    {
        return $this->lytA;
    }

    public function getSodC()
    {
        return $this->sodC;
    }

    public function getHpd()
    {
        return $this->hpd;
    }

    public function getRNaseP()
    {
        return $this->rNaseP;
    }

    public function getSpnSerotype()
    {
        return $this->spnSerotype;
    }

    public function getSpnSerotypeOther()
    {
        return $this->spnSerotypeOther;
    }

    public function getHiSerotype()
    {
        return $this->hiSerotype;
    }

    public function getHiSerotypeOther()
    {
        return $this->hiSerotypeOther;
    }

    public function getNmSerogroup()
    {
        return $this->nmSerogroup;
    }

    public function getNmSerogroupOther()
    {
        return $this->nmSerogroupOther;
    }

    public function setCsfSiteId($csfSiteId)
    {
        $this->csfSiteId = $csfSiteId;
        return $this;
    }

    public function setCsfNLId($csfNLId)
    {
        $this->csfNLId = $csfNLId;
        return $this;
    }

    public function setCsfRRLId($csfRRLId)
    {
        $this->csfRRLId = $csfRRLId;
        return $this;
    }

    public function setCsfSiteDateTime($csfSiteDateTime)
    {
        $this->csfSiteDateTime = $csfSiteDateTime;
        return $this;
    }

    public function setCsfNLDateTime($csfNLDateTime)
    {
        $this->csfNLDateTime = $csfNLDateTime;
        return $this;
    }

    public function setCsfRRLDateTime($csfRRLDateTime)
    {
        $this->csfRRLDateTime = $csfRRLDateTime;
        return $this;
    }

    public function setCsfWcc($csfWcc)
    {
        $this->csfWcc = $csfWcc;
        return $this;
    }

    public function setCsfGlucose($csfGlucose)
    {
        $this->csfGlucose = $csfGlucose;
        return $this;
    }

    public function setCsfProtein($csfProtein)
    {
        $this->csfProtein = $csfProtein;
        return $this;
    }

    public function setCsfCultDone(TripleChoice $csfCultDone)
    {
        $this->csfCultDone = $csfCultDone;
        return $this;
    }

    public function setCsfGramDone(TripleChoice $csfGramDone)
    {
        $this->csfGramDone = $csfGramDone;
        return $this;
    }

    public function setCsfBinaxDone(TripleChoice $csfBinaxDone)
    {
        $this->csfBinaxDone = $csfBinaxDone;
        return $this;
    }

    public function setCsfLatDone(TripleChoice $csfLatDone)
    {
        $this->csfLatDone = $csfLatDone;
        return $this;
    }

    public function setCsfPcrDone(TripleChoice $csfPcrDone)
    {
        $this->csfPcrDone = $csfPcrDone;
        return $this;
    }

    public function setCsfCultResult(CultureResult $csfCultResult)
    {
        $this->csfCultResult = $csfCultResult;
        return $this;
    }

    public function setCsfCultOther($csfCultOther)
    {
        $this->csfCultOther = $csfCultOther;
        return $this;
    }

    public function setCsfGramResult(GramStain $csfGramResult)
    {
        $this->csfGramResult = $csfGramResult;
        return $this;
    }

    public function setCsfGramResultOrganism(GramStainOrganism $csfGramResultOrganism)
    {
        $this->csfGramResultOrganism = $csfGramResultOrganism;
        return $this;
    }

    public function setCsfGramOther($csfGramOther)
    {
        $this->csfGramOther = $csfGramOther;
        return $this;
    }

    public function setCsfBinaxResult(BinaxResult $csfBinaxResult)
    {
        $this->csfBinaxResult = $csfBinaxResult;
        return $this;
    }

    public function setCsfLatResult(LatResult $csfLatResult)
    {
        $this->csfLatResult = $csfLatResult;
        return $this;
    }

    public function setCsfLatOther($csfLatOther)
    {
        $this->csfLatOther = $csfLatOther;
        return $this;
    }

    public function setCsfPcrResult(PCRResult $csfPcrResult)
    {
        $this->csfPcrResult = $csfPcrResult;
        return $this;
    }

    public function setCsfPcrOther($csfPcrOther)
    {
        $this->csfPcrOther = $csfPcrOther;
        return $this;
    }

    public function setCsfSentToRRL($csfSentToRRL)
    {
        $this->csfSentToRRL = $csfSentToRRL;
        return $this;
    }

    public function setCsfDateSentToRRL($csfDateSentToRRL)
    {
        $this->csfDateSentToRRL = $csfDateSentToRRL;
        return $this;
    }

    public function setCsfSentToNL($csfSentToNL)
    {
        $this->csfSentToNL = $csfSentToNL;
        return $this;
    }

    public function setCsfDateSentToNL($csfDateSentToNL)
    {
        $this->csfDateSentToNL = $csfDateSentToNL;
        return $this;
    }

    public function setCsfStore(TripleChoice $csfStore)
    {
        $this->csfStore = $csfStore;
        return $this;
    }

    public function setCsfIsolStore(TripleChoice $csfIsolStore)
    {
        $this->csfIsolStore = $csfIsolStore;
        return $this;
    }

    public function setCsfVolume(Volume $csfVolume)
    {
        $this->csfVolume = $csfVolume;
        return $this;
    }

    public function setCsfSiteDNAExtractionDate($csfSiteDNAExtractionDate)
    {
        $this->csfSiteDNAExtractionDate = $csfSiteDNAExtractionDate;
        return $this;
    }

    public function setCsfSiteDNAVolume($csfSiteDNAVolume)
    {
        $this->csfSiteDNAVolume = $csfSiteDNAVolume;
        return $this;
    }

    public function setCsfNLDNAExtractionDate($csfNLDNAExtractionDate)
    {
        $this->csfNLDNAExtractionDate = $csfNLDNAExtractionDate;
        return $this;
    }

    public function setCsfNLDNAVolume($csfNLDNAVolume)
    {
        $this->csfNLDNAVolume = $csfNLDNAVolume;
        return $this;
    }

    public function setCsfRRLDNAExtractionDate($csfRRLDNAExtractionDate)
    {
        $this->csfRRLDNAExtractionDate = $csfRRLDNAExtractionDate;
        return $this;
    }

    public function setCsfRRLDNAVolume($csfRRLDNAVolume)
    {
        $this->csfRRLDNAVolume = $csfRRLDNAVolume;
        return $this;
    }

    public function setBloodSiteId($bloodSiteId)
    {
        $this->bloodSiteId = $bloodSiteId;
        return $this;
    }

    public function setBloodSiteDateTime($bloodSiteDateTime)
    {
        $this->bloodSiteDateTime = $bloodSiteDateTime;
        return $this;
    }

    public function setBloodSentToNL($bloodSentToNL)
    {
        $this->bloodSentToNL = $bloodSentToNL;
        return $this;
    }

    public function setBloodNLId($bloodNLId)
    {
        $this->bloodNLId = $bloodNLId;
        return $this;
    }

    public function setBloodDateSentToNL($bloodDateSentToNL)
    {
        $this->bloodDateSentToNL = $bloodDateSentToNL;
        return $this;
    }

    public function setBloodNLDateTime($bloodNLDateTime)
    {
        $this->bloodNLDateTime = $bloodNLDateTime;
        return $this;
    }

    public function setBloodSentToRRL($bloodSentToRRL)
    {
        $this->bloodSentToRRL = $bloodSentToRRL;
        return $this;
    }

    public function setBloodRRLId($bloodRRLId)
    {
        $this->bloodRRLId = $bloodRRLId;
        return $this;
    }

    public function setBloodDateSentToRRL($bloodDateSentToRRL)
    {
        $this->bloodDateSentToRRL = $bloodDateSentToRRL;
        return $this;
    }

    public function setBloodRRLDateTime($bloodRRLDateTime)
    {
        $this->bloodRRLDateTime = $bloodRRLDateTime;
        return $this;
    }

    public function setBloodCultDone(TripleChoice $bloodCultDone)
    {
        $this->bloodCultDone = $bloodCultDone;
        return $this;
    }

    public function setBloodGramDone(TripleChoice $bloodGramDone)
    {
        $this->bloodGramDone = $bloodGramDone;
        return $this;
    }

    public function setBloodPcrDone(TripleChoice $bloodPcrDone)
    {
        $this->bloodPcrDone = $bloodPcrDone;
        return $this;
    }

    public function setBloodCultResult(CultureResult $bloodCultResult)
    {
        $this->bloodCultResult = $bloodCultResult;
        return $this;
    }

    public function setBloodCultOther($bloodCultOther)
    {
        $this->bloodCultOther = $bloodCultOther;
        return $this;
    }

    public function setBloodGramResult(GramStain $bloodGramResult)
    {
        $this->bloodGramResult = $bloodGramResult;
        return $this;
    }

    public function setBloodGramResultOrganism(GramStainOrganism $bloodGramResultOrganism)
    {
        $this->bloodGramResultOrganism = $bloodGramResultOrganism;
        return $this;
    }

    public function setBloodGramOther($bloodGramOther)
    {
        $this->bloodGramOther = $bloodGramOther;
        return $this;
    }

    public function setBloodPcrResult(PCRResult $bloodPcrResult)
    {
        $this->bloodPcrResult = $bloodPcrResult;
        return $this;
    }

    public function setBloodPcrOther($bloodPcrOther)
    {
        $this->bloodPcrOther = $bloodPcrOther;
        return $this;
    }

    public function setOtherSiteId($otherSiteId)
    {
        $this->otherSiteId = $otherSiteId;
        return $this;
    }

    public function setOtherNLId($otherNLId)
    {
        $this->otherNLId = $otherNLId;
        return $this;
    }

    public function setOtherRRLId($otherRRLId)
    {
        $this->otherRRLId = $otherRRLId;
        return $this;
    }

    public function setOtherSiteDateTime($otherSiteDateTime)
    {
        $this->otherSiteDateTime = $otherSiteDateTime;
        return $this;
    }

    public function setOtherSentToNL($otherSentToNL)
    {
        $this->otherSentToNL = $otherSentToNL;
        return $this;
    }

    public function setOtherDateSentToNL($otherDateSentToNL)
    {
        $this->otherDateSentToNL = $otherDateSentToNL;
        return $this;
    }

    public function setOtherNLDateTime($otherNLDateTime)
    {
        $this->otherNLDateTime = $otherNLDateTime;
        return $this;
    }

    public function setOtherSentToRRL($otherSentToRRL)
    {
        $this->otherSentToRRL = $otherSentToRRL;
        return $this;
    }

    public function setOtherDateSentToRRL($otherDateSentToRRL)
    {
        $this->otherDateSentToRRL = $otherDateSentToRRL;
        return $this;
    }

    public function setOtherRRLDateTime($otherRRLDateTime)
    {
        $this->otherRRLDateTime = $otherRRLDateTime;
        return $this;
    }

    public function setOtherCultDone(TripleChoice $otherCultDone)
    {
        $this->otherCultDone = $otherCultDone;
        return $this;
    }

    public function setOtherCultResult(CultureResult $otherCultResult)
    {
        $this->otherCultResult = $otherCultResult;
        return $this;
    }

    public function setOtherCultOther($otherCultOther)
    {
        $this->otherCultOther = $otherCultOther;
        return $this;
    }

    public function setPathogenIdentifierMethod(PathogenIdentifier $pathogenIdentifierMethod)
    {
        $this->pathogenIdentifierMethod = $pathogenIdentifierMethod;
        return $this;
    }

    public function setPathogenIdentifierOther($pathogenIdentifierOther)
    {
        $this->pathogenIdentifierOther = $pathogenIdentifierOther;
        return $this;
    }

    public function setSerotypeIdentifier(SerotypeIdentifier $serotypeIdentifier)
    {
        $this->serotypeIdentifier = $serotypeIdentifier;
        return $this;
    }

    public function setSerotypeIdentifierOther($serotypeIdentifierOther)
    {
        $this->serotypeIdentifierOther = $serotypeIdentifierOther;
        return $this;
    }

    public function setLytA($lytA)
    {
        $this->lytA = $lytA;
        return $this;
    }

    public function setSodC($sodC)
    {
        $this->sodC = $sodC;
        return $this;
    }

    public function setHpd($hpd)
    {
        $this->hpd = $hpd;
        return $this;
    }

    public function setRNaseP($rNaseP)
    {
        $this->rNaseP = $rNaseP;
        return $this;
    }

    public function setSpnSerotype($spnSerotype)
    {
        $this->spnSerotype = $spnSerotype;
        return $this;
    }

    public function setSpnSerotypeOther($spnSerotypeOther)
    {
        $this->spnSerotypeOther = $spnSerotypeOther;
        return $this;
    }

    public function setHiSerotype($hiSerotype)
    {
        $this->hiSerotype = $hiSerotype;
        return $this;
    }

    public function setHiSerotypeOther($hiSerotypeOther)
    {
        $this->hiSerotypeOther = $hiSerotypeOther;
        return $this;
    }

    public function setNmSerogroup($nmSerogroup)
    {
        $this->nmSerogroup = $nmSerogroup;
        return $this;
    }

    public function setNmSerogroupOther($nmSerogroupOther)
    {
        $this->nmSerogroupOther = $nmSerogroupOther;
        return $this;
    }

    public function isComplete()
    {
        return $this->status->equal(CaseStatus::COMPLETE);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->_calculateStatus();

        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->_calculateStatus();

        $this->updatedAt = new \DateTime();
    }

    public function validate(ExecutionContextInterface $context)
    {
        if($this->csfCultDone && $this->csfCultDone->equal(TripleChoice::YES) && !$this->csfCultResult)
            $context->addViolationAt('csfCultDone',"form.validation.meningitis-sitelab-csfCult-was-done-without-result");

        if($this->csfCultDone && $this->csfCultDone->equal(TripleChoice::YES) && $this->csfCultResult && $this->csfCultResult->equal(CultureResult::OTHER) && empty($this->csfCultOther))
            $context->addViolationAt('csfCultDone',"form.validation.meningitis-sitelab-csfCult-was-done-without-result-other");

        if($this->csfBinaxDone && $this->csfBinaxDone->equal(TripleChoice::YES) && !$this->csfBinaxResult)
            $context->addViolationAt('csfBinaxDone',"form.validation.meningitis-sitelab-csfBinax-was-done-without-result");

        if($this->csfLatDone && $this->csfLatDone->equal(TripleChoice::YES) && !$this->csfLatResult)
            $context->addViolationAt('csfLatDone',"form.validation.meningitis-sitelab-csfLat-was-done-without-result");

        if($this->csfLatDone && $this->csfLatDone->equal(TripleChoice::YES) && $this->csfLatResult && $this->csfLatResult->equal(LatResult::OTHER) && empty($this->csfLatOther))
            $context->addViolationAt('csfLatDone',"form.validation.meningitis-sitelab-csfLat-was-done-without-result-other");

        if($this->csfPcrDone && $this->csfPcrDone->equal(TripleChoice::YES) && !$this->csfPcrResult)
            $context->addViolationAt('csfPcrDone',"form.validation.meningitis-sitelab-csfPcr-was-done-without-result");

        if($this->csfPcrDone && $this->csfPcrDone->equal(TripleChoice::YES) && $this->csfPcrResult && $this->csfPcrResult->equal(PCRResult::OTHER) && empty($this->csfPcrOther))
            $context->addViolationAt('csfPcrDone',"form.validation.meningitis-sitelab-csfPcr-was-done-without-result");

        if($this->spnSerotype && $this->spnSerotype->equal(SpnSerotype::OTHER) && (!$this->spnSerotypeOther || empty($this->spnSerotypeOther)))
            $context->addViolationAt('spnSerotype',"form.validation.meningitis-sitelab-spnSerotype-other-without-data");

        if($this->hiSerotype && $this->hiSerotype->equal(HiSerotype::OTHER) && (!$this->hiSerotypeOther || empty($this->hiSerotypeOther)))
            $context->addViolationAt('hiSerotype',"form.validation.meningitis-sitelab-hiSerotype-other-without-data");

        if($this->nmSerogroup && $this->nmSerogroup->equal(NmSerogroup::OTHER) && (!$this->nmSerogroupOther || empty($this->nmSerogroupOther)))
            $context->addViolationAt('nmSerogroup',"form.validation.meningitis-sitelab-nmSerogroup-other-without-data");

        if($this->bloodCultDone && $this->bloodCultDone->equal(TripleChoice::YES) && !$this->bloodCultResult)
            $context->addViolationAt('csfCultDone',"form.validation.meningitis-sitelab-bloodCult-was-done-without-result");

        if($this->bloodCultDone && $this->bloodCultDone->equal(TripleChoice::YES) && $this->bloodCultResult && $this->bloodCultResult->equal(CultureResult::OTHER) && empty($this->bloodCultOther))
            $context->addViolationAt('bloodCultDone',"form.validation.meningitis-sitelab-bloodCult-was-done-without-result");

        if($this->otherCultDone && $this->otherCultDone->equal(TripleChoice::YES) && !$this->otherCultResult)
            $context->addViolationAt('csfCultDone',"form.validation.meningitis-sitelab-otherCult-was-done-without-result");

        if($this->otherCultDone && $this->otherCultDone->equal(TripleChoice::YES) && $this->otherCultResult && $this->otherCultResult->equal(CultureResult::OTHER) && empty($this->otherCultOther))
            $context->addViolationAt('otherCultDone',"form.validation.meningitis-sitelab-otherCult-was-done-without-result");
    }

    private function _calculateStatus()
    {
        // Don't adjust cancelled or deleted records
        if($this->status->getValue() >= CaseStatus::CANCELLED)
            return;

        if($this->getIncompleteField())
            $this->status->setValue(CaseStatus::OPEN);
        else
            $this->status->setValue(CaseStatus::COMPLETE);

        return;
    }

    public function getIncompleteField()
    {
        foreach($this->getMinimumRequiredFields() as $field)
        {
            if(is_null($this->$field) || empty($this->$field) || ($this->$field instanceof ArrayChoice && $this->$field->equal(-1)) )
                return $field;
        }

        //Additional Tests as needed (result=other && other fields etc)

        return null;
    }

// TODO this needs to be updated with the new fields as this was copyied from the old site lab and not all fields have stayed the same
    public function getMinimumRequiredFields()
    {
        return array(
                    'csfLabDateTime',
                    'csfWcc',
                    'csfGlucose',
                    'csfProtein',
                    'csfCultDone',
                    'csfGramDone',
                    'csfBinaxDone',
                    'csfLatDone',
                    'csfPcrDone',
                    'rrlCsfDate',
                    'rrlIsolDate',
                    'csfStore',
                    'isolStore',
                    'bloodCultDone',
                    'bloodGramDone',
                    'bloodPcrDone',
                    'otherCultDone',
                    'spnSerotype',
                    'hiSerotype',
                    'nmSerogroup',
                    );
    }
}