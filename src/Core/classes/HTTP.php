<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core;

/**
 * Description of HTTP
 *
 * @author JackRabbit
 */

use Core\Helper\Arr;

class HTTP
{
    // Default Accept-* quality value if none supplied
    const DEFAULT_QUALITY = 1;
    
    public static $uri = NULL;
    
    protected static $base_url = NULL;
    
    protected static $_user_agent = NULL;
    
    protected static $_client_ip = NULL;
    
    protected static $scheme = NULL;
    protected static $domain = NULL;
    protected static $protocol = NULL;
    
    protected static $_request_headers = NULL;
    
    
    protected static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
    
    
    /**
     * Checks the browser cache to see the response needs to be returned,
     * execution will halt and a 304 Not Modified will be sent if the
     * browser cache is up to date.
     *
     * @param  Request   $request   Request
     * @param  Response  $response  Response
     * @param  string    $etag      Resource ETag
     * @throws HTTP_Exception_304
     * @return Response
     */
    public static function check_cache(Request $request, Response $response, $etag = NULL)
    {
            // Generate an etag if necessary
            if ($etag == NULL)
            {
                    $etag = $response->generate_etag();
            }

            // Set the ETag header
            $response->headers('etag', $etag);

            // Add the Cache-Control header if it is not already set
            // This allows etags to be used with max-age, etc
            if ($response->headers('cache-control'))
            {
                    $response->headers('cache-control', $response->headers('cache-control').', must-revalidate');
            }
            else
            {
                    $response->headers('cache-control', 'must-revalidate');
            }

            // Check if we have a matching etag
            if ($request->headers('if-none-match') AND (string) $request->headers('if-none-match') === $etag)
            {
                    // No need to send data again
                    throw HTTP_Exception::factory(304)->headers('etag', $etag);
            }

            return $response;
    }
    
    public static function detect_url()
    {
//        if (isset($_SERVER['PATH_INFO']))
//        {
            $url = filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_URL);
            return $url;
//        }
//        else return '';
    }
    
    public static function detect_uri()
    {
        if(static::$uri !== NULL) return static::$uri;
        
        if (isset($_SERVER['REQUEST_URI']))
        {           
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);            
        }
        elseif (isset($_SERVER['PHP_SELF']))
        {
            $uri = $_SERVER['PHP_SELF'];
        }
        elseif (isset($_SERVER['REDIRECT_URL']))
        {
            $uri = $_SERVER['REDIRECT_URL'];
        }
        else
        {
            throw new Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
        }

        // Get the path from the base URL, including the index file
//          $base_url = parse_url(Core::init('base_url'), PHP_URL_PATH);
        
        if (strpos($uri, '/') === 0)
        {
//            self::$base_url =  self::base_url();
//            $uri = (string) substr($uri, strlen(self::$base_url));
            $uri = (string) substr($uri, strlen(BASEDIR));
        }
