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
 * VoteRepository
 */
class VoteRepository extends Repository
{

    /**
     * @param Vote $vote
     */
    public function add($vote)
    {

    }

    /**
     * @param Vote $vote
     */
    public function update($vote)
    {
        $sql = 'UPDATE tx_easyvotecompetition_domain_model_participation AS participation
			LEFT JOIN (
				SELECT participation, COUNT(*) number_of_votes
				FROM  tx_easyvotecompetition_domain_model_vote
				WHERE deleted = 0 AND hidden = 0
				GROUP BY participation
				) AS vote
			ON participation.uid = vote.participation
			SET cached_votes = CASE
				WHEN vote.number_of_votes IS NULL THEN 0
				WHEN vote.number_of_votes > 0 THEN number_of_votes
			END
			WHERE participation.competition = ' . $competition->getUid() . '
			AND participation.deleted = 0 AND participation.hidden = 0 AND participation.disabled = 0';
        $this->getDatabaseConnection()->sql_query($sql);

        /* http://stackoverflow.com/a/14297055/1517316 */
        $this->getDatabaseConnection()->sql_query('SET @prev_value = NULL;');
        $this->getDatabaseConnection()->sql_query('SET @rank_count = 0;');
        $sql = '
			UPDATE tx_easyvotecompetition_domain_model_participation
			SET cached_rank = CASE
				WHEN @prev_value = cached_votes THEN @rank_count
				WHEN @prev_value := cached_votes THEN @rank_count := @rank_count + 1
				ELSE @rank_count := @rank_count + 1
			END
			WHERE competition = ' . $competition->getUid() . ' AND deleted = 0 AND hidden = 0 AND disabled = 0
			ORDER BY cached_votes DESC';
        $this->getDatabaseConnection()->sql_query($sql);

        /* Compare cached votes with calculated votes
            SELECT uid,title,cached_votes,cached_rank,calculated_votes FROM `tx_easyvotecompetition_domain_model_participation` AS participation
            LEFT JOIN
            (
                SELECT participation, COUNT(*) calculated_votes
                FROM  tx_easyvotecompetition_domain_model_vote
                WHERE deleted = 0 AND hidden = 0
                GROUP BY participation
            )  vote ON participation.uid = vote.participation ORDER by cached_rank
         */
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
}