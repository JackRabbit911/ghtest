<?
namespace Core;
/**
 * Internationalization (i18n) class. Provides language loading and translation
 * methods without dependencies on [gettext](http://php.net/gettext).
 *
 * Typically this class would never be used directly, but used via the __()
 * function, which loads the message and replaces parameters:
 *
 *     // Display a translated message
 *     echo __('Hello, world');
 *
 *     // With parameter replacement
 *     echo __('Hello, :user', array(':user' => $username));
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
//define('I18N_URI', 1);
//define('I18N_QUERY', 2);
//define('I18N_SUBDOMAIN', 3);
//define('I18N_COOKIE', 4);
//define('I18N_SESSION', 5);
//define('I18N_BROWSER', 7); 

//use Core\Core;
use Core\Exception;
use Core\Helper\Arr;

class I18n
{
    const URI = 1;
    const QUERY = 2;
    const SUBDOMAIN = 3;
    const COOKIE = 4;
    const SESSION = 5;
    const BROWSER = 7;

    const LANG_SEGMENT_2_EMPTY = 1;
    const EMPTY_2_LANG_SEGMENT = 2;


//    protected static $method_enable = 'url';
	/**
	 * @var  string   target language: en-us, es-es, zh-cn, etc
	 */
//	public static $lang = 'en-us';

	/**
	 * @var  string  source language: en-us, es-es, zh-cn, etc
	 */
//	public static $source = 'en-us';

	/**
	 * @var  array  cache of loaded languages
	 */
	protected static $_cache = array();
        
        
        public static $base_lang = 'en';
        public static $current_lang = 'en';
        public static $detect_lang_method = FALSE;
        public static $direct = 1;// self::LANG_SEGMENT_2_EMPTY;
        public static $is_detect_lang_from_browser = TRUE;
        


        protected static $stored_uri;
        
//        protected static $_langs = [
//            'en-us'=>['en-us', 'English'],
//            'en'=>['en-us', 'English'],
//            'ru-ru'=>['ru-Ru', 'Русский'],
//            'ru-Al'=>['ru-Al', 'Олбанцкей'],
//            'ru-Su'=>['ru-Su', 'Советский'],
//            'ru'=>['ru-Ru', 'Русский'],
////            'de'=>['de-De', 'Deutch'],
//            
//            
//            ];
        