//        define('BASEDIR', self::$base_url);
        
        if (strpos($uri, '/index.php') === 0)
        {
            // Remove the index file from the URI
            $uri = (string) substr($uri, strlen('/index.php'));
        }

        return self::$uri = $uri;
    }
    
    public static function user_agent()
    {
        if(empty(static::$_user_agent))
            if (isset($_SERVER['HTTP_USER_AGENT']))
                static::$_user_agent = $_SERVER['HTTP_USER_AGENT'];
            
            return static::$_user_agent;
    }
    
    public static function client_ip()
    {
        if(static::$_client_ip === NULL)
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                AND isset($_SERVER['REMOTE_ADDR'])
                AND in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
            {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                // Format: "X-Forwarded-For: client1, proxy1, proxy2"
                $client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                static::$_client_ip = array_shift($client_ips);

                unset($client_ips);
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP'])
                    AND isset($_SERVER['REMOTE_ADDR'])
                    AND in_array($_SERVER['REMOTE_ADDR'], static::$trusted_proxies))
            {
                // Use the forwarded IP address, typically set when the
                // client is using a proxy server.
                $client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);

                static::$_client_ip = array_shift($client_ips);

                unset($client_ips);
            }
            elseif (isset($_SERVER['REMOTE_ADDR']))
            {
                // The remote IP address
                static::$_client_ip = $_SERVER['REMOTE_ADDR'];
            }
        }

        return static::$_client_ip;
    }
    
    public static function scheme()
    {
        if(self::$scheme === NULL)
            self::$scheme = filter_input(INPUT_SERVER, 'REQUEST_SCHEME', FILTER_SANITIZE_URL);
        
        return self::$scheme;
    }
    
    public static function protocol()
    {
         if(self::$protocol === NULL)
            self::$protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_URL);
        
        return self::$protocol;
    }

    public static function domain()
    {
        if(self::$domain === NULL)
            self::$domain = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
        
        return self::$domain;
    }
    
    public static function base_url()
    {
        if(self::$base_url === NULL)
        {
            self::$base_url = (string)substr(dirname(SRCPATH), strlen($_SERVER['DOCUMENT_ROOT']));
            self::$base_url = '/'.ltrim(self::$base_url, DIRECTORY_SEPARATOR);
        }
        
        return self::$base_url;
    }

    public static function url($uri=NULL)
    {
        if($uri) $uri = '/'.trim($uri, '/');
        else $uri = self::detect_uri();
        return self::scheme().'://'.self::domain().self::base_url().$uri;
    }
    
//    public static function request_headers($key = NULL)
//    {
//        if(self::$_request_headers === NULL)
//            self::$_request_headers = apache_request_headers();
//
//        if($key === NULL) return self::$_request_headers;
//        else return Arr::get(self::$_request_headers, $key);
//    }

    public static function redirect($uri=NULL, $code=302)
    {
        if($uri === NULL)
        {
            $uri = static::scheme().'://'.static::domain().static::detect_uri();
        }
//        else
//        {
//            $uri = self::$base_url.$uri;
//        }
        header('Location: '.$uri, $code);
    }
    
    public static function referer($default_url = '')
    {
        if($default_url === '') $default_url = static::base_url();
        
        $ref = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_URL);
        
        if(!$ref) $ref = $default_url;
        
        return $ref;
    }
    
//    public static function header($headers = NULL)
//    {
//        if(empty($headers)) return apache_response_headers();
//        elseif(is_array($headers) && Arr::is_assoc($headers))
//        {
//            foreach($headers AS $key=>$value)
//            {
//                header((ucwords($key, '-').': '.$value));
//            }
//        }
//        elseif(func_num_args() === 2 && is_string($headers))
//        {
//            header(ucwords($headers, '-').': '.func_get_arg(1));
//        }
//        elseif(func_num_args() === 1)
//        {
//            return Arr::get(apache_response_headers(), $headers);
//        }
//    }
    
    public static function status($status, $message=NULL)
    {
        if($message !== NULL) $message = ' '.$message;
        header(self::protocol().' '.$status.$message);
    }

        /**
	 * Parses an Accept(-*) header and detects the quality
	 *
	 * @param   array   $parts  accept header parts
	 * @return  array
	 * @since   3.2.0
	 */
//	public static function accept_quality(array $parts)
//	{
//		$parsed = array();
//
//		// Resource light iteration
//		$parts_keys = array_keys($parts);
//		foreach ($parts_keys as $key)
//		{
//			$value = trim(str_replace(array("\r", "\n"), '', $parts[$key]));
//
//			$pattern = '~\b(\;\s*+)?q\s*+=\s*+([.0-9]+)~';
//
//			// If there is no quality directive, return default
//			if ( ! preg_match($pattern, $value, $quality))
//			{
//				$parsed[$value] = (float) self::DEFAULT_QUALITY;
//			}
//			else
//			{
//				$quality = $quality[2];
//
//				if ($quality[0] === '.')
//				{
//					$quality = '0'.$quality;
//				}
//
//				// Remove the quality value from the string and apply quality
//				$parsed[trim(preg_replace($pattern, '', $value, 1), '; ')] = (float) $quality;
//			}
//		}
//
//		return $parsed;
//	}
        
        /**
	 * Parses the `Accept-Language:` HTTP header and returns an array containing
	 * the languages and associated quality.
	 *
	 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 * @param   string  $language   charset string to parse
	 * @return  array
	 * @since   3.2.0
	 */
