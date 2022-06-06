<?php

namespace App\EventSubscriber;

use App\Entity\Question;
use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\Security\Core\Security;

class BlameableSubscriber implements EventSubscriberInterface
{
    /**
     * @param Security $security
     */
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $question = $event->getEntityInstance();
        if (!$question instanceof Question) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new LogicException('Currently logged in User is not instance of User');
        }

        $question->setUpdatedBy($user);
    }

    /**
     * @return string[]
     */
    #[ArrayShape([BeforeEntityUpdatedEvent::class => "string"])] public static function getSubscribedEvents(): array
    {
        return [
//            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
