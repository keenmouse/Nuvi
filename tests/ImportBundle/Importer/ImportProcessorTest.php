<?php

namespace NS\ImportBundle\Tests\Importer;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use NS\ImportBundle\Converter\Registry;
use NS\ImportBundle\Entity\Column;
use NS\ImportBundle\Entity\Import;
use NS\ImportBundle\Entity\Map;
use NS\ImportBundle\Filter\Duplicate;
use NS\ImportBundle\Filter\NotBlank;
use NS\ImportBundle\Importer\ImportProcessor;
use NS\ImportBundle\Linker\CaseLinkerRegistry;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of ImportProcessorTest
 *
 * @author gnat
 */
class ImportProcessorTest extends WebTestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidImportReader()
    {
        $file = new File(__DIR__ . '/../Fixtures/IBD-BadHeader.csv');

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import   = new Import($mockUser);
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($this->getIbdColumns()));

        $processor = $this->getContainer()->get('ns_import.processor');
        $processor->getReader($import);
    }
//
//    /**
//     * @param $file
//     * @param $interface
//     * @dataProvider getFiles
//     */
//    public function testGetReader($file, $interface)
//    {
//        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
//        $columns = array(
//            array(
//                'name'      => 'Country',
//                'converter' => null,
//                'mapper'    => null,
//                'ignored'   => true,
//            ),
//            array(
//                'name'      => 'Auto_ID',
//                'converter' => '',
//                'mapper'    => '',
//                'ignored'   => false,
//            ),
//        );
//
//        $map    = $this->getIbdMap($columns);
//        $import = new Import($mockUser);
//        $import->setSourceFile($file);
//        $import->setMap($map);
//
//        $registry = new Registry();
//        $mockEntityMgr = $this->createMock('\Doctrine\ORM\EntityManager');
//
//
//        $processor = new ImportProcessor($registry,$mockEntityMgr);
//        $reader    = $processor->getReader($import);
//        $this->assertInstanceOf($interface,$reader);
//    }
//
//    public static function getFiles()
//    {
//        return array(
//            array(new File(__DIR__ . '/../Fixtures/EMR-IBD-headers.xls'), 'Ddeboer\DataImport\Reader\ExcelReader'),
//            array(new File(__DIR__ . '/../Fixtures/EMR-IBD-headers.csv'), 'Ddeboer\DataImport\Reader\CsvReader'),
//            array(new File(__DIR__ . '/../Fixtures/EMR-IBD-headers.xlsx'), 'Ddeboer\DataImport\Reader\ExcelReader'),
//            array(new File(__DIR__ . '/../Fixtures/EMR-IBD-headers.ods'), 'Ddeboer\DataImport\Reader\ExcelReader'),
//        );
//    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExtraColumnReader()
    {
        $file    = new File(__DIR__ . '/../Fixtures/IBD-BadHeader.csv');
        $columns = [
            [
                'name'      => 'Col1',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => true,
            ],
            [
                'name'      => 'Col3',
                'converter' => '',
                'mapper'    => '',
                'ignored'   => false,
            ],
            [
                'name'      => 'Col2',
                'converter' => null,
                'mapper'    => '',
                'ignored'   => false,
            ],
            [
                'name'      => 'gender',
                'converter' => '',
                'mapper'    => '',
                'ignored'   => false,
            ],
            [
                'name'      => 'birthday',
                'converter' => 'ns_import.converter.date.who',
                'mapper'    => 'birthdate',
                'ignored'   => false,
            ],
        ];

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $processor = $this->getContainer()->get('ns_import.processor');
        $processor->getReader($import);
    }

    public function testMatchingColumnsReader()
    {
        $file    = new File(__DIR__ . '/../Fixtures/IBD-BadHeader.csv');
        $columns = [
            [
                'name'      => 'Col1',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => true,
            ],
            [
                'name'      => 'Col2',
                'converter' => '',
                'mapper'    => '',
                'ignored'   => false,
            ],
            [
                'name'      => 'Col3',
                'converter' => null,
                'mapper'    => '',
                'ignored'   => false,
            ],
        ];

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $processor = $this->getContainer()->get('ns_import.processor');
        $reader    = $processor->getReader($import);
        $this->assertInstanceOf('\Ddeboer\DataImport\Reader\CsvReader', $reader);
    }

    public function testGetDoctrineWriter()
    {
        $meta = $this->createMock('Doctrine\ORM\Mapping\ClassMetadata');
        $meta->expects($this->once())
            ->method('getName')
            ->willReturn('NS\SentinelBundle\Entity\IBD');

        $mockRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(['findBySiteAndCaseId'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockRepo->expects($this->never())
            ->method('findBySiteAndCaseId');

        $entityMgr = $this->createMock('\Doctrine\ORM\EntityManager');
        $entityMgr->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockRepo);

        $entityMgr->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($meta);

        $registry  = new Registry();
        $processor = new ImportProcessor($registry, $entityMgr, new CaseLinkerRegistry());
        $writer    = $processor->getWriter('NS\SentinelBundle\Entity\IBD', [], 'method');
        $this->assertInstanceOf('\Ddeboer\DataImport\Writer\DoctrineWriter', $writer);
        $writer2   = $processor->getWriter('NS\SentinelBundle\Entity\IBD', [], 'method');

        $this->assertEquals($writer, $writer2);
    }

//    public function testGetDoctrineWriterEntityManager()
//    {
//        $processor = new ImportProcessor($this->getContainer());
//        $writer = $processor->getWriter('NS\SentinelBundle\Entity\IBD');
//        $writer->prepare();
//    }

    public function testDuplicateFilterIsCalled()
    {
        $file    = new File(__DIR__ . '/../Fixtures/IBD-DuplicateRows.csv');
        $columns = [
            [
                'name'      => 'Col1',
                'converter' => null,
                'mapper'    => 'col1',
                'ignored'   => false,
            ],
            [
                'name'      => 'Col2',
                'converter' => '',
                'mapper'    => 'col2',
                'ignored'   => false,
            ],
            [
                'name'      => 'Col3',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'Col4',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
        ];

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $mockDuplicate = $this->getMockBuilder('NS\ImportBundle\Filter\Duplicate')
            ->setMethods(['__invoke', 'getFieldKey'])
            ->getMock();

        $mockDuplicate
            ->expects($this->at(0))
            ->method('__invoke')
            ->with(['col1' => 1, 'col2' => 2, 'Col3' => 3, 'Col4' => 4])
            ->willReturn(true);

        $mockDuplicate
            ->expects($this->at(1))
            ->method('__invoke')
            ->with(['col1' => 3, 'col2' => 3, 'Col3' => 4, 'Col4' => 5])
            ->willReturn(true);

        $mockDuplicate
            ->expects($this->at(2))
            ->method('__invoke')
            ->with(['col1' => 1, 'col2' => 2, 'Col3' => 5, 'Col4' => 6])
            ->willReturn(false);

        $mockDuplicate
            ->expects($this->at(3))
            ->method('__invoke')
            ->with(['col1' => 4, 'col2' => 5, 'Col3' => 6, 'Col4' => 7])
            ->willReturn(true);

        $container = $this->getContainer();
        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $container->get('ns_import.linker_registry'));
        $processor->setDuplicate($mockDuplicate);

        $reader    = $processor->getReader($import);

        $this->assertInstanceOf('\Ddeboer\DataImport\Reader\CsvReader', $reader);
        $this->assertCount(4, $reader);

        $outputData = [];

        $this->assertEquals(0, $import->getPosition());

        // Create the workflow from the reader
        $workflow   = new Workflow\StepAggregator($reader);
        $workflow->setSkipItemOnFailure(true);
        $workflow->addWriter(new ArrayWriter($outputData));

        $processor->addSteps($workflow, $import);

        $workflow->process();
        $this->assertCount(3, $outputData, print_r($outputData, true));
    }

    public function testDuplicates()
    {
        $file    = new File(__DIR__ . '/../Fixtures/IBD-DuplicateRows.csv');
        $columns = [
            [
                'name'      => 'Col1',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'Col2',
                'converter' => null,
                'mapper'    => 'col2',
                'ignored'   => false,
            ],
            [
                'name'      => 'Col3',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'Col4',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => true,
            ],
        ];

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $uniqueFields = ['Col1', 'col2'];
        $duplicate    = new Duplicate($uniqueFields);

        $container = $this->getContainer();
        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $container->get('ns_import.linker_registry'));
        $processor->setDuplicate($duplicate);
        $processor->setNotBlank(new NotBlank('Col1'));
        $reader    = $processor->getReader($import);

        $this->assertInstanceOf('\Ddeboer\DataImport\Reader\CsvReader', $reader);
        $this->assertCount(4, $reader);

        $outputData = [];
        $workflow   = new Workflow\StepAggregator($reader);
        $workflow->setSkipItemOnFailure(true);
        $workflow->addWriter(new ArrayWriter($outputData));

        $processor->addSteps($workflow, $import);

        $workflow->process();

        $this->assertCount(3, $outputData);
        $this->assertCount(1, $duplicate->toArray());
    }

    public function testBadDateFormat()
    {
        $file    = new File(__DIR__ . '/../Fixtures/IBD-BadDate.csv');
        $columns = [
            [
                'name'      => 'date',
                'converter' => 'ns_import.converter.date.who',
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'ignored',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => true,
            ],
        ];

        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $duplicate = new Duplicate([]);
        $container = $this->getContainer();

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $container->get('ns_import.linker_registry'));
        $processor->setDuplicate($duplicate);
        $processor->setNotBlank(new NotBlank('date'));
        $reader    = $processor->getReader($import);

        $this->assertInstanceOf('\Ddeboer\DataImport\Reader\CsvReader', $reader);
        $this->assertCount(2, $reader);

        $outputData = [];
        $workflow   = new Workflow\StepAggregator($reader);
        $workflow->setSkipItemOnFailure(true);
        $workflow->addWriter(new ArrayWriter($outputData));

        $processor->addSteps($workflow, $import);

        $result = $workflow->process();
        $except = $result->getExceptions();
        $this->assertCount(0, $outputData);
        $this->assertInstanceOf('\SplObjectStorage', $except);

        // THIS SHOULD BE 2 ???
        $this->assertEquals(2, $except->count());
    }

    public function testBlankFieldConversion()
    {
        $entityMgr = $this->createMock('\Doctrine\ORM\EntityManager');

        $registry  = new Registry();
        $processor = new ImportProcessor($registry, $entityMgr, new CaseLinkerRegistry());
        $processor->setNotBlank(new NotBlank("case_id"));
        $this->assertInstanceOf('NS\ImportBundle\Filter\NotBlank', $processor->getNotBlank());
        $this->assertTrue(is_array($processor->getNotBlank()->fields));
        $this->assertEquals(['case_id'], $processor->getNotBlank()->fields);
    }

    public function testNotBlank()
    {
        $notBlankStr = new NotBlank("field");
        $this->assertEquals(['field'], $notBlankStr->fields);

        $notBlankArr = new NotBlank(["field"]);
        $this->assertEquals(['field'], $notBlankArr->fields);

        $this->assertTrue($notBlankArr->__invoke(['field' => '1', 'something' => 2]));
        $this->assertFalse($notBlankArr->__invoke(['field' => null, 'something' => 2]));
        $this->assertFalse($notBlankArr->__invoke(['field' => '', 'something' => 2]));
    }

    public function testDeepArrayMap()
    {
        $columns = [
            [
                'name'      => 'Date Sample received-RRL',
                'converter' => 'ns_import.converter.date.year_month_day',
                'mapper'    => 'dateReceived',
                'ignored'   => true,
            ],
            [
                'name'      => 'RRL lab #',
                'converter' => null,
                'mapper'    => 'labId',
                'ignored'   => false,
            ],
            [
                'name'      => 'patient first name',
                'converter' => null,
                'mapper'    => 'caseFile.firstName',
                'ignored'   => false,
            ],
            [
                'name'      => 'Site Code',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'caseFile.site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case id',
                'converter' => null,
                'mapper'    => 'caseFile.case_id',
                'ignored'   => false,
            ],
        ];

        $source = [
            ['Date Sample received-RRL' => '2014/01/01', 'RRL lab #' => '13314',
                'Site Code' => 'ALBCHLD', 'case id' => '12', 'patient first name' => 'Fname 1'],
            ['Date Sample received-RRL' => '2014/06/10', 'RRL lab #' => '1314',
                'Site Code' => 'ALBCHLD', 'case id' => '14', 'patient first name' => 'Fname 2'],
            ['Date Sample received-RRL' => '2014/07/18', 'RRL lab #' => '12345',
                'Site Code' => 'ALBCHLD', 'case id' => '15', 'patient first name' => 'Fname 3'],
            ['Date Sample received-RRL' => '2014/09/15', 'RRL lab #' => '54321',
                'Site Code' => 'ALBCHLD', 'case id' => '16', 'patient first name' => 'Fname 4'],
        ];

        $processor = $this->getContainer()->get('ns_import.processor');
        $reader    = new ArrayReader($source);
        $mockUser  = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import    = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setMap($this->getReferenceLabMap($columns));

        $converters = $import->getConverters();
        $this->assertCount(1, $converters); // fields where ignored == true are completely dropped
//        $this->assertEquals('Site',end($converters)->getName());

        $this->assertInstanceOf('\Ddeboer\DataImport\Reader\ArrayReader', $reader);

        $outputData = [];
        $workflow   = new Workflow\StepAggregator($reader);
        $workflow->setSkipItemOnFailure(true);
        $workflow->addWriter(new ArrayWriter($outputData));

        $processor->addSteps($workflow, $import);

        $res = $workflow->process();
        $this->assertInstanceOf("Ddeboer\DataImport\Result", $res);
        if ($res->getErrorCount() > 0) {
            $exceptions = $res->getExceptions();
            $this->assertInstanceOf('\SplObjectStorage', $exceptions);
            $this->assertEquals($res->getErrorCount(), $exceptions->count());
            $object = $exceptions->current();
            $this->assertInstanceOf('\Exception', $object);
            if ($exceptions->valid()) {
                //                $object = $exceptions->current();
                $this->assertInstanceOf('\Exception', $object);
                $this->fail($object->getMessage());
            }
            $this->fail('Got here!?');
        }

        $this->assertCount(4, $outputData, sprintf("Didn't receive proper output - Error count: %d", $res->getErrorCount()));
        $this->assertInstanceOf('NS\SentinelBundle\Entity\Site', $outputData[0]['caseFile']['site']);
//        $this->fail(print_r(array_keys($outputData[0]['caseFile']),true));
//        $this->fail($outputData[0]['caseFile']['site']->getName());
    }

    /**
     * @group repositoryTest
     */
    public function testImportWithSiteLabFields()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_id',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'firstName',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'csf Date',
                'converter' => 'ns_import.converter.date.timestamp',
                'mapper'    => 'siteLab.csf_lab_date',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/IBD-CasePlusSiteLab.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $this->assertEquals(['site','case_id'],$linker->getCriteria());
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $repoMethod = $writer->getEntityRepositoryMethod();
        $this->assertTrue(is_callable($repoMethod));
        $this->assertEquals($repoMethod[1], 'findBySiteAndCaseId');

        $result    = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $this->fail("Error Count: ".$result->getErrorCount());
        }

        $this->assertEquals(2, $result->getSuccessCount());
    }

    public function testImportWithReferenceLabFields()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_id',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'lastName',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'date received',
                'converter' => 'ns_import.converter.date.afr1',
                'mapper'    => 'referenceLab.date_received',
                'ignored'   => false,
            ],
            [
                'name'      => 'lab id',
                'converter' => null,
                'mapper'    => 'referenceLab.lab_id',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/IBD-CasePlusRRL.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $repoMethod = $writer->getEntityRepositoryMethod();
        $this->assertTrue(is_callable($repoMethod));
        $this->assertEquals($repoMethod[1], 'findBySiteAndCaseId');

        $result    = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $this->fail('Error Count: '.$result->getErrorCount());
        }

        $this->assertEquals(2, $result->getSuccessCount());
    }

    public function testWarningsAdded()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_id',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'lastName',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'anTibioTics',
                'converter' => 'ns.sentinel.converter.triple_choice',
                'mapper'    => 'antibiotics',
                'ignored'   => false,
            ],
            [
                'name'      => 'date received',
                'converter' => 'ns_import.converter.date.afr1',
                'mapper'    => 'referenceLab.date_received',
                'ignored'   => false,
            ],
            [
                'name'      => 'lab id',
                'converter' => null,
                'mapper'    => 'referenceLab.lab_id',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/IBD-CasePlusRRL-WithWarning.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $repoMethod = $writer->getEntityRepositoryMethod();
        $this->assertTrue(is_callable($repoMethod));
        $this->assertEquals($repoMethod[1], 'findBySiteAndCaseId');

        $result    = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $this->fail('Error Count: '.$result->getErrorCount());
        }

        $this->assertEquals(2, $result->getSuccessCount());
        $results = $writer->getResults();
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[0]);
        $this->assertTrue($results[0]->hasWarning());
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[1]);
        $this->assertFalse($results[1]->hasWarning());
    }

    /**
     * @group futureDate
     */
    public function testNoFutureDate()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_id',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'lastName',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'date received',
                'converter' => 'ns_import.converter.date.year_month_day',
                'mapper'    => 'referenceLab.dt_sample_recd',
                'ignored'   => false,
            ],
            [
                'name'      => 'lab id',
                'converter' => null,
                'mapper'    => 'referenceLab.lab_id',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/IBD-CasePlusRRL-FutureDate.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime());
        $import->setInputDateEnd(new \DateTime());
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $result = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $this->fail('Error Count: '.$result->getErrorCount());
        }

        $this->assertEquals(2, $result->getSuccessCount());
        $results = $writer->getResults();
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[0]);
        $this->assertTrue($results[0]->hasWarning());
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD\ReferenceLab',$results[0]->getReferenceLab());
        $this->assertEquals('1125',$results[0]->getReferenceLab()->getLabId());
        $this->assertInstanceOf('\DateTime', $results[0]->getReferenceLab()->getDateReceived());
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD',$results[1]);
        $this->assertFalse($results[1]->hasWarning());
    }

    /**
     * @group futureDate
     */
    public function testDatesInRange()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_id',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'AdmDate',
                'converter' => 'ns_import.converter.date.year_month_day',
                'mapper'    => 'adm_date',
                'ignored'   => false,
            ],
            [
                'name'      => 'lastName',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => false,
            ],
            [
                'name'      => 'date received',
                'converter' => 'ns_import.converter.date.year_month_day',
                'mapper'    => 'referenceLab.date_received',
                'ignored'   => false,
            ],
            [
                'name'      => 'lab id',
                'converter' => null,
                'mapper'    => 'referenceLab.lab_id',
                'ignored'   => false,
            ],
        ];

        $start = new \DateTime('2015-08-01');
        $end = new \DateTime('2015-08-30');

        $file = new File(__DIR__ . '/../Fixtures/IBD-CasePlusRRL-DatesInRange.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart($start);
        $import->setInputDateEnd($end);
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $result = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $this->fail('Error Count: '.$result->getErrorCount());
        }

        $this->assertEquals(2, $result->getSuccessCount());
        $results = $writer->getResults();
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[0]);
        $this->assertTrue($results[0]->hasWarning());
        $this->assertInstanceOf('\DateTime', $results[0]->getAdmDate());
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[1]);
        $this->assertFalse($results[1]->hasWarning());
    }

    /**
     * @group preprocessor
     *
     * This tests *two critical* features of the PreProcessorStep.
     *
     * Given an item array with with two PreProcessors per row, ensure that a match on the first property does not stop
     * the preProcessor at looking at the second property with a match.
     *
     * Given a preprocessor set with multiple independent conditions, once one match is made it stops looking
     * For example in the adm_dx preProcessor, if the first condition is true (orgValue = 3) it should stop processing,
     * otherwise it will change it to 3, which matches the third condition which then changes it to 6. This would seem
     * like a nice feature, except a single condition can be individually complex enough to express something like that.
     */
    public function testPreProcessor()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_ID',
                'converter' => null,
                'mapper'    => 'case_id',
                'ignored'   => false,
            ],
            [
                'name'      => 'adm_dx',
                'preProcessor' => '[
                                     {"conditions":{"condition":"AND","rules":[{"id":"adm_dx","field":"adm_dx","type":"string","input":"text","operator":"equal","value":"1"}]},"output_value":"3"},
                                     {"conditions":{"condition":"AND","rules":[{"id":"adm_dx","field":"adm_dx","type":"string","input":"text","operator":"equal","value":"2"}]},"output_value":"1"},
                                     {"conditions":{"condition":"AND","rules":[{"id":"adm_dx","field":"adm_dx","type":"string","input":"text","operator":"equal","value":"3"}]},"output_value":"4"},
                                     {"conditions":{"condition":"AND","rules":[{"id":"adm_dx","field":"adm_dx","type":"string","input":"text","operator":"equal","value":"4"}]},"output_value":"6"},
                                     {"conditions":{"condition":"AND","rules":[{"id":"adm_dx","field":"adm_dx","type":"string","input":"text","operator":"equal","value":"9"}]},"output_value":"99"}
                                   ]',
                'converter' => 'ns.sentinel.converter.diagnosis',
                'mapper'    => 'adm_dx',
                'ignored'   => false,
            ],
            [
                'name'      => 'lName',
                'preProcessor'=> '[
                                 {"conditions":{"condition":"AND","rules":[{"id":"lName","field":"lName","type":"string","input":"text","operator":"equal","value":"l Name2"}]},"output_value":"Second"}
                                ]',
                'converter' => null,
                'mapper'    => 'lastName',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/IBD-PreProcess.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime('2015-08-01'));
        $import->setInputDateEnd(new \DateTime('2015-08-30'));
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $container = $this->getContainer();
        $linkerRegistry = $container->get('ns_import.linker_registry');
        $linker = $linkerRegistry->getLinker($import->getCaseLinkerId());

        $processor = new ImportProcessor($container->get('ns_import.converters'), $container->get('doctrine.orm.entity_manager'), $linkerRegistry);
        $processor->setDuplicate(new Duplicate($linker->getCriteria()));
        $writer = $processor->getWriter($import->getClass(), $linker->getCriteria(), $linker->getRepositoryMethod());
        $result = $processor->process($import);

        if (count($result->getExceptions()) > 0) {
            $exceptions = $result->getExceptions();
            $exceptions->rewind();
            $object = $exceptions->current();

            $this->fail('Error Count: '.$result->getErrorCount().' '.$object->getMessage());
        }

        $this->assertEquals(6, $result->getSuccessCount());
        $results = $writer->getResults();
        $this->assertInstanceOf('NS\SentinelBundle\Entity\IBD', $results[0]);

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[0]->getAdmDx());
        $this->assertTrue($results[0]->getAdmDx()->equal(3), sprintf('%d != %d', 3, $results[0]->getAdmDx()->getValue()));

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[1]->getAdmDx());
        $this->assertTrue($results[1]->getAdmDx()->equal(1), sprintf('%d != %d', 1, $results[1]->getAdmDx()->getValue()));
        $this->assertEquals('Second', $results[1]->getLastName());

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[2]->getAdmDx());
        $this->assertTrue($results[2]->getAdmDx()->equal(4), sprintf('%d != %d', 4, $results[2]->getAdmDx()->getValue()));

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[3]->getAdmDx());
        $this->assertTrue($results[3]->getAdmDx()->equal(6), sprintf('%d != %d', 6, $results[3]->getAdmDx()->getValue()));

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[4]->getAdmDx());
        $this->assertTrue($results[4]->getAdmDx()->equal(99), sprintf('%d != %d', 99, $results[4]->getAdmDx()->getValue()));

        $this->assertInstanceOf('NS\SentinelBundle\Form\IBD\Types\Diagnosis', $results[5]->getAdmDx());
        $this->assertTrue($results[5]->getAdmDx()->equal(6), sprintf('%d != %d', 6, $results[5]->getAdmDx()->getValue()));
    }

    /**
     * @group dobFilter
     */
    public function testDateOfBirthFilter()
    {
        $columns = [
            [
                'name'      => 'site_CODE',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_ID',
                'converter' => null,
                'mapper'    => 'case_id',
                'ignored'   => false,
            ],
            [
                'name'      => 'adm_date',
                'converter' => 'ns_import.converter.date.iso',
                'mapper'    => 'adm_date',
                'ignored'   => false,
            ],
            [
                'name'      => 'birthday',
                'converter' => 'ns_import.converter.date.iso',
                'mapper'    => 'birthdate',
                'ignored'   => false,
            ],
        ];

        $file = new File(__DIR__ . '/../Fixtures/DobAdmDate.csv');
        $mockUser = $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
        $import = new Import($mockUser);
        $import->setInputDateStart(new \DateTime('2014-01-01'));
        $import->setInputDateEnd(new \DateTime('2017-01-30'));
        $import->setSourceFile($file);
        $import->setMap($this->getIbdMap($columns));

        $importer = $this->getContainer()->get('ns_import.processor');
        $result = $importer->process($import);
        $this->assertEquals(2, $result->getTotalProcessedCount());
        $this->assertEquals(2, $result->getSuccessCount());
        $this->assertEquals(0, $result->getSkippedCount());
    }

    public function getReferenceLabMap(array $columns)
    {
        return $this->getMap('NS\SentinelBundle\Entity\IBD\ReferenceLab', 'IBD Reference Lab', $columns);
    }

    public function getIbdMap(array $columns)
    {
        return $this->getMap('NS\SentinelBundle\Entity\IBD', 'IBD Clinical', $columns);
    }

    public function getMap($class, $name, $columns, $linker = 'ns_import.standard_case_linker')
    {
        $map = new Map();
        $map->setName($name);
        $map->setClass($class);
        $map->setCaseLinker($linker);

        foreach ($columns as $colArray) {
            $column = new Column();
            $column->setName($colArray['name']);
            $column->setConverter($colArray['converter']);
            $column->setMapper($colArray['mapper']);
            $column->setIgnored($colArray['ignored']);
            if (isset($colArray['preProcessor'])) {
                $column->setPreProcessor($colArray['preProcessor']);
            }
            $map->addColumn($column);
        }

        return $map;
    }

    public function getIbdColumns()
    {
        return [
            [
                'name'      => 'ISO3_code',
                'converter' => null,
                'mapper'    => null,
                'ignored'   => true,
            ],
            [
                'name'      => 'site_code',
                'converter' => 'ns.sentinel.converter.site',
                'mapper'    => 'site',
                'ignored'   => false,
            ],
            [
                'name'      => 'case_ID',
                'converter' => null,
                'mapper'    => 'case_id',
                'ignored'   => false,
            ],
            [
                'name'      => 'gender',
                'converter' => 'ns.sentinel.converter.gender',
                'mapper'    => 'gender',
                'ignored'   => false,
            ],
        ];
    }
}