//	public static function parse_language_header($language = NULL)
//	{
//                if ($language === NULL)
//		{
//			return array('*' => array('*' => (float) self::DEFAULT_QUALITY));
//		}
//                
//                $language = strtolower($language);
//
//		$language = self::accept_quality(explode(',', (string) $language));
//
//		$parsed_language = array();
//
//		$keys = array_keys($language);
//		foreach ($keys as $key)
//		{
//			// Extract the parts
//			$parts = explode('-', $key, 2);
//
//			// Invalid content type- bail
//			if ( ! isset($parts[1]))
//			{
//				$parsed_language[$parts[0]]['*'] = $language[$key];
//			}
//			else
//			{
//				// Set the parsed output
//				$parsed_language[$parts[0]][$parts[1]] = $language[$key];
//                                $parsed_language[$parts[0]]['*'] = $language[$key];
//			}
//		}
//
//		return $parsed_language;
//	}
        
        	/**
	 * Returns the quality of `$language` supplied, optionally ignoring
	 * wildcards if `$explicit` is set to a non-`FALSE` value. If the quality
	 * is not found, `0.0` is returned.
	 *
	 *     // Accept-Language: en-us, en-gb; q=.7, en; q=.5
	 *     $lang = $header->accepts_language_at_quality('en-gb');
	 *     // $lang = (float) 0.7
	 *
	 *     $lang2 = $header->accepts_language_at_quality('en-au');
	 *     // $lang2 = (float) 0.5
	 *
	 *     $lang3 = $header->accepts_language_at_quality('en-au', TRUE);
	 *     // $lang3 = (float) 0.0
	 *
	 * @param   string  $language   language to interrogate
	 * @param   boolean $explicit   explicit interrogation, `TRUE` ignores wildcards
	 * @return  float
	 * @since   3.2.0
	 */
//	public static function accepts_language_at_quality($language, $explicit = FALSE)
//	{
//                $_accept_language = self::parse_language_header(self::request_headers('Accept-Language'));
//
//		// Normalize the language
//		$language_parts = explode('-', strtolower($language), 2);
//                
//		if (isset($_accept_language[$language_parts[0]]))
//		{
//			if (isset($language_parts[1]))
//			{
//				if (isset($_accept_language[$language_parts[0]][$language_parts[1]]))
//				{
//					return $_accept_language[$language_parts[0]][$language_parts[1]];
//				}
//				elseif ($explicit === FALSE AND isset($_accept_language[$language_parts[0]]['*']))
//				{
//					return $_accept_language[$language_parts[0]]['*'];
//				}
//			}
//			elseif (isset($_accept_language[$language_parts[0]]['*']))
//			{
//				return $_accept_language[$language_parts[0]]['*'];
//			}
//		}
//
//		if ($explicit === FALSE AND isset($_accept_language['*']))
//		{
//			return $_accept_language['*'];
//		}
//
//		return (float) 0;
//	}
        
        /**
	 * Returns the preferred language from the supplied array `$languages` based
	 * on the `Accept-Language` header directive.
	 *
	 *      // Accept-Language: en-us, en-gb; q=.7, en; q=.5
	 *      $lang = $header->preferred_language(array(
	 *          'en-gb', 'en-au', 'fr', 'es'
	 *      )); // $lang = 'en-gb'
	 *
	 * @param   array   $languages
	 * @param   boolean $explicit
	 * @return  mixed
	 * @since   3.2.0
	 */
