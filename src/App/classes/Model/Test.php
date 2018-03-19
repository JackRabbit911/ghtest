<?php
namespace App\Model;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Core\Model;
/**
 * Description of Test
 *
 * @author JackRabbit
 */
class Test extends Model\Model
{
    public function get()
    {
        return $this->db->table('users')->getAll();
    }
}
