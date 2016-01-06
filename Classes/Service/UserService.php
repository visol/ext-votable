<?php
namespace Visol\Votable\Service;

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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Service related to the User.
 */
class UserService implements SingletonInterface
{

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return !empty($this->getFrontendUser()->user);
    }

    /**
     * @return int
     */
    public function getUserIdentifier()
    {
        return empty($this->getUserData()) ? 0 : $this->getUserData()['uid'];
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        $userData = $this->getFrontendUser()->user;
        return $this->isAuthenticated() ? $userData : array();
    }

}
