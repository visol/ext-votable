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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Visol\Votable\Domain\Model\Vote;
use Visol\Votable\Hook\RankCacheHookInterface;


/**
 * VoteRepository
 */
class VoteRepository extends Repository
{
    /**
     * @var string
     */
    protected $tableName = 'tx_votable_domain_model_vote';

    /**
     * @param Vote $vote
     */
    public function add($vote)
    {
        $values = $vote->toArray();
        $values['tstamp'] = time();
        $values['crdate'] = time();

        $this->getDatabaseConnection()->exec_INSERTquery($this->tableName, $values);
        $vote->_setProperty('uid', $this->getDatabaseConnection()->sql_insert_id());

        // Establish relation between $vote and the voted object.
        $relation['uid_local'] = $vote->getUid();
        $relation['uid_foreign'] = $vote->getVotedObject()->getIdentifier();
        $relation['tablenames'] = $vote->getVotedObject()->getContentType();
        $relation['fieldname'] = $vote->getVotedObject()->getRelationalFieldName();
        $this->getDatabaseConnection()->exec_INSERTquery('tx_votable_vote_record_mm', $relation);

        // Post process data
        $this->keepCleanRelationalTable();
        $this->cacheNumberOfVotes($vote);
        $this->cacheRank($vote);
    }

    /**
     * @param Vote $vote
     */
    public function remove($vote)
    {

        $voteIdentifier = $vote->getUid();
        $this->getDatabaseConnection()->exec_DELETEquery($this->tableName, 'uid = ' . $voteIdentifier);

        // Post process data
        $this->keepCleanRelationalTable();
        $this->cacheNumberOfVotes($vote);
        $this->cacheRank($vote);
    }

    /**
     * @param Vote $vote
     * @return bool
     */
    public function exists(Vote $vote)
    {
        $record = $this->getVoteData($vote);
        return !empty($record);
    }

    /**
     * @param Vote $vote
     * @return array
     */
    public function getVoteData(Vote $vote)
    {
        $clause = sprintf(
            'user = %s AND uid IN (SELECT uid_local FROM tx_votable_vote_record_mm WHERE uid_foreign = %s AND tablenames = "%s" AND fieldname = "%s")',
            $vote->getUser(),
            $vote->getVotedObject()->getIdentifier(),
            $vote->getVotedObject()->getContentType(),
            $vote->getVotedObject()->getRelationalFieldName()
        );

        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->tableName, $clause);

        return is_array($record) ? $record : [];
    }

    /**
     * @param string $contentType
     * @param int $userIdentifier
     * @return array
     */
    public function findLastVote($contentType, $userIdentifier)
    {
        $clause = sprintf(
            'user = %s AND uid IN (SELECT uid_local FROM tx_votable_vote_record_mm WHERE tablenames = "%s" AND fieldname = "%s")',
            $userIdentifier,
            $contentType,
            'votes'
        );

        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->tableName, $clause, '', 'time DESC');

        return is_array($record) ? $record : [];
    }

    /**
     * @return void
     */
    protected function keepCleanRelationalTable()
    {
        $sql = 'DELETE FROM tx_votable_vote_record_mm WHERE uid_local NOT IN (SELECT uid FROM tx_votable_domain_model_vote);';
        $this->getDatabaseConnection()->sql_query($sql);
    }

    /**
     * @param Vote $vote
     */
    protected function cacheNumberOfVotes(Vote $vote)
    {
        $sql = sprintf(
            'UPDATE %s SET %s = (SELECT count(*)
                FROM   tx_votable_vote_record_mm
                WHERE  uid_foreign = %s
                       AND tablenames = "%s"
                       AND fieldname = "%s")
WHERE  uid = %s;',

            $vote->getVotedObject()->getContentType(),
            $vote->getVotedObject()->getRelationalFieldName(),
            $vote->getVotedObject()->getIdentifier(),
            $vote->getVotedObject()->getContentType(),
            $vote->getVotedObject()->getRelationalFieldName(),
            $vote->getVotedObject()->getIdentifier()
        );

        $this->getDatabaseConnection()->sql_query($sql);
    }

    /**
     * @param Vote $vote
     */
    protected function cacheRank(Vote $vote)
    {
        /* http://stackoverflow.com/a/14297055/1517316 */
        $this->getDatabaseConnection()->sql_query('SET @prev_value = NULL;');
        $this->getDatabaseConnection()->sql_query('SET @rank_count = 0;');
        $sql = sprintf(
            'UPDATE %s
			SET %s = CASE
				WHEN @prev_value = votes THEN @rank_count
				WHEN @prev_value := votes THEN @rank_count := @rank_count + 1
				ELSE @rank_count := @rank_count + 1
			END
			%s
			ORDER BY %s DESC',
            $vote->getVotedObject()->getContentType(),
            'rank',
            $this->getPossibleWhereClause($vote),
            $vote->getVotedObject()->getRelationalFieldName()
        );


        $this->getDatabaseConnection()->sql_query($sql);
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
     * @param Vote $vote
     * @return string
     */
    protected function getPossibleWhereClause($vote)
    {
        $possibleWhereClause = '';

        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['votable']['rankCacheWhereClause'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['votable']['rankCacheWhereClause'] as $key => $classRef) {

                /** @var RankCacheHookInterface $object */
                $object = GeneralUtility::getUserObj($classRef);
                $possibleWhereClause = $object->getPossibleWhereClause($possibleWhereClause, $vote);
            }
        }

        // Add possible WHERE keyword.
        if (!empty($possibleWhereClause)) {
            $possibleWhereClause = 'WHERE ' . $possibleWhereClause;
        }
        return $possibleWhereClause;
    }
}