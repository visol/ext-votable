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
use Visol\Votable\Domain\Repository\VoteRepository;

/**
 * Validate whether the vote exists
 */
class VoteDoesNotExistValidator extends AbstractValidator
{

    /**
     * Check whether the vote exists or not.
     *
     * @param Vote $vote
     * @return void
     */
    public function isValid($vote)
    {
        if ($this->getVoteRepository()->exists($vote)) {
            print 'Sorry, a vote already exists for this object.';
            HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_403);
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
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

}
