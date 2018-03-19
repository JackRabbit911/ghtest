<?php
namespace Core\Controller;

use Core\Controller\Controller;
//use Core\Core;
use Core\View;

/**
 * Description of Template
 *
 * @author JackRabbit
 */
abstract Class Template extends Controller
{
    /**
     * name of Template and than object of View class templare
     * 
     * @var string|object
     */
    public $template = 'template';
    
    protected function _before()
    {
        parent::_before();            
        $this->template = View::factory($this->template);
    }
    
    protected function _after()
    {
        echo $this->template->render();

        parent::_after();
    }
}