//        public static function detect_lang_from_uri(Request $request)
//        {
//            $uri = $request->uri;
//            $langs = self::$_langs;
//            
//            if(!empty($langs))
//            {
//                foreach($langs AS $key=>$arr)
//                {
//                    $lang_segment = '/'.strtolower($key);
//                    
//                    if(stripos($uri, $lang_segment) === 0)
//                    {
////                        self::$current_lang = $key;
//                        
//                        $uri = (string) substr($uri, strlen($lang_segment));
//                        
//                        $request->uri = $uri;
////                        return $uri;
//                        return $key;
//                    }
//                }
//                return NULL;
//            }
//            return NULL;
//        }
        
       
        
        public static function _detect_lang_from_uri(Request $request, $delimeter='/')
        {
            $uri = $request->uri;
            $uri = ltrim($uri, '/');
            $arr = explode($delimeter, $uri);
            
            $lang = strtolower(array_shift($arr));
            
            $config = \Core\Config::get('i18n');
            
            $result = (isset($config[$lang])) ? $lang : FALSE;
            
            self::$stored_uri = $request->uri;
            
            if($result !== FALSE)
            {
                
                $request->uri = '/'.implode($delimeter, $arr);
            }
//            else self::$stored_uri = $request->uri;
            
            return $result;
        }
        
        public static function set_current_lang(Request $request)
        {
//            require_once SRCPATH.'Core/i18n/functions.php';
//            exit;
            
//            echo 'REF: '.Arr::get($_SERVER, 'HTTP_REFERER');
//            var_dump($_COOKIE); exit;
            
            $lang = FALSE;
            
            if($request->initial())
            {
                
                
                if(self::$detect_lang_method === FALSE) return NULL;
                elseif(self::$detect_lang_method === I18N::URI) $lang = $request->params('lang');
                elseif(self::$detect_lang_method === I18N::QUERY) $lang = $request->query('lang');
                elseif(self::$detect_lang_method === I18N::SUBDOMAIN) $lang = $request->params('subdomain');
                
//                if($lang) header("Set-Cookie: lang=$lang");
                if($lang) setcookie("lang", $lang, time()+60, '/');
//                
//                var_dump($lang);
//                echo '<br>';
//                var_dump($_COOKIE);
//                echo '<br>';
//                $al = Header::request_headers('Accept-Language');
//                
//                $langs = Header::parse_language_header(Header::request_headers('Accept-Language'));
////                
//                $x = Header::accepts_language_at_quality('ru-RU', FALSE);
//                
//                $y = Header::preferred_language(['ru', 'en', 'es'], FALSE);
//            
//            
//            echo $al;
//             echo '<br>';
//                print_r($langs); 
//            echo '<br>';
//            echo $x;
//             echo '<br>';
//            var_dump($y);
//            exit;
                
//var_dump($lang);
            
                if($lang === FALSE || $lang === NULL)
                {
                    $lang = Arr::get($_COOKIE, 'lang', FALSE);
                    
//                    echo $lang;
//                    
//                    var_dump($_COOKIE); exit;
                    
                    if($lang === FALSE)
//                        $lang = self::_detect_lang_from_browser();
                        $lang = HTTP::preferred_language(['ru-ru', 'en', 'es'], FALSE);
                }
//                else
//                {
//                     setcookie("lang", $lang, time()+60);
////                     var_dump($_COOKIE); exit;
//                }
//                
//                var_dump($_COOKIE);
//                var_dump($lang);
                
//                die($lang);
                
                
            }
//            else
//            {
//                if($request->initial()) $lang = self::_detect_lang_from_browser();
//            }
            
//            var_dump($lang); exit;
            
            if($lang)
            {
                self::$current_lang = $lang;
                
//                die(self::$base_lang.' '.self::$current_lang);
                
                self::redirect($request);
            }
            
//            else
//            {
//                if($request->initial()) $lang = self::_detect_lang_from_browser();
//                self::$current_lang = $lang;
//            }
        }
        
        protected static function _detect_lang_from_browser()
        {
//            $http_str = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
//            if($http_str = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING))
//            {
//
//                $arr = explode(',', $http_str);
//                foreach($arr AS $code_lang)
//                {
//                    $arr_code = explode(';', $code_lang);
//                    $key = Arr::get($arr_code, 1, 'q=1');
//                    $key = strtolower(substr($key, 2));
//                    $langs[$key] = strtolower($arr_code[0]);
//                }
//            }
//            krsort($langs);
            
//            print_r($langs); exit;
            
            if(self::$is_detect_lang_from_browser !== TRUE) return self::$base_lang;
            
            $langs = HTTP::parse_language_header();
            
            
//            var_dump($langs); exit;
            
            $config = \Core\Config::get('i18n');
            
//            return 'en';
//            return FALSE;
            
            foreach($langs AS $accept_lang=>$quality)
            {
                if(isset($config[strtolower($accept_lang)])) return $accept_lang;
//                if($result = self::get_lang_group($config, $accept_lang)) return $result;
                
            }
            
//            print_r($langs);
//            echo '<br>';
//            echo $http_str;
            
//            return 'en-us';
            
            return self::$base_lang;
            
//            return FALSE;
        }
        
//        public static function detect_lang($request)
//        {
//            if(self::$detect_lang_method === 'uri') return self::_detect_lang_from_uri($request);
//            elseif(self::$detect_lang_method === 'subdomain') return self::_detect_lang_from_subdomain($request);
//            elseif(self::$detect_lang_method === 'query') return self::_detect_lang_from_query($request);
//            else return self::$current_lang;
//        }
        
        public static function detect_lang_method($method = NULL)
        {
            if($method === NULL) return self::$detect_lang_method;
            else self::$detect_lang_method = $method;
        }
        
        public static function redirect($request)
        {
            
//            die(HTTP::protocol().'://'.$request->domain().HTTP::$base_url.'/'.self::$current_lang.$request->uri);
//            die(dirname(SRCPATH).' '.$_SERVER['DOCUMENT_ROOT']);
//            die(HTTP::url($request->uri));
            
//            var_dump($_SERVER);
//            echo '<br> request <br>';
//            var_dump($_REQUEST);
//            exit;
           
            
            if(self::$detect_lang_method === self::URI && self::$base_lang == self::$current_lang)
            {
//                die(self::$base_lang.' '.self::$current_lang);
                
                if(self::$direct === self::LANG_SEGMENT_2_EMPTY && self::$stored_uri != $request->uri)
                {
//                    die(self::$modified_uri.'  '.$request->uri);
                    $url = HTTP::url($request->uri); //'http://tetris.test/'.HTTP::$base_url.$request->uri;
//                    die($uri);
//                    header('Referer: /lala'); 
//                    $lang = self::$current_lang;
//                    header("Accept-Language: $lang");
                    header("Location: $url");
                }
                elseif(self::$direct === self::EMPTY_2_LANG_SEGMENT  && self::$stored_uri === $request->uri)
                {
//                    $lang = self::$current_lang;
//                    
//                    die(HTTP::$base_url.'/'.$lang.'/'.$request->uri);
                    
//                    $url = 'http://'.$request->domain().HTTP::$base_url.'/'.self::$current_lang.$request->uri;
                    $url = HTTP::url('/'.self::$current_lang.$request->uri);
                    
//                    die($url);
//                    header('Referer: /lala'); 
                    header("Location: $url");
                }
//                else return;
            }
            elseif(self::$detect_lang_method === self::URI && self::$base_lang !== self::$current_lang)
            {
                if(self::$direct === self::LANG_SEGMENT_2_EMPTY && self::$stored_uri === $request->uri)
                {
//                    $url = 'http://tetris.test/'.HTTP::$base_url.'/'.self::$current_lang.$request->uri;
                    $url = HTTP::url('/'.self::$current_lang.$request->uri);
    //                die($url);
//                    header('Referer: /lala'); 
                    header("Location: $url");
                    
                   
                    
                }
            }
            
            
        }


//        protected static function get_lang_group($config, $lang)
//        {
//            $result = FALSE;
//            if(strlen($lang) === 2)
//            {
//                if(isset($config[$lang]))
//                {
//                    if(!empty($config[$lang]) && is_array($config[$lang]))
//                    {
//                        $result = $lang.'-'.$config[$lang][0];
//                    }
//                }
//            }
//            elseif(strlen($lang) === 5)
//            {
//                $lang1 = substr($lang, 0, 2);
//                $local = ucfirst(substr($lang, 3, 2));
//                if(isset($config[$lang1]))
//                {
//                    if(!empty($config[$lang1]) && is_array($config[$lang1]))
//                    {
//                        if(in_array($local, $config[$lang1]))
//                            $result = $lang;
//                    }
//                    else $result = $lang1;            
//                }
//            }
//            
//            return $result;
//        }
        
        
//        public static function enable($param = NULL)
//        {
//            if($param === NULL) return self::$method_enable;
//            else self::$method_enable = $param;
//        }
        
//        public static function modified_uri($uri)
//        {
//            $langs = self::$_langs;
//            
//            if(!empty($langs))
//            {
////                $uri = str_replace(array('ru', 'en'), '', $uri);
//                foreach($langs AS $key=>$arr)
//                {
//                    $lang_segment = '/'.strtolower($key);
////                    echo $lang_segment;
//                    if(stripos($uri, $lang_segment) === 0)
//                    {
//                        self::$current_lang = $key;
//                        
//                        $uri = (string) substr($uri, strlen($lang_segment));
//                        
//                        if(strtolower(self::$base_lang) === self::$current_lang)
//                        {
//                            header("Location: $uri", TRUE, 301);
//                        }
////                        
//                        return $uri; // (string) substr($uri, strlen($lang_segment));
//                    }
//                }
//            }
////            echo $uri;
////            exit;
//            self::$current_lang = self::$base_lang;
//            return $uri;
//        }


        	/**
	 * Get and set the target language.
	 *
	 *     // Get the current language
	 *     $lang = I18n::lang();
	 *
	 *     // Change the current language to Spanish
	 *     I18n::lang('es-es');
	 *
	 * @param   string  $lang   new language setting
         * @param   boolean $base_lang  base or current language
	 * @return  string
	 * @since   3.0.2
	 */
//	public static function lang($lang = NULL, $base_lang = FALSE)
//	{
//		if ($lang)
//		{
//			// Normalize the language
//			$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
//                        if($base_lang) I18n::$_base_lang = $lang;
//                        else I18n::$_lang = $lang;
//		}
//
//		return ($base_lang) ? I18n::$_base_lang : I18n::$_lang;
//	}
        
        public static function date($value = NULL)
        {
            if($value === NULL) $value = time();
            
            if(ctype_digit($value))
            {
                $format = I18n::l10n(self::$current_lang, 'date');
                $value = date($format, $value);
            }
            
            return $value;
        }
        
        public static function float($value, $decimals=NULL)
        {
            $format = I18n::l10n(self::$current_lang, 'float');
            $arr = explode(' ', $format);
            if(!$decimals)
                $decimals = (isset($arr[0])) ? (int)$arr[0] : 2;

            $dec_point = (isset($arr[1])) ? $arr[1] : '.';
            $thousands_sep = (isset($arr[2])) ? $arr[2] : '';
            return number_format($value, $decimals, $dec_point, $thousands_sep);
        }
        
        public static function currency($value)
        {
            $_format = I18n::l10n(self::$current_lang, 'float');
            $format = I18n::l10n(self::$current_lang, 'currency');
            return self::float($_format, $value, 2).' '.$format;
        }
