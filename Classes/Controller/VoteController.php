<?php
namespace Visol\Votable\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use Visol\Votable\Domain\Model\Vote;
use Visol\Votable\Service\UserService;
use Visol\Votable\TypeConverter\VoteConverter;

/**
 * VotingController
 */
class VoteController extends ActionController
{
    /**
     * @var \Visol\Votable\Domain\Repository\VoteRepository
     * @inject
     */
    protected $voteRepository;

    /**
     * @var \Visol\Votable\Domain\Repository\VotedObjectRepository
     * @inject
     */
    protected $votedObjectRepository;

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction()
    {
        if ($this->arguments->hasArgument('vote')) {

            /** @var VoteConverter $typeConverter */
            $typeConverter = $this->objectManager->get(VoteConverter::class);

            $this->arguments->getArgument('vote')
                ->getPropertyMappingConfiguration()
                ->setTypeConverter($typeConverter);
        }
    }

    /**
     * @return null|string
     */
    public function indexAction()
    {
        // Check if environment is sane.
        $possibleMessage = null;
        if (empty($this->settings['contentType'])) {

            $possibleMessage = '<strong style="color: red">Please select a content type to be voted!</strong>';
        } elseif (empty($GLOBALS['TCA'][$this->settings['contentType']])) {

            $possibleMessage = '<strong style="color: red">Not a valid content type to be voted!</strong>';
        } else {

            $this->view->assign('settings', $this->settings);
            $this->view->assign('contentElement', $this->configurationManager->getContentObject()->data);
            $votedItems = [];
            if ($this->getUserService()->isAuthenticated()) {
                $userIdentifier = $this->getUserService()->getUserIdentifier();
                $votedItems = $this->votedObjectRepository->findFor($this->settings['contentType'], $userIdentifier);
            }
            $this->view->assign('votedItems', $this->formatVotedItems($votedItems));

            $closingDate = (int)$this->settings['closingDate'];
            $this->view->assign('isVoteOpen', $closingDate > 0 && $closingDate > time());
        }

        return $possibleMessage;
    }

    /**
     * @param Vote $vote
     *
     * @return string
     * @validate $vote \Visol\Votable\Domain\Validator\VoteDoesNotExistValidator
     */
    public function addAction(Vote $vote)
    {
        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeVoteChange', [$vote, 'add']);

        /** @var Vote $vote */
        $vote = $signalResult[0];

        $this->voteRepository->add($vote);

        $this->view->assign('vote', $vote);

        // Send signal
        $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterVoteChange', [$vote, 'add']);

        $this->response->setHeader('Content-Type', 'application/json');
        return json_encode([
            'action' => 'add',
            'success' => $this->voteRepository->exists($vote),
            'identifier' => $vote->getUid(),
        ]);
    }

    /**
     * @param Vote $vote
     * @return string
     * @validate $vote \Visol\Votable\Domain\Validator\VoteExistsValidator
     */
    public function removeAction(Vote $vote)
    {
        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeVoteChange', [$vote, 'remove']);
        $vote = $signalResult[0];

        $this->voteRepository->remove($vote);

        $this->view->assign('vote', $vote);

        // Send signal
        $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterVoteChange', [$vote, 'remove']);

        $this->response->setHeader('Content-Type', 'application/json');
        return json_encode([
            'action' => 'remove',
            'success' => !$this->voteRepository->exists($vote),
            'identifier' => 0,
        ]);
    }

    /**
     * @param array $votedItems
     * @return string
     */
    protected function formatVotedItems(array $votedItems)
    {
        $items = [];
        foreach ($votedItems as $votedItem) {
            $items[] = (int)$votedItem['uid_foreign'];
        }
        return json_encode($items);
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher()
    {
        return $this->objectManager->get(Dispatcher::class);
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->objectManager->get(UserService::class);
    }

}