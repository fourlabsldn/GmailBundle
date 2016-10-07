<?php

namespace FL\GmailBundle\Form\Type;

use FL\GmailBundle\Services\Directory;
use FL\GmailBundle\Services\OAuth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InboxType
 * @package FL\GmailBundle
 */
class InboxType extends AbstractType
{

    /**
     * @var string[]
     */
    private $emailToUserId;

    /**
     * InboxType constructor.
     * @param OAuth $oAuth
     * @param Directory $directory
     */
    public function __construct(OAuth $oAuth, Directory $directory)
    {
        $this->emailToUserId = $directory->resolveInboxesToUserIdArray(", ", $oAuth->resolveDomain(), Directory::MODE_RESOLVE_PRIMARY_ONLY);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->emailToUserId,
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
