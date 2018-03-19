<?php
namespace UnitTests;
require_once SRCPATH.'Dev'.DIRECTORY_SEPARATOR.'UnitTests'.DIRECTORY_SEPARATOR.'phpunit-5.7.21.phar';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use PHPUnit\Framework\TestCase;
use Core\Helper\Arr;
/**
 * Description of ArrTests
 *
 * @author JackRabbit
 */
class ArrTests extends TestCase
{
    public static function testGet()
    {
        
//        $array = 
        $result = Arr::get(['k1'=>'v1', 'k2'=>'v2'], 'k2');
        self::assertEquals('v2', $result);
        
        return $result;
    }
    
    
    
}
