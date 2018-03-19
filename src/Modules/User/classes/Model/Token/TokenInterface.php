<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace User\Model\Token;

/**
 *
 * @author JackRabbit
 */
interface TokenInterface
{
    public function get($token);
    
    public function set($data, $cond);
    
    public function delete($token);
    
    public function is_unique($token);
    
    public function gc($lifetime);
}
