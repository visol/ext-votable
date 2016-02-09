<?php
namespace Visol\Votable\TypeConverter;

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
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Visol\Votable\Domain\Model\Vote;
use Visol\Votable\Domain\Model\VotedObject;
use Visol\Votable\Service\ContentElementService;
use Visol\Votable\Service\UserService;

/**
 * Convert a content identifier into a Content object.
 */
class VoteConverter extends AbstractTypeConverter
{

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('int');

    /**
     * @var string
     */
    protected $targetType = 'Visol\Votable\Domain\Model\Vote';

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @var \Visol\Votable\Domain\Repository\VoteRepository
     * @inject
     */
    protected $voteRepository;

    /**
     * Actually convert from $source to $targetType
     *
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @throws \Exception
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     * @return Vote
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL)
    {
        /** @var Vote $vote */
        $vote = $this->objectManager->get(Vote::class);

        $vote->setUser($this->getUserService()->getUserIdentifier());
        $vote->setIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        $vote->setTime(time());
        $vote->setValue((int)GeneralUtility::_GP('value'));
        $vote->setPid($this->getContentElementPid());

        /** @var VotedObject $votedObject */
        $votedObject = $this->objectManager->get(VotedObject::class);
        $votedObject->setIdentifier((int)$source);
        $votedObject->setContentType($this->getContentType());
        $vote->setVotedObject($votedObject);

        // Retrieve uid
        $voteData = $this->voteRepository->getVoteData($vote);
        if (array_key_exists('uid', $voteData)) {
            $vote->_setProperty('uid', (int)$voteData['uid']);
        }

        return $vote;
    }

    /**
     * @return string
     */
    protected function getContentType()
    {
        // Check the content element that contains the voting meta information.
        $contentElementIdentifier = (int)GeneralUtility::_GP('contentElement');
        $settings = $this->getContentElementService()->getSettings($contentElementIdentifier);
        return $settings['contentType'];
    }

    /**
     * @return int
     */
    protected function getContentElementPid()
    {
        // Check the content element that contains the voting meta information.
        $contentElementIdentifier = (int)GeneralUtility::_GP('contentElement');
        $contentElement = $this->getContentElementService()->get($contentElementIdentifier);
        return (int)$contentElement['pid'];
    }

    /**
     * @return ContentElementService
     */
    protected function getContentElementService()
    {
        return GeneralUtility::makeInstance(ContentElementService::class);
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->objectManager->get(UserService::class);
    }
}