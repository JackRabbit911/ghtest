<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Controller;

/**
 * Description of ContollerInterface
 *
 * @author JackRabbit
 */
interface ContollerInterface
{
    public static function set_global($key, $value=NULL);
    public function execute($action='index');
//    public function _before();
//    public function _after();
}
