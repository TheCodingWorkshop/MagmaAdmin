<?php
/*
 * This file is part of the MagmaCore package.
 *
 * (c) Ricardo Miller <ricardomiller@lava-studio.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace MagmaAdmin\Backend\Controller;

use MagmaCore\Auth\Controller\AuthenticatedController;
use MagmaCore\Auth\Authorized;

class AdminController extends AuthenticatedController
{

    /**
     * Extends the base constructor method. Which gives us access to all the base 
     * methods inplemented within the base controller class.
     * Class dependency can be loaded within the constructor by calling the 
     * container method and passing in an associative array of dependency to use within
     * the class
     *
     * @param array $routeParams
     * @return void
     * @throws BaseInvalidArgumentException
     */
    public function __construct(array $routeParams)
    {
        parent::__construct($routeParams);
        /**
         * Dependencies are defined within a associative array like example below
         * [ userModel => \App\Model\UserModel::class ]. Where the key becomes the 
         * property for the userModel object like so $this->userModel->getRepo();
         */
        $this->container(
            [
                "tableGrid" => \MagmaCore\Datatable\Datatable::class /* Global access */
            ]
        );
    }

    /**
     * Before filter which is called before every controller
     * method. Use to check is user as privileges to be in the backend
     * or use to log data on requesting of methods
     *
     * @return void
     */
    protected function before()
    {
        parent::before(); /* simple ineriting the loginRequired method */
        /* But this the admin section we are going to be super strict with access */
        $authorized = Authorized::grantedUser();
        if (!empty($authorized) && $authorized !=null) {
            $authorized = (object)$authorized;
            if (!$authorized->hasPrivilege('all')) {
                header('HTTP/1.1 403 Forbidden');
                $this->flashMessage('You are not allowed to access that resource.', $this->flashWarning());
                $this->redirect('/login');
            }
        }
    }

    /**
     * After filter which is called after every controller. Can be used
     * for garbage collection
     *
     * @return void
     */
    protected function after()
    {}

}