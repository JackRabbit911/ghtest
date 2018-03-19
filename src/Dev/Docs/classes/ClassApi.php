<?php
namespace Docs;

/**
 * :KLUDGE: Это пиздец. Самый главный класс
 * Всякое такое.
 * [!!] lalala
 * :KLUDGE: Это пиздец. Самый главный класс
 * 
 * @author JackRabbit
 * @author Kolosoft
 */


use Core\Exception\Debug;
use Core\Helper\Text;
use Core\Helper\Arr;
use Core\Helper\HTML;
use Docs\DocBlock;

class ClassApi
{

	/**
	 * @var  ReflectionClass The ReflectionClass for this class
	 */
	public $class;

	/**
	 * @var  string  modifiers like abstract, final
	 */
	public $modifiers;

	/**
	 * @var  string  description of the class from the comment
	 */
	public $description;

	/**
	 * @var  array  array of tags, retrieved from the comment
	 */
	public $tags = array();

	/**
	 * @var  array  array of this classes constants
	 */
	public $constants = array();

	/**
	 * @var array Parent classes/interfaces of this class/interface
	 */
	public $parents = array();
        
        
        public $module = NULL;

	/**
	 * Loads a class and uses [reflection](http://php.net/reflection) to parse the class. 
         * Reads the class modifiers, constants and comment. Parses the
	 * comment to find the description and tags.
	 *
	 * @param   string  Class name
	 * @return  void
	 */
	public function __construct($class)
	{
		$this->class = new \ReflectionClass($class);
                
//                $path = $this->class->getFilename();
//                
//                
//                $s = (string) substr($path, strlen(SRCPATH));
//                $pos = stripos($s, 'classes');
//                $m = substr($s, 0, $pos-1);
                
                $this->module = $this->getModule($this->class);
		
                if ($modifiers = $this->class->getModifiers())
		{
                    $prefix = '';
                    if($this->class->isInterface()) $prefix = 'interface ';
                    if($this->class->isTrait()) $prefix = 'trait ';
                    
                    $this->modifiers = '<small>'.$prefix.implode(' ', \Reflection::getModifierNames($modifiers)).'</small> ';
		}

		$this->constants = $this->class->getConstants();

		// If ReflectionClass::getParentClass() won't work if the class in
		// question is an interface
		if ($this->class->isInterface())
		{
			$this->parents = $this->class->getInterfaces();
		}
		else
		{
			$parent = $this->class;

			while ($parent = $parent->getParentClass())
			{
				$this->parents[] = $parent;
			}
		}

		if ( ! $comment = $this->class->getDocComment())
		{
			foreach ($this->parents as $parent)
			{
				if ($comment = $parent->getDocComment())
				{
					// Found a description for this class
					break;
				}
			}
		}
                
//                $x = DocBlock::parse($comment, FALSE);
//                
//                var_dump($x); exit;
                
//                $this->description = $x['comment'];
//                $this->tags = $x['tags'];

		list($this->description, $this->tags) = DocBlock::parse($comment);   //, FALSE);
	}
        
        
        /**
         * Get module name of this class
         * 
         * @param \ReflectionClass $class
         * @return type string
         */
        public static function getModule(\ReflectionClass $class)
        {
            $path = $class->getFilename();
            $s = (string) substr($path, strlen(SRCPATH));
            $pos = stripos($s, 'classes');
            return (string) substr($s, 0, $pos-1);
        }
        
        /**
         * Get link to the class api
         * 
         * @param string | \ReflectionClass $class
         * @return type string
         */
        public static function getLink($class, array $postfix=NULL)
        {
            
            
            if($class instanceof \ReflectionClass)
                $name = $class->getName();
            else
            {
                $name = $class;
                if(class_exists($class)) $class = new \ReflectionClass($class);
                else return $name;
            }
            
            if($postfix === NULL) $postfix = array('anchor'=>NULL, 'text'=>$name);
            
//            return $name;
            
            if($class->getFilename())
            {
                $route = \Core\Route::current();                
                $url = str_replace('\\', '_', $name);
                $module = strtolower(static::getModule($class));
                return HTML::anchor($route->uri(array('class' => $url, 'module1'=>$module)).$postfix['anchor'], str_replace('#', '::', $postfix['text']), NULL, NULL, TRUE);
            }
            else return HTML::anchor('http://php.net/manual/class.'.strtolower($name).'.php', $name.' <small>internal PHP class</small>', ['target'=>'_blank'], NULL, TRUE);
        }

	/**
	 * Gets the constants of this class as HTML.
	 *
	 * @return  array
	 */
	public function constants()
	{
		$result = array();

		foreach ($this->constants as $name => $value)
		{
			$result[$name] = Debug::vars($value);
		}

		return $result;
	}

	/**
	 * Get the description of this class as HTML. Includes a warning when the
	 * class or one of its parents could not be found.
	 *
	 * @return  string  HTML
	 */
	public function description()
	{
		$result = $this->description;

		// If this class extends Kodoc_Missing, add a warning about possible
		// incomplete documentation
		foreach ($this->parents as $parent)
		{
			if ($parent->name == 'Kodoc_Missing')
			{
				$result .= "[!!] **This class, or a class parent, could not be
				           found or loaded. This could be caused by a missing
				           module or other dependancy. The documentation for
				           class may not be complete!**";
			}
		}

		return Text::markdown($result);
	}

