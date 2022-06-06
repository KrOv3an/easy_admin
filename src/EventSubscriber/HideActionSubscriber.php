<?php

namespace App\EventSubscriber;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;

class HideActionSubscriber implements EventSubscriberInterface
{
    /**
     * @param BeforeCrudActionEvent $event
     * @return void
     */
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
    {
        if (!$adminContext = $event->getAdminContext()) {
            return;
        }

        if (!$crudDto = $adminContext->getCrud()) {
            return;
        }

        if ($crudDto->getEntityFqcn() !== Question::class) {
            return;
        }

        //disable action entirely for delete, detail & edit page
        $question = $adminContext->getEntity()->getInstance();
        if ($question instanceof Question && $question->getIsApproved()) {
            $crudDto->getActionsConfig()->disableActions([Action::DELETE]);
        }

        //returns an array of actual actions that will be enabled
        // for the current page
        $actions = $crudDto->getActionsConfig()->getActions();
        if (!$deleteAction = $actions[Action::DELETE] ?? null) {
            return;
        }

        $deleteAction->setDisplayCallable(function (Question $question) {
            return !$question->getIsApproved();
        });
    }

    /**
     * @return string[]
     */
    #[ArrayShape([BeforeCrudActionEvent::class => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
        ];
    }
}
