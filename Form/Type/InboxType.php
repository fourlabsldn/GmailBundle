<?php

namespace FL\GmailBundle\Form\Type;

use FL\GmailBundle\Services\Directory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InboxType.
 */
class InboxType extends AbstractType
{
    /**
     * @var string[]
     */
    protected $emailToUserId;

    /**
     * InboxType constructor.
     *
     * @param Directory $directory
     */
    public function __construct(Directory $directory)
    {
        $this->emailToUserId = $directory->resolveInboxesToUserIdArray(', ', Directory::MODE_RESOLVE_PRIMARY_ONLY);
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
