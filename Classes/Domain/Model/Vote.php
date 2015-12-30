<?php
namespace Visol\Votable\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Voting
 */
class Vote extends AbstractEntity
{

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $user;

    /**
     * @var \Visol\Votable\Domain\Model\Voting
     */
    protected $voting;

    /**
     * @var int
     */
    protected $votedObject;

    /**
     * @var string
     */
    protected $time = '';

    /**
     * @var string
     */
    protected $ip = '';

}