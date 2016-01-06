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


/**
 * Voting
 */
class VotedObject
{

    /**
     * @var int
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $relationalFieldName = 'votes';

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getRelationalFieldName()
    {
        return $this->relationalFieldName;
    }

    /**
     * @param string $relationalFieldName
     * @return $this
     */
    public function setRelationalFieldName($relationalFieldName)
    {
        $this->relationalFieldName = $relationalFieldName;
        return $this;
    }

}