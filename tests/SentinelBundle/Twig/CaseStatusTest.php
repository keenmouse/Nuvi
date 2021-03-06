<?php

namespace NS\SentinelBundle\Tests\Twig;

use NS\SentinelBundle\Twig\CaseStatus;
use NS\SentinelBundle\Entity\IBD;
use NS\SentinelBundle\Form\Types\CaseStatus as FormCaseStatus;
use NS\SentinelBundle\Entity\IBD\SiteLab;
use NS\SentinelBundle\Entity\IBD\ReferenceLab;
use NS\SentinelBundle\Entity\IBD\NationalLab;

/**
 * Description of CaseStatusTest
 *
 * @author gnat
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CaseStatusTest extends \PHPUnit_Framework_TestCase
{

    public function testIncompleteCase()
    {
        $case   = new IBD();
        $status = new CaseStatus();
        $label  = $status->getLabel($case, 'nothing');

        $this->assertContains('label-warning', $label, "Incomplete case has warning label");
    }

    public function testCompleteCase()
    {
        $case   = new IBD();
        $case->setStatus(new FormCaseStatus(FormCaseStatus::COMPLETE));
        $status = new CaseStatus();
        $label  = $status->getLabel($case, 'nothing');

        $this->assertContains('label-success', $label, "Complete case has success label");
    }

    public function testErrorCase()
    {
        $status = new CaseStatus();

        $case = new IBD();
        $lab  = new SiteLab();
        $lab->setNlBrothSent(true);
        $case->setSiteLab($lab);

        $label = $status->getLabel($case, 'nothing');

        $this->assertContains('label-danger', $label, "Case with data sent to national lab but without a national lab has danger label");

        $case = new IBD();
        $lab  = new SiteLab();
        $lab->setRlIsolCsfSent(true);
        $case->setSiteLab($lab);

        $label = $status->getLabel($case, 'nothing');

        $this->assertContains('label-danger', $label, "Case with data sent to reference lab but without a reference lab has danger label");
    }

    public function testLabIncomplete()
    {
        $status = new CaseStatus();

        $case = new IBD();
        $lab  = new SiteLab();
        $case->setSiteLab($lab);

        $label = $status->getLabLabel($case, 'nothing');

        $this->assertContains('label-warning', $label, "Incomplete lab has warning label");
    }

    public function testLabComplete()
    {
        $status = new CaseStatus();

        $case = new IBD();
        $lab  = new SiteLab();
        $lab->setStatus(new FormCaseStatus(FormCaseStatus::COMPLETE));
        $case->setSiteLab($lab);

        $label = $status->getLabLabel($case, 'nothing');

        $this->assertContains('label-success', $label, "Complete case has success label");
    }

    public function testLabErrorCase()
    {
        $status = new CaseStatus();

        $case = new IBD();

        $label = $status->getLabLabel($case, 'nothing');

        $this->assertContains('label-danger', $label, "Case without lab record has danger label");
    }

    public function testNoExternalLabs()
    {
        $status = new CaseStatus();
        $case   = new IBD();

        $l1 = $status->getRRLLabel($case, 'nothing');
        $this->assertNull($l1, "Case without RRL returns null");

        $l2 = $status->getNLLabel($case, 'nothing');
        $this->assertNull($l2, "Case without RRL returns null");
    }

    public function testExternalLabIncomplete()
    {
        $status = new CaseStatus();

        //---------------------------
        // RRL
        $case1 = new IBD();
        $lab   = new SiteLab();
        $lab->setRlCsfSent(true);
        $case1->setSiteLab($lab);

        $rrl = new ReferenceLab();
        $case1->setReferenceLab($rrl);

        $l1 = $status->getRRLLabel($case1, 'nothing');

        //---------------------------
        // NL
        $case2 = new IBD();
        $lab   = new SiteLab();
        $lab->setNlBrothSent(true);
        $case2->setSiteLab($lab);
        $nl    = new NationalLab();
        $case2->setNationalLab($nl);

        $l2 = $status->getNLLabel($case2, 'nothing');

        $this->assertContains('label-warning', $l1, "Incomplete RRL lab has warning label");
        $this->assertContains('label-warning', $l2, "Incomplete RRL lab has warning label");
    }

    public function testExternalLabComplete()
    {
        $status = new CaseStatus();

        //----------------------
        // RRL test
        $case1 = new IBD();
        $lab   = new SiteLab();
        $lab->setRlCsfSent(true);
        $case1->setSiteLab($lab);

        $rrl = new ReferenceLab();
        $rrl->setStatus(new FormCaseStatus(FormCaseStatus::COMPLETE));

        $case1->setReferenceLab($rrl);

        $l1 = $status->getRRLLabel($case1, 'nothing');

        //----------------------
        // NL test
        $case2 = new IBD();
        $lab   = new SiteLab();
        $lab->setNlBrothSent(true);
        $case2->setSiteLab($lab);

        $nl = new NationalLab();
        $nl->setStatus(new FormCaseStatus(FormCaseStatus::COMPLETE));

        $case2->setNationalLab($nl);

        $l2 = $status->getNLLabel($case2, 'nothing');


        $this->assertTrue($rrl->isComplete(), 'rrl is complete');
        $this->assertContains('label-success', $l1, "Complete RRL lab has success label");

        $this->assertTrue($nl->isComplete(), 'nl is complete');
        $this->assertContains('label-success', $l2, "Complete NL lab has success label");
    }

    public function testExternalLabErrorCase()
    {
        $status = new CaseStatus();

        //----------------------
        // RRL - sent to lab but no lab data
        $case1 = new IBD();
        $lab   = new SiteLab();
        $lab->setRlBrothSent(true);
        $case1->setSiteLab($lab);

        $l1  = $status->getRRLLabel($case1, 'nothing');
        $l11 = $status->getLabel($case1, 'nothing');

        //----------------------
        // RRL - lab data but no sent to lab
        $case2 = new IBD();
        $rrl   = new ReferenceLab();
        $case2->setReferenceLab($rrl);

        $l2  = $status->getRRLLabel($case2, 'nothing');
        $l21 = $status->getLabel($case2, 'nothing');

        //----------------------
        // NL - sent to lab but no lab data
        $case3 = new IBD();
        $lab   = new SiteLab();
        $lab->setNlCsfSent(true);
        $case3->setSiteLab($lab);

        $l3  = $status->getNLLabel($case3, 'nothing');
        $l31 = $status->getLabel($case3, 'nothing');

        //----------------------
        // NL - lab data but no sent to lab
        $case4 = new IBD();
        $nl    = new NationalLab();
        $case4->setNationalLab($nl);

        $l4  = $status->getNLLabel($case4, 'nothing');
        $l41 = $status->getLabel($case4, 'nothing');

        $this->assertContains('label-danger', $l1, "RRL - sent to lab but no lab data");
        $this->assertContains('label-danger', $l11, "Case Error - RRL - sent to lab but no lab data");
        $this->assertContains('label-danger', $l2, "RRL - lab data but no sent to lab");
        $this->assertContains('label-danger', $l21, "Case Error - RRL - lab data but no sent to lab");
        $this->assertContains('label-danger', $l3, "NL - sent to lab but no lab data");
        $this->assertContains('label-danger', $l31, "Case Error - NL - sent to lab but no lab data");
        $this->assertContains('label-danger', $l4, "NL - lab data but no sent to lab");
        $this->assertContains('label-danger', $l41, "Case Error - NL - lab data but no sent to lab");
    }
}
