<?php
namespace Visol\Votable\Domain\Validator;

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
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use Visol\Votable\Domain\Model\Vote;
use Visol\Votable\Domain\Repository\VotedObjectRepository;
use Visol\Votable\Domain\Repository\VoteRepository;
use Visol\Votable\Service\ContentElementService;
use Visol\Votable\Service\UserService;

/**
 * Validate "columns" to be displayed in the BE module.
 */
class VoteValidator extends AbstractValidator
{

    const ALLOWED_ONLY_ONCE = 1;

    const ALLOWED_ONLY_ONCE_PER_24 = 2;

    /**
     * Check if $columns is valid. If it is not valid, throw an exception.
     *
     * @param Vote $vote
     * @return void
     */
    public function isValid($vote)
    {

        // Check if User is logged in
        if (!$this->getUserService()->isAuthenticated()) {
            print 'Authentication required.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_401);
        }

        if (!$vote instanceof Vote) {
            print 'I could not instantiate the Vote object.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
        }

        if (empty($vote->getVotedObject()->getContentType())) {
            print 'I miss a valid content type.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
        }

        $object = $this->getVotedObjectRepository()->findOne($vote);
        if (empty($object)) {
            print 'I could not retrieve the voted object.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_404);
        }

        // Check the content element that contains the voting meta information.
        $contentElementIdentifier = (int)GeneralUtility::_GP('contentElement');
        if ($contentElementIdentifier < 1) {
            print 'Invalid or missing content element parameter.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
        }

        $content = $this->getContentElementService()->get($contentElementIdentifier);
        if (empty($content)) {
            print 'I could not retrieve this content element: ' . $contentElementIdentifier;
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_404);
        }

        $settings = $this->getContentElementService()->getSettings($contentElementIdentifier);

        if ((int)$settings['closingDate'] > 0 && (int)$settings['closingDate'] < time()) {
            print 'Sorry, the vote is closed for this content element: ' . $contentElementIdentifier;
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
        }

        $allowedFrequency = (int)$settings['allowedFrequency'];
        $userIdentifier = $vote->getUser();
        $lastVote = $this->getVoteRepository()->findLastVote($settings['contentType'], $userIdentifier);
        if ($allowedFrequency > 0 && !empty($lastVote)) {

            if ($allowedFrequency === self::ALLOWED_ONLY_ONCE_PER_24 && time() - $lastVote['time'] < 86400) {
                print 'Sorry, you can not vote for this type of object today, please come back.';
                HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
            } elseif ($allowedFrequency === self::ALLOWED_ONLY_ONCE) {
                print 'Sorry, you can vote only once for this type of object.';
                HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
            }
        }
    }

    /**
     * @return VoteRepository
     */
    protected function getVoteRepository()
    {
        return $this->getObjectManager()->get(VoteRepository::class);
    }

    /**
     * @return VotedObjectRepository
     */
    protected function getVotedObjectRepository()
    {
        return $this->getObjectManager()->get(VotedObjectRepository::class);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return GeneralUtility::makeInstance(UserService::class);
    }

    /**
     * @return ContentElementService
     */
    protected function getContentElementService()
    {
        return GeneralUtility::makeInstance(ContentElementService::class);
    }

}