	/**
	 * Gets a list of the class properties as objects.
	 *
	 * @return  array
	 */
	public function properties()
	{
		$props = $this->class->getProperties();

		$defaults = $this->class->getDefaultProperties();

		usort($props, array($this,'_prop_sort'));

		foreach ($props as $key => $property)
		{
			// Create Kodoc Properties for each property
			$props[$key] = new PropertyApi($this->class->name, $property->name,  Arr::get($defaults, $property->name));
		}

		return $props;
	}

	protected function _prop_sort($a, $b)
	{
		// If one property is public, and the other is not, it goes on top
		if ($a->isPublic() AND ( ! $b->isPublic()))
			return -1;
		if ($b->isPublic() AND ( ! $a->isPublic()))
			return 1;

		// If one property is protected and the other is private, it goes on top
		if ($a->isProtected() AND $b->isPrivate())
			return -1;
		if ($b->isProtected() AND $a->isPrivate())
			return 1;

		// Otherwise just do alphabetical
		return strcmp($a->name, $b->name);
	}

	/**
	 * Gets a list of the class methods as objects.
	 *
	 * @return  array
	 */
	public function methods()
	{
		$methods = $this->class->getMethods();
		usort($methods, array($this,'_method_sort'));
                
                $traits = array_reverse($this->class->getTraits());

		foreach ($methods as $key => $method)
		{
                    
                    
                    $method_reflector = new MethodApi($this->class->name, $method->name);
                    
                    $method_in_trate = $this->is_trate_defined($traits, $method->name);
                    
                    if($method_in_trate !== FALSE) $methods[$key] = $method_in_trate;
                    else $methods[$key] = $method_reflector;
                    
//                    if($method_reflector == $method_in_trate)
//                        $methods[$key] = $method_in_trate;
//                    else 
//                        $methods[$key] = $method_reflector;
                    
		}

		return $methods;
	}
        
        /**
	 * Gets an object of MethodApi class if the method is defined in a trait.
         * [!!] Костыль! PHP 5.6 глючит на рефлекторе трейта
         * :KLUDGE:
	 *
	 * @author JackRabbit
         * @return  MethodApi object
	 */
        protected function is_trate_defined($traits, $method)
        {
            foreach($traits AS $trait)
            {
//                $trait->getName();
                if(method_exists($trait->getName(), $method))
                {
                    $method_reflector = new MethodApi($trait->getName(), $method);
                    return $method_reflector;
                }
            }
            return FALSE;
        }

	/**
	 * Sort methods based on their visibility and declaring class based on:
	 *
	 *  * methods will be sorted public, protected, then private.
	 *  * methods that are declared by an ancestor will be after classes
	 *    declared by the current class
	 *  * lastly, they will be sorted alphabetically
	 *
	 */
	protected function _method_sort($a, $b)
	{
		// If one method is public, and the other is not, it goes on top
		if ($a->isPublic() AND ( ! $b->isPublic()))
			return -1;
		if ($b->isPublic() AND ( ! $a->isPublic()))
			return 1;

		// If one method is protected and the other is private, it goes on top
		if ($a->isProtected() AND $b->isPrivate())
			return -1;
		if ($b->isProtected() AND $a->isPrivate())
			return 1;

		// The methods have the same visibility, so check the declaring class depth:


		/*
		echo Debug::vars('a is '.$a->class.'::'.$a->name,'b is '.$b->class.'::'.$b->name,
						   'are the classes the same?', $a->class == $b->class,'if they are, the result is:',strcmp($a->name, $b->name),
						   'is a this class?', $a->name == $this->class->name,-1,
						   'is b this class?', $b->name == $this->class->name,1,
						   'otherwise, the result is:',strcmp($a->class, $b->class)
						   );
		*/

		// If both methods are defined in the same class, just compare the method names
		if ($a->class == $b->class)
			return strcmp($a->name, $b->name);

		// If one of them was declared by this class, it needs to be on top
		if ($a->name == $this->class->name)
			return -1;
		if ($b->name == $this->class->name)
			return 1;

		// Otherwise, get the parents of each methods declaring class, then compare which function has more "ancestors"
		$adepth = 0;
		$bdepth = 0;

		$parent = $a->getDeclaringClass();
		do
		{
			$adepth++;
		}
		while ($parent = $parent->getParentClass());

		$parent = $b->getDeclaringClass();
		do
		{
			$bdepth++;
		}
		while ($parent = $parent->getParentClass());

		return $bdepth - $adepth;
	}

	/**
	 * Get the tags of this class as HTML.
	 *
	 * @return  array
	 */
	public function tags()
	{
		$result = array();

		foreach ($this->tags as $name => $set)
		{
			foreach ($set as $text)
			{
				$result[$name][] = DocBlock::format_tag($name, $text);
			}
		}

		return $result;
	}

} // End Kodoc_Class
