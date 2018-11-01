<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 01.11.18
 * Time: 23:28
 */

namespace ImageUploadingService\Repositories;

use \RedBeanPHP\R as R;
use ImageUploadingService\Models\User;

/**
 * Class UserRepository
 * @package ImageUploadingService\Repositories
 */
class UserRepository
{

    /**
     * @var array|\RedBeanPHP\OODBBean
     */
    private $dbUser;

    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $this->dbUser = R::dispense('users');
    }

    /**
     * @param User $user
     * @return int|string
     */
    public function addUser($user)
    {
        $this->dbUser->password = $user->password;
        $this->dbUser->name = $user->name;
        $this->dbUser->email = $user->email;
        $id = R::store($this->dbUser);
        return $id;
    }

    /**
     * @param $email
     * @return int
     */
    public function getUsersCountByEmail($email)
    {
        return R::count('users', 'email = ?', array($email));
    }

    /**
     * @param $email
     * @param $pass
     * @return NULL|\RedBeanPHP\OODBBean
     */
    public function findUser($email, $pass)
    {
        return R::findOne('users', 'email = ? AND password = ?', array($email, $pass));
    }

    /**
     * @return \RedBeanPHP\OODBBean
     */
    public static function getUserBean()
    {
        $userBean = R::load('users', $_SESSION['id']);
        return $userBean;
    }

    /**
     * @param array<int> $ids
     * @return array|\RedBeanPHP\OODBBean
     */
    public function getUsers($ids=null)
    {
        $query = 'SELECT users.id, users.name, users.email, count(d.id) AS documents_count
                  FROM users LEFT JOIN documents AS d ON users.id = d.users_id'
            . ($ids !== null ? ' WHERE users.id IN (?) ' : ' ') .
            'GROUP BY users.id
                  ORDER BY users.id';
        $users = $ids !== null ? R::getAll($query, [implode(',', $ids)]) : R::getAll($query);
        return $users;
    }

    /**
     * @param int $shift
     * @param int $pageSize
     * @return array|\RedBeanPHP\OODBBean
     */

    public function getUsersWithLimit($shift, $pageSize)
    {
        $users = R::getAll(
            'SELECT users.id, users.name, users.email, count(d.id) AS documents_count
                  FROM users LEFT JOIN documents AS d ON users.id = d.users_id
                  GROUP BY users.id
                  ORDER BY users.id
                  LIMIT ?, ? ', [$shift, $pageSize]);
        return $users;
    }

}
