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
     * @var int
     */
    protected $user;

    /**
     * @var \Visol\Votable\Domain\Model\VotedObject
     */
    protected $votedObject;

    /**
     * @var int
     */
    protected $value = '';

    /**
     * @var int
     */
    protected $time = '';

    /**
     * @var string
     */
    protected $ip = '';

    /**
     * @var int
     */
    protected $pid = '';

    /**
     * @return int
     */
    public function getUser()
    {
        return (int)$this->user;
    }

    /**
     * @param int $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return VotedObject
     */
    public function getVotedObject()
    {
        return $this->votedObject;
    }

    /**
     * @param VotedObject $votedObject
     * @return $this
     */
    public function setVotedObject($votedObject)
    {
        $this->votedObject = $votedObject;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return (int)$this->value;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return (int)$this->time;
    }

    /**
     * @param int $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return (string)$this->ip;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return (int)$this->pid;
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'user' => $this->getUser(),
            'value' => $this->getValue(),
            'time' => $this->getTime(),
            'ip' => $this->getIp(),
            'pid' => $this->getPid(),
            'item' => 1,
        ];
    }

}