//	public static function preferred_language(array $languages, $explicit = FALSE)
//	{
//		$ceiling = 0;
//		$preferred = FALSE;
//
//		foreach ($languages as $language)
//		{
//			$quality = self::accepts_language_at_quality($language, $explicit);
//
//			if ($quality > $ceiling)
//			{
//				$ceiling = $quality;
//				$preferred = $language;
//			}
//		}
//
//		return $preferred;
//	}
        
        /**
	 * Parses the `Accept-Charset:` HTTP header and returns an array containing
	 * the charset and associated quality.
	 *
	 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
	 * @param   string  $charset    charset string to parse
	 * @return  array
	 * @since   3.2.0
	 */
//	public static function parse_charset_header($charset = NULL)
//	{
//		if ($charset === NULL)
//		{
//			return array('*' => (float) self::DEFAULT_QUALITY);
//		}
//
//		return HTTP_Header::accept_quality(explode(',', (string) $charset));
//	}

	/**
	 * Parses the `Accept-Encoding:` HTTP header and returns an array containing
	 * the charsets and associated quality.
	 *
	 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
	 * @param   string  $encoding   charset string to parse
	 * @return  array
	 * @since   3.2.0
	 */
//	public static function parse_encoding_header($encoding = NULL)
//	{
//		// Accept everything
//		if ($encoding === NULL)
//		{
//			return array('*' => (float) self::DEFAULT_QUALITY);
//		}
//		elseif ($encoding === '')
//		{
//			return array('identity' => (float) self::DEFAULT_QUALITY);
//		}
//		else
//		{
//			return self::accept_quality(explode(',', (string) $encoding));
//		}
//	}
        
//        public static function match_encoding_header($encoding)
//        {
//            if(self::$_request_headers === NULL) self::$_request_headers = apache_request_headers();
//            
//            if($encoding_header = Arr::get(self::$_request_headers, 'Accept-Encoding'))
//            {
//                $encoding_header = array_map('trim', explode(',', $encoding_header));
//                return in_array($encoding, $encoding_header);
//            }
//            else return FALSE;
//        }



	/**
	 * Generates a Cache-Control HTTP header based on the supplied array.
	 *
	 *     // Set the cache control headers you want to use
	 *     $cache_control = array(
	 *         'max-age'          => 3600,
	 *         'must-revalidate',
	 *         'public'
	 *     );
	 *
	 *     // Create the cache control header, creates :
	 *     // cache-control: max-age=3600, must-revalidate, public
	 *     $response->headers('Cache-Control', HTTP_Header::create_cache_control($cache_control);
	 *
	 * @link    http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13
	 * @param   array   $cache_control  Cache-Control to render to string
	 * @return  string
	 */
	public static function create_cache_control(array $cache_control)
	{
		$parts = array();

		foreach ($cache_control as $key => $value)
		{
			$parts[] = (is_int($key)) ? $value : ($key.'='.$value);
		}

		return implode(', ', $parts);
	}

	/**
	 * Parses the Cache-Control header and returning an array representation of the Cache-Control
	 * header.
	 *
	 *     // Create the cache control header
	 *     $response->headers('cache-control', 'max-age=3600, must-revalidate, public');
	 *
	 *     // Parse the cache control header
	 *     if ($cache_control = HTTP_Header::parse_cache_control($response->headers('cache-control')))
	 *     {
	 *          // Cache-Control header was found
	 *          $maxage = $cache_control['max-age'];
	 *     }
	 *
	 * @param   array   $cache_control Array of headers
	 * @return  mixed
	 */
	public static function parse_cache_control($cache_control)
	{
		$directives = explode(',', strtolower($cache_control));

		if ($directives === FALSE)
			return FALSE;

		$output = array();

		foreach ($directives as $directive)
		{
			if (strpos($directive, '=') !== FALSE)
			{
				list($key, $value) = explode('=', trim($directive), 2);

				$output[$key] = ctype_digit($value) ? (int) $value : $value;
			}
			else
			{
				$output[] = trim($directive);
			}
		}

		return $output;
	}

}
