<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 31.10.18
 * Time: 22:56
 */

$app->post('/login', 'UserController:login')
    ->add($mwCheckJson);

$app->post('/register', 'UserController:register')
    ->add($mwCheckJson);

$app->get('/me', 'UserController:me')
    ->add($mwCheckAuth);

$app->get('/logout', 'UserController:logout')
    ->add($mwCheckAuth);