/******************************************************************************/        
        
//        public static function base_lang($lang = NULL)
//	{
//		if ($lang)
//		{
//			// Normalize the language
//			I18n::$_base_lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
//		}
//
//		return I18n::$_base_lang;
//	}
        

	/**
	 * Returns translation of a string. If no translation exists, the original
	 * string will be returned. No parameters are replaced.
	 *
	 *     $hello = I18n::get('Hello friends, my name is :name');
	 *
	 * @param   string  $string text to translate
	 * @param   string  $lang   target language
	 * @return  string
	 */
	public static function get($string, $lang = NULL)
	{
		if ( ! $lang)
		{
			// Use the global target language
			$lang = self::$current_lang;
		}

		// Load the translation table for this language
		$table = I18n::load($lang);

		// Return the translated string if it exists
		return isset($table[$string]) ? $table[$string] : $string;
	}

	/**
	 * Returns the translation table for a given language.
	 *
	 *     // Get all defined Spanish messages
	 *     $messages = I18n::load('es-es');
	 *
	 * @param   string  $lang   language to load
	 * @return  array
	 */
	public static function load($lang, $dir='i18n')
	{
		if (isset(I18n::$_cache[$dir][$lang]))
		{
			return I18n::$_cache[$dir][$lang];
		}

		// New translation table
		$table = array();

		// Split the language: language, region, locale, etc
		$parts = explode('-', $lang);

		do
		{
			// Create a path for this set of parts
			$path = implode(DIRECTORY_SEPARATOR, $parts);

			if ($files = Core::find_file($path, $dir, NULL, TRUE))
			{
				$t = array();
				foreach ($files as $file)
				{
					// Merge the language strings into the sub table
//					$t = array_merge($t, Core::load($file));
                                        $t = array_merge(include $file, $t);
				}

				// Append the sub table, preventing less specific language
				// files from overloading more specific files
				$table += $t;
			}

			// Remove the last part
			array_pop($parts);
		}
		while ($parts);

		// Cache the translation table locally
		return I18n::$_cache[$dir][$lang] = $table;
	}
        
        public static function l10n($lang, $path=NULL)
	{
		if (isset(I18n::$_cache['l10n'][$lang]))
			$table = I18n::$_cache['l10n'][$lang];
                else
                {
                    $filepath = Core::find_file($lang, 'i18n/l10n');
                    $table = include $filepath;//self::_load($lang, 'i18n/l10n');

                    // Cache the translation table locally
                    I18n::$_cache['l10n'][$lang] = $table;
                }
                
                return ($path) ? Arr::path($table, $path) : $table;
	}
        
        public static function gettext($string, array $values = NULL, $lang = NULL)
	{
            if(!$lang) $lang = I18n::$current_lang;
            
            if ($lang !== I18n::$base_lang)
            {
                    // The message and target languages are different
                    // Get the translation for this message
                    $string = I18n::get($string, $lang);
            }

		return empty($values) ? $string : strtr($string, $values);
	}
        
        protected static function _load($lang, $dir)
        {
            // New translation table
		$table = array();

		// Split the language: language, region, locale, etc
		$parts = explode('-', $lang);

		do
		{
			// Create a path for this set of parts
			$path = implode(DIRECTORY_SEPARATOR, $parts);

			if ($files = Core::find_file($path, $dir, NULL, TRUE))
			{        
                            $t = array();
				foreach ($files as $file)
				{
					// Merge the language strings into the sub table
					$t = array_merge($t, Core::load($file));
				}

				// Append the sub table, preventing less specific language
				// files from overloading more specific files
				$table += $t;
			}

			// Remove the last part
			array_pop($parts);
		}
		while ($parts);
       
                return $table;
        }

}

//if ( ! function_exists('__'))
//{
	/**
	 * Kohana translation/internationalization function. The PHP function
	 * [strtr](http://php.net/strtr) is used for replacing parameters.
	 *
	 *    __('Welcome back, :user', array(':user' => $username));
	 *
	 * [!!] The target language is defined by [I18n::$lang].
	 *
	 * @uses    I18n::get
	 * @param   string  $string text to translate
	 * @param   array   $values values to replace in the translated text
	 * @param   string  $lang   source language
	 * @return  string
	 */
//	function __($string, array $values = NULL, $lang = 'en-us')
//	{
//		if ($lang !== I18n::$lang)
//		{
//			// The message and target languages are different
//			// Get the translation for this message
//			$string = I18n::get($string);
//		}
//
//		return empty($values) ? $string : strtr($string, $values);
//	}
//}
