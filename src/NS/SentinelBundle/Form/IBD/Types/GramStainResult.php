<?php

namespace NS\SentinelBundle\Form\IBD\Types;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use NS\UtilBundle\Form\Types\TranslatableArrayChoice;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GramStainResult extends TranslatableArrayChoice implements TranslationContainerInterface
{
    const GM_NEG_DIPLOCOCCI    = 1;
    const GM_NEG_COCCOBACILLI  = 2;
    const GM_NEG_RODS          = 3;
    const GM_POS_COCCI_PAIRS   = 4;
    const GM_POS_COCCI_CLUSTER = 5;
    const OTHER                = 6;
    const UNKNOWN              = 99;

    protected $values = [
        self::GM_NEG_DIPLOCOCCI    => 'Gm neg diplococci',
        self::GM_NEG_COCCOBACILLI  => 'Gm neg coccobacilli',
        self::GM_NEG_RODS          => 'Gm neg rods',
        self::GM_POS_COCCI_PAIRS   => 'Gm pos cocci pairs',
        self::GM_POS_COCCI_CLUSTER => 'Gm pos cocci clusters',
        self::OTHER                => 'Other',
        self::UNKNOWN              => 'Unknown',
    ];

    /** @var AuthorizationCheckerInterface */
    private $authChecker;

    /**
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function setAuthorizationChecker($authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        if ($this->authChecker->isGranted('ROLE_AMR')) {
            unset($this->values[self::OTHER]);
            $this->values[self::UNKNOWN] = 'Undetermined';
        }

        parent::configureOptions($resolver);
    }

    static function getTranslationMessages()
    {
        $messages = parent::getTranslationMessages();
        $messages[] = new Message('Undetermined');

        return $messages;
    }
}
