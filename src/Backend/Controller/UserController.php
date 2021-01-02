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

use MagmaAdmin\Backend\Controller\AdminController;
use MagmaCore\Utility\Yaml;
use LoaderError;
use RuntimeError;
use SyntaxError;

class UserController extends AdminController
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
                "repository" => \MagmaCore\Auth\Model\UserModel::class,
                "column" => \MagmaAdmin\Backend\DataColumns\UserColumn::class
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
        parent::before();
    }

    /**
     * After filter which is called after every controller. Can be used
     * for garbage collection
     *
     * @return void
     */
    protected function after()
    {}


    /**
     * Entry method which is hit on request. This method should be implement within
     * all sub controller class as a default landing point when a request is 
     * made.
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function indexAction()
    { 
        $args = Yaml::file('controller')[$this->thisRouteController()];

        $repository = $this
        ->repository
        ->getRepo()
        ->findWithSearchAndPaging($this->request->handler(), $args);
        $tableData = $this->tableGrid->create($this->column, $repository)->table();
        $this->render(
            'admin/user/index.html.twig',
            [
                "table" => $tableData
            ]
        );
    }

    public function newAction()
    {}

}