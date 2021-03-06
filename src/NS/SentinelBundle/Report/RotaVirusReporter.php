<?php

namespace NS\SentinelBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use NS\SentinelBundle\Report\Result\RotaVirus\GeneralStatisticResult;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RotaVirusReporter extends AbstractReporter
{
    /**
     * @param Request $request
     * @param FormInterface $form
     * @param $redirectRoute
     * @return array|RedirectResponse
     */
    public function getDataQuality(Request $request, FormInterface $form, $redirectRoute)
    {
        $results = new ArrayCollection();
        $alias = 'i';
        $queryBuilder = $this->entityMgr->getRepository('NSSentinelBundle:Site')->getWithCasesForDate($alias, 'NS\SentinelBundle\Entity\RotaVirus');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('reset')->isClicked()) {
                return new RedirectResponse($this->router->generate($redirectRoute));
            }

            $this->filter->addFilterConditions($form, $queryBuilder, $alias);

            $sites = $queryBuilder->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

            if (empty($sites)) {
                return ['sites' => [], 'form' => $form->createView()];
            }

            $this->populateSites($sites, $results, 'NS\SentinelBundle\Report\Result\RotaVirus\DataQualityResult');

            $repo = $this->entityMgr->getRepository('NSSentinelBundle:RotaVirus');
            $columns = [
                'getStoolCollectionDateErrorCountBySites' => 'setStoolCollectionDateErrorCount',
                'getMissingDischargeOutcomeCountBySites' => 'setMissingDischargeOutcomeCount',
                'getMissingDischargeDateCountBySites' => 'setMissingDischargeDateCount',
                'getStoolCollectedCountBySites' => 'setStoolCollectedCount',
                'getElisaDoneCountBySites' => 'setElisaDoneCount',
                'getElisaPositiveCountBySites' => 'setElisaPositiveCount',
            ];

            $this->processResult($columns, $repo, $alias, $results, $form);

            if ($form->get('export')->isClicked()) {
                $this->exporter->export('NSSentinelBundle:Report/RotaVirus/Export:data-quality.html.twig', ['sites'=>$results]);
            }
        }

        return ['sites' => $results, 'form' => $form->createView()];
    }

    public function getSitePerformance(Request $request, FormInterface $form, $redirectRoute)
    {
        $results = new ArrayCollection();
        $alias = 'i';
        $queryBuilder = $this->entityMgr->getRepository('NSSentinelBundle:Site')->getWithCasesForDate($alias, 'NS\SentinelBundle\Entity\RotaVirus');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('reset')->isClicked()) {
                return new RedirectResponse($this->router->generate($redirectRoute));
            }

            $this->filter->addFilterConditions($form, $queryBuilder, $alias);

            $sites = $queryBuilder->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

            if (empty($sites)) {
                return ['sites' => [], 'form' => $form->createView()];
            }

            $this->populateSites($sites, $results, 'NS\SentinelBundle\Report\Result\RotaVirus\SitePerformanceResult');

            $repo = $this->entityMgr->getRepository('NSSentinelBundle:RotaVirus');
            $columns = [
                'getConsistentReporting' => 'addConsistentReporting',
                'getZeroReporting' => 'addConsistentReporting',
            ];

            $this->processSitePerformanceResult($columns, $repo, $alias, $results, $form);

            $columns = [
                'getSpecimenCollectedWithinTwoDays' => 'setSpecimenCollection',
                'getLabConfirmedCount' => 'setLabConfirmed',
            ];

            $this->processResult($columns, $repo, $alias, $results, $form);

            if($form->get('export')->isClicked()) {
                $this->exporter->export('NSSentinelBundle:Report/RotaVirus/Export:site-performance.html.twig', ['sites'=>$results]);
            }
        }

        return ['sites' => $results, 'form' => $form->createView()];
    }

    public function getDataLinking(Request $request, FormInterface $form, $redirectRoute)
    {
        $results = new ArrayCollection();
        $alias = 'i';

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('reset')->isClicked()) {
                return new RedirectResponse($this->router->generate($redirectRoute));
            }

            $queryBuilder = $this->entityMgr->getRepository('NSSentinelBundle:Country')->getWithCasesForDate($alias, 'NS\SentinelBundle\Entity\RotaVirus');

            $this->filter->addFilterConditions($form, $queryBuilder, $alias);

            $countries =  $queryBuilder->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

            if (empty($countries)) {
                return ['sites' => [], 'form' => $form->createView()];
            }

            $this->populateCountries($countries, $results, 'NS\SentinelBundle\Report\Result\DataLinkingResult');
            $repo = $this->entityMgr->getRepository('NSSentinelBundle:RotaVirus');

            if ($form->get('export')->isClicked()) {
                $results = $repo->getFailedLink($alias, $results->getKeys())->getQuery()->getResult();
                return $this->exporter->export('NSSentinelBundle:Report/RotaVirus/Export:data-linking.html.twig', ['results' => $results]);
            }

            $columns = [
                'getLinkedCount' => 'setLinked',
                'getFailedLinkedCount' => 'setNotLinked',
                'getNoLabCount' => 'setNoLab',
            ];

            $this->processLinkingResult($columns, $repo, $alias, $results, $form);
        }

        return ['sites' => $results, 'form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @param $redirectRoute
     * @return array|RedirectResponse
     */
    public function getStats(Request $request, FormInterface $form, $redirectRoute)
    {
        return $this->retrieveStats('NSSentinelBundle:RotaVirus',new GeneralStatisticResult(),$request,$form,$redirectRoute);
    }
}
