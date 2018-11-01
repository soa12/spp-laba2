<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 01.11.18
 * Time: 23:14
 */

namespace ImageUploadingService\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ImageUploadingService\Models\PageInfo;
use ImageUploadingService\Models\User;
use ImageUploadingService\Repositories\UserRepository;
use ImageUploadingService\Validation\Validator;
use ImageUploadingService\Models\Response;
use Respect\Validation\Validator as v;

/**
 * Class UserController
 * @package ImageUploadingService\Controllers
 */
class UserController
{

    /**
     * @var User
     */
    private $user;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var Response
     */
    private $resp;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->validator = new Validator();
        $this->user = new User();
        $this->resp = new Response();
        $this->repository = new UserRepository();
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function register($request, $response)
    {
        $data = json_decode($request->getBody(), TRUE);

        $validationErrors = $this->validator->validate($data, [
            'email' => v::notEmpty()->email(),
            'name' => v::notEmpty()->noWhitespace(),
            'password' => v::notEmpty()->length(8, 20)->regex('/(?=.*[a-z])(?=.*[0-9])/')
        ]);

        if (!empty($validationErrors)) {
            $answer = $this->resp->setResponse('400', $validationErrors);
        } else {
            if ($this->repository->getUsersCountByEmail($data['email']) == 0) {
                $this->user->email = $data['email'];
                $this->user->password = md5($data['password']);
                $this->user->name = $data['name'];

                $this->user->id = $this->repository->addUser($this->user);

                $_SESSION['isLogin'] = TRUE;
                $_SESSION['id'] = $this->user->id;

                $answer = $this->resp->setResponse('200', $this->user->email);
            } else {
                $answer = $this->resp->setResponse('400', 'Пользователь с таким почтовым адресом уже зарегистрирован');
            }
        }

        return $response->withJson($answer)->withHeader('Access-Control-Allow-Origin', '*');
    }

    /**
     * @return mixed
     */
    public function checkUser()
    {
        return $_SESSION['isLogin'];
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function login($request, $response)
    {
        if ($this->checkUser()) {
            $answer = $this->resp->setResponse('400', 'Вы уже авторизованы. Пожалуйста, завершите текущий сеанс перед тем, как начать новый');
            return $response->withJson($answer);
        }

        $data = json_decode($request->getBody(), TRUE);

        $validationErrors = $this->validator->validate($data, [
            'email' => v::notEmpty(),
            'password' => v::notEmpty()
        ]);

        if (!empty($this->validationErrors)) {
            $answer = $this->resp->setResponse('400', $validationErrors);
            return $response->withJson($answer);
        }

        $this->user = $this->repository->findUser($data['email'], md5($data['password']));

        if (empty($this->user)) {
            $answer = $this->resp->setResponse('400', 'Пользователь не найден. Проверьте правильность введённых данных');
        } else {
            $_SESSION['isLogin'] = TRUE;
            $_SESSION['id'] = $this->user->id;
            $answer = $this->resp->setResponse('200', $this->user->email);
        }
        return $response->withJson($answer);
    }

    public function me($request, $response)
    {
        $user = $this->repository->getUsers([$_SESSION['id']])[0];
        $answer = $this->resp->setResponse('200', $user);

        return $response->withJson($answer);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function logout($request, $response)
    {
        if ($this->checkUser()) {
            session_unset();
            $answer = $this->resp->setResponse('200', 'Успешный выход');
        } else {
            $answer = $this->resp->setResponse('400', 'Вы не авторизованы');
        }

        return $response->withJson($answer);
    }

}
