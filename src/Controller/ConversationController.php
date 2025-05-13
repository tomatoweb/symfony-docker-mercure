<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\ConversationFactory;
use App\Repository\ConversationRepository;
use App\Service\TopicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Attribute\Route;


/**
 * @method User|null getUser()
 */
final class ConversationController extends AbstractController
{

    public function __construct(
        private readonly Authorization $authorization,
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationFactory $conversationFactory,
        private readonly TopicService $topicService,
        private readonly Discovery $discovery
    ) {
    }

    #[Route('/conversation/users/{recipient}', name: 'conversation.show')]
    public function show(?User $recipient, Request $request): Response
    {

        $sender = $this->getUser();

        $conversation = $this->conversationRepository->findByUsers($sender, $recipient);

        if(!$conversation) {
            $conversation = $this->conversationFactory->create($sender, $recipient);
        }

        $topic = $this->topicService->getTopicUrl($conversation);

        $this->discovery->addLink($request);

        $this->authorization->setCookie($request, [$topic]);

        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
            'topic' => $topic
        ]);
    }
}
