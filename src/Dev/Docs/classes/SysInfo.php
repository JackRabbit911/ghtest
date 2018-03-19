<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs;

/**
 * Description of sysInfo
 *
 * @author JackRabbit
 */

use Core\Core;

class SysInfo
{
    public static function modules()
    {
        $i = 0;
        $modules = [];
        foreach(Core::paths() AS $path=>$namespace)
        {
            $module_name = (string) substr($path, strlen(SRCPATH));
            $modules[$i]['link'] = mb_strtolower(str_replace('\\', '_', $module_name));
            $modules[$i]['module'] = $module_name;
            $modules[$i]['path'] = (string)substr($path, strlen(DOCROOT));
            $modules[$i]['namespace'] = $namespace;
            $i++;
        }
        return $modules;
    }
}
