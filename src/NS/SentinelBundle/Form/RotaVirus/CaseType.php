<?php

namespace NS\SentinelBundle\Form\RotaVirus;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NS\SentinelBundle\Form\Types\RotavirusVaccinationReceived;
use NS\SentinelBundle\Form\Types\TripleChoice;
use NS\SentinelBundle\Form\Types\Dehydration;
use \NS\SentinelBundle\Form\Types\ThreeDoses;

/**
 * Class CaseType
 * @package NS\SentinelBundle\Form\Rota
 */
class CaseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = (isset($options['method']) && $options['method'] == 'PUT');

        $builder
            ->add('lastName',                   null,               array('required'=>$required, 'label'=>'rotavirus-form.last-name'))
            ->add('firstName',                  null,               array('required'=>$required, 'label'=>'rotavirus-form.first-name'))
            ->add('parentalName',               null,               array('required'=>$required, 'label'=>'rotavirus-form.parental-name'))
            ->add('caseId',                     null,               array('required'=>true,      'label'=>'rotavirus-form.caseId'))
            ->add('gender',                     'Gender',           array('required'=>$required, 'label'=>'rotavirus-form.gender'))
            ->add('dobKnown',                   'TripleChoice',     array('required'=>$required, 'label' => 'ibd-form.date-of-birth-known', 'attr' => array('data-context-child' => 'dob')))
            ->add('birthdate',                  'acedatepicker',    array('required'=>$required, 'label' => 'ibd-form.date-of-birth', 'attr' => array('data-context-parent' => 'dob', 'data-context-value' => TripleChoice::YES), 'widget' => 'single_text'))
            ->add('dobYears',                   null,               array('required'=>$required, 'label' => 'ibd-form.date-of-birth-years', 'attr' => array('data-context-parent' => 'dob', 'data-context-value' => TripleChoice::NO)))
            ->add('dobMonths',                  null,               array('required'=>$required, 'label' => 'ibd-form.date-of-birth-months', 'attr' => array('data-context-parent' => 'dob', 'data-context-value' => TripleChoice::NO)))
            ->add('district',                   null,               array('required'=>$required, 'label'=>'rotavirus-form.district'))
            ->add('state',                      null,               array('required'=>$required, 'label'=>'rotavirus-form.state'))
            ->add('admDate',                    'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.admissionDate'))

            ->add('intensiveCare',              'TripleChoice',     array('required'=>$required, 'label'=>'rotavirus-form.intensiveCare', ))
            ->add('symptomDiarrhea',            'TripleChoice',     array('required'=>$required, 'label'=>'rotavirus-form.symptomDiarrhea',         'attr' => array('data-context-child'=>'vaccineReceived')))
            ->add('symptomDiarrheaOnset',       'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.symptomDiarrheaOnset',    'attr' => array('data-context-parent'=>'vaccineReceived', 'data-context-value'=>  TripleChoice::YES)))
            ->add('symptomDiarrheaEpisodes',    null,               array('required'=>$required, 'label'=>'rotavirus-form.symptomDiarrheaEpisodes', 'attr' => array('data-context-parent'=>'vaccineReceived', 'data-context-value'=>  TripleChoice::YES)))
            ->add('symptomDiarrheaDuration',    null,               array('required'=>$required, 'label'=>'rotavirus-form.symptomDiarrheaDuration', 'attr' => array('data-context-parent'=>'vaccineReceived', 'data-context-value'=>  TripleChoice::YES)))

            ->add('symptomVomit',               'TripleChoice',     array('required'=>$required, 'label'=>'rotavirus-form.symptomVomit',            'attr' => array('data-context-child'=>'symptomVomit')))
            ->add('symptomVomitEpisodes',       null,               array('required'=>$required, 'label'=>'rotavirus-form.symptomVomitEpisodes',    'attr' => array('data-context-parent'=>'symptomVomit', 'data-context-value'=>  TripleChoice::YES)))
            ->add('symptomVomitDuration',       null,               array('required'=>$required, 'label'=>'rotavirus-form.symptomVomitDuration',    'attr' => array('data-context-parent'=>'symptomVomit', 'data-context-value'=>  TripleChoice::YES)))

            ->add('symptomDehydration',         'Dehydration',     array('required'=>$required, 'label'=>'rotavirus-form.symptomDehydration',       'attr' => array('data-context-child'=>'symptomDehydration')))
            ->add('rehydration',                'TripleChoice',     array('required'=>$required, 'label'=>'rotavirus-form.rehydration',              'attr' => array('data-context-parent'=>'symptomDehydration','data-context-value'=> json_encode(array(Dehydration::SOME,Dehydration::SEVERE)))))
            ->add('rehydrationType',            'Rehydration',      array('required'=>$required, 'label'=>'rotavirus-form.rehydrationType',          'attr' => array('data-context-parent'=>'symptomDehydration','data-context-value'=> json_encode(array(Dehydration::SOME,Dehydration::SEVERE)))))
            ->add('rehydrationOther',           null,               array('required'=>$required, 'label'=>'rotavirus-form.rehydrationOther',         'attr' => array('data-context-parent'=>'symptomDehydration','data-context-value'=> json_encode(array(Dehydration::SOME,Dehydration::SEVERE)))))

            ->add('vaccinationReceived',        'RotavirusVaccinationReceived', array('required'=>$required, 'label'=>'rotavirus-form.vaccinationReceived',
                                                                                        'attr' => array('data-context-child'=>'vaccineReceived')))
            ->add('vaccinationType',            'RotavirusVaccinationType',     array('required'=>$required, 'label'=>'rotavirus-form.vaccinationType',
                                                                                        'attr' => array(
                                                                                                        'data-context-parent'=>'vaccineReceived',
                                                                                                        'data-context-value'=>json_encode(array(RotavirusVaccinationReceived::YES_CARD, RotavirusVaccinationReceived::YES_HISTORY)))))
            ->add('doses',                      'ThreeDoses',   array('required'=>$required, 'label'=>'rotavirus-form.doses',
                                                                                        'attr' => array(
                                                                                                        'data-context-parent'=>'vaccineReceived',
                                                                                                        'data-context-child'=>'vaccineReceivedDoses',
                                                                                                        'data-context-value'=>json_encode(array(RotavirusVaccinationReceived::YES_CARD, RotavirusVaccinationReceived::YES_HISTORY)))))
            ->add('firstVaccinationDose',       'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.firstVaccinationDose',
                                                                                        'attr' => array(
                                                                                                        'data-context-parent'=>'vaccineReceivedDoses',
                                                                                                        'data-context-value'=> json_encode(array(ThreeDoses::ONE, ThreeDoses::TWO, ThreeDoses::THREE)))))
            ->add('secondVaccinationDose',      'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.secondVaccinationDose',
                                                                                        'attr' => array(
                                                                                                        'data-context-parent'=>'vaccineReceivedDoses',
                                                                                                        'data-context-value'=>json_encode(array(ThreeDoses::TWO, ThreeDoses::THREE)))))
            ->add('thirdVaccinationDose',       'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.thirdVaccinationDose',
                                                                                        'attr' => array(
                                                                                                        'data-context-parent'=>'vaccineReceivedDoses',
                                                                                                        'data-context-value'=>ThreeDoses::THREE)))
            ->add('stoolCollected',             'TripleChoice',     array('required'=>$required, 'label'=>'rotavirus-form.stoolCollected',  'attr' => array('data-context-child'=>'stoolCollected')))
            ->add('stoolId',                    null,               array('required'=>$required, 'label'=>'rotavirus-form.stoolId',         'attr' => array('data-context-parent'=>'stoolCollected', 'data-context-value'=>  TripleChoice::YES)))
            ->add('stoolCollectionDate',        'acedatepicker',    array('required'=>$required, 'label'=>'rotavirus-form.stoolCollectionDate',         'attr' => array('data-context-parent'=>'stoolCollected', 'data-context-value'=>  TripleChoice::YES)))
            ->add('dischargeOutcome',           'RotavirusDischargeOutcome', array('required'=>false, 'label'=>'rotavirus-form.dischargeOutcome'))
            ->add('dischargeDate',              'acedatepicker',    array('required'=>false, 'label'=>'rotavirus-form.dischargeDate'))
            ->add('dischargeClassification',    'RotaVirusDischargeClassification', array('required'=>false,'label'=>'rotavirus-form.dischargeClassification'))
            ->add('dischargeClassOther',        null,               array('required'=>false, 'label'=>'rotavirus-form.dischargeClassOther'))
            ->add('comment',                    null,               array('required'=>false, 'label'=>'rotavirus-form.comment'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\SentinelBundle\Entity\RotaVirus'
        ));
    }
}