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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use Visol\Votable\Domain\Model\Vote;
use Visol\Votable\Domain\Model\Voting;

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
     * @var string
     */
    protected $defaultViewObjectName = 'TYPO3\CMS\Extbase\Mvc\View\JsonView';


    protected $configuration = array(
        'vote' => array(
            '_descendAll' => array(
                //'_only' => array('property1', 'property2'),
                '_exclude' => array('pid')
            )
        )
    );

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction()
    {

//        if ($this->arguments->hasArgument('data')) {
//
//            /** @var \Fab\VidiFrontend\TypeConverter\ContentConverter $typeConverter */
//            $typeConverter = $this->objectManager->get('Fab\VidiFrontend\TypeConverter\DataConverter');
//
//            $this->arguments->getArgument('content')
//                ->getPropertyMappingConfiguration()
//                ->setTypeConverter($typeConverter);
//        }

    }

    /**
     * @param Voting $voting
     */
    public function listAction(Voting $voting)
    {
        // @todo define if useful
    }

    /**
     * @param Voting $voting
     */
    public function showAction(Voting $voting)
    {
        // @todo define if useful
    }

    /**
     * @param Vote $vote
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function createAction(Vote $vote)
    {

        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeVoteCreate', $vote);

        $this->voteRepository->add($vote);
        $this->view->assign('vote', $vote);

        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterVoteCreate', $vote);

        $this->response->setHeader('Content-Type', 'application/json');
    }

    /**
     * @param Vote $vote
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function updateAction(Vote $vote)
    {

        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeVoteUpdate', $vote);


        $this->voteRepository->update($vote);
        #$this->voteRepository->findByUid()
        $this->view->assign('vote', $vote);

        // Send signal
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterVoteUpdate', $vote);

        $this->response->setHeader('Content-Type', 'application/json');
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher() {
        return $this->objectManager->get(Dispatcher::class);
    }

}