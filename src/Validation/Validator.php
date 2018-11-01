<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 01.11.18
 * Time: 23:46
 */

namespace ImageUploadingService\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{

    protected $errors;
    protected $messages;
    protected $exMessages;

    public function __construct()
    {
        $this->errors = array();
        $this->messages = array();
        $this->exMessages = array(
            'email' => array(
                'email' => 'Электронный адрес должен быть в формате name@email.ab',
                'notEmpty' => 'Поле email не должно быть пустым'
            ),
            'name' => array(
                'notEmpty' => 'Поле Имя не должно быть пустым',
                'noWhitespace' => 'В Имени не должно быть пробелов',
            ),
            'password' => array(
                'notEmpty' => 'Пароль не должен быть пустым',
                'length' => 'Длина пароля должна быть не менее 8 символов',
                'regex' => 'Пароль должен содержать буквы и цифры'
            )
        );
    }

    public function validate($data, $rules)
    {
        foreach ($rules as $key => $rule) {
            try {
                $rule->assert($data[$key]);
            } catch (NestedValidationException $ex) {
                $messages = $ex->findMessages($this->exMessages[$key]);
                foreach ($messages as $message) {
                    if ($message)
                        $this->errors[] = $message;
                }
            }
        }
        return $this->errors;
    }

}
