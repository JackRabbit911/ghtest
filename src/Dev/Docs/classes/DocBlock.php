<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Docs;

/**
 * Description of DocBlock
 *
 * @author JackRabbit
 */

use Core\Helper\Text;
use Core\Helper\HTML;
use Core\Route;

class DocBlock 
{
//    public static $regex_class_member = '((\w++)(?:::(\$?\w++))?(?:\(\))?)';
    public static $regex_class_member = '(([a-zA-Z_0-9\\\]++)(?:::(\$?\w++))?(?:\(\))?)';
    
    public static function parse($comment, $html = TRUE)
    {
        // Normalize all new lines to \n
        $comment = str_replace(array("\r\n", "\n"), "\n", $comment);

        // Split into lines while capturing without leading whitespace
        preg_match_all('/^\s*\* ?(.*)\n/m', $comment, $lines);

        // Tag content
        $tags = array();

        /**
         * Process a tag and add it to $tags
         *
         * @param   string  $tag    Name of the tag without @
         * @param   string  $text   Content of the tag
         * @return  void
         */
        $add_tag = function ($tag, $text) use ($html, & $tags)
        {
            // Don't show @access lines, they are shown elsewhere
            if ($tag !== 'access')
            {
                if ($html)
                {
                    $text = self::format_tag($tag, $text);
                }

                // Add the tag
                $tags[$tag][] = $text;
            }
        };

        $comment = $tag = NULL;
        $end = count($lines[1]) - 1;

        foreach ($lines[1] as $i => $line)
        {
            // Search this line for a tag
            if (preg_match('/^@(\S+)\s*(.+)?$/', $line, $matches))
            {
                if ($tag)
                {
                    // Previous tag is finished
                    $add_tag($tag, $text);
                }

                $tag = $matches[1];
                $text = isset($matches[2]) ? $matches[2] : '';

                if ($i === $end)
                {
                    // No more lines
                    $add_tag($tag, $text);
                }
            }
            elseif ($tag)
            {
                // This is the continuation of the previous tag
                $text .= "\n".$line;

                if ($i === $end)
                {
                    // No more lines
                    $add_tag($tag, $text);
                }
            }
            else
            {
                $comment .= "\n".$line;
            }
        }

//        $comment = trim($comment, "\n");

        if ($comment AND $html)
        {
            // Parse the comment with Markdown
            
            $comment = nl2br($comment);
            
            $comment = Text::markdown($comment);
            
//            echo $comment;
        }
        else $comment = trim($comment, "\n");

        return array($comment, $tags);
    }

    public static function format_tag($tag, $text)
    {
        if ($tag === 'license')
        {
            if (strpos($text, '://') !== FALSE)
                return HTML::anchor($text);
        }
        elseif ($tag === 'link')
        {
            $split = preg_split('/\s+/', $text, 2);

            return HTML::anchor(
                $split[0],
                isset($split[1]) ? $split[1] : $split[0],
                    array('target'=>'_blank')
            );
        }
        elseif ($tag === 'copyright')
        {
            // Convert the copyright symbol
            return str_replace('(c)', '&copy;', $text);
        }
        elseif ($tag === 'throws')
        {
            
//            return $text;
            
            return ClassApi::getLink($text);
            
            
            
            $route = Route::get('docs/api');

            if (preg_match('/^(\w+)\W(.*)$/D', $text, $matches))
            {
//                return HTML::anchor(
//                    $route->uri(array('class' => $matches[1])),
//                    $matches[1]
//                ).' '.$matches[2];
                
                return implode(' - ', $matches).'**'.$text.'**';
                
//                return $route->uri(array('class' => $matches[1]));
            }

            return HTML::anchor(
                $route->uri(array('class' => $text, 'module1'=>'core')),
                $text
            );
        }
        elseif ($tag === 'see' OR $tag === 'uses')
        {
            if (preg_match('/^'.self::$regex_class_member.'/', $text, $matches))
            {
//                return implode(' - ', $matches);
                $postfix = self::link_class_member($matches);
                return ClassApi::getLink($matches[2], $postfix);
            }
            return implode(' - ', $matches);
//            return $text;
        }

        return $text;
    }
    
    /**
     * Get the source of a function
     *
     * @param  string   the filename
     * @param  int      start line?
     * @param  int      end line?
     */
    public static function source($file, $start, $end)
    {
            if ( ! $file) return FALSE;

            $file = file($file, FILE_IGNORE_NEW_LINES);
            
//            $file = highlight_file($file, TRUE);

            $file = array_slice($file, $start - 1, $end - $start + 1);

//            if (preg_match('/^(\s+)/', $file[0], $matches))
//            {
//                    $padding = strlen($matches[1]);
//
//                    foreach ($file as & $line)
//                    {
//                            $line = substr($line, $padding);
//                    }
//            }

            return htmlspecialchars(implode("\n", $file), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Make a class#member API link using an array of matches from [Kodoc::$regex_class_member]
     *
     * @param   array   $matches    array( 1 => link text, 2 => class name, [3 => member name] )
     * @return  string
     */
    public static function link_class_member($matches)
    {
        $link = $matches[1];
        $class = $matches[2];
        $member = NULL;

        if (isset($matches[3]))
        {
                // If the first char is a $ it is a property, e.g. Core::$base_url
                if ($matches[3][0] === '$')
                {
                        $member = '#property:'.substr($matches[3], 1);
                }
                elseif (preg_match('/^[A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*$/', $matches[3]))
                {
                        $member = '#constant:'.substr($matches[3],0);
                }
                else
                {
                        $member = '#'.$matches[3];
                }
                
            return ['anchor'=>$member, 'text'=>$matches[1]];
        }
        
        return NULL;
        
//        return ['anchor'=>$member, 'text'=>$matches[1]];
        
//        $route = Route::current();              
//        return HTML::anchor($route->uri(array('class' => $class, 'module1'=>'core')).$member, $link, NULL, NULL, TRUE);
    }
        
}
