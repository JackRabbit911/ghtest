<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs;

/**
 * Description of CodeBlock
 *
 * @author JackRabbit
 */

//use Core\Helper\Text;

class CodeBlock 
{
    public static function source($file, $start, $end)
    {
        if ( ! $file) return FALSE;
 
        $file = file($file, FILE_IGNORE_NEW_LINES);

        $file = array_slice($file, $start - 1, $end - $start + 1);

        if (preg_match('/^(\s+)/', $file[0], $matches))
        {
            $padding = strlen($matches[1]);

            foreach ($file as & $line)
            {
                $line = substr($line, $padding);
            }
        }

        return implode("\n", $file);
    }
}
