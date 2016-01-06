<?php
namespace Visol\Votable\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\Repository;
use Visol\Votable\Domain\Model\Vote;


/**
 * VotedObjectRepository
 */
class VotedObjectRepository extends Repository
{

    /**
     * @param Vote $vote
     * @return array
     *
     */
    public function findOne(Vote $vote)
    {
        $votedObject = $vote->getVotedObject();
        $clause = 'uid = ' . $votedObject->getIdentifier();
        $clause .= $this->getPageRepository()->enableFields($votedObject->getContentType());
        $clause .= $this->getPageRepository()->deleteClause($votedObject->getContentType());
        $object = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $votedObject->getContentType(), $clause);

        return is_array($object) ? $object : [];
    }
    /**
     * @param string $contentType
     * @param int $userIdentifier
     * @return array
     */
    public function findFor($contentType, $userIdentifier)
    {
        $clause = sprintf(
            'tablenames = "%s" AND fieldname = "%s" AND uid_local IN (SELECT uid FROM tx_votable_domain_model_vote WHERE user = %s)',
            $contentType,
            'votes',
            $userIdentifier
        );

        $records = $this->getDatabaseConnection()->exec_SELECTgetRows('*', 'tx_votable_vote_record_mm', $clause);
        return is_array($records) ? $records : [];
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }
}