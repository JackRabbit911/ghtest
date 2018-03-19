<?php
namespace App\Controller;

use Core\Controller;
use Core\Core;
use Core\Helper\Text;

/**
 * Description of Index
 * about controller Index
 *
 * @author JackRabbit
 * @copyright (c) year, John Doe
 */
class Index extends Controller\Template
{
    /**
     * name of Template and than object of View class templare
     * 
     * @var string|object
     */
    public $template = '/Dev/Docs/views/template';
    
    protected function _before()
    {
        parent::_before();
        
        
        
        
        $menu = Core::config('menu');
        
        $this->template->header = $this->_view('header');
        
        $this->template->navbar = $this->_view('/Dev/Docs/views/navbar', $menu)
                ->set('params', $this->request->params())
                ->set('prefix', BASEDIR);
        
//        $sidebar = Text::markdown('sidebar', '/Docs/docs/wn/'.$this->request->params('action'));
        $this->template->sidebar = $this->_view('/Dev/Docs/views/sidebar')->set('sidebar', '');
        
//        $content = Text::markdown('content', '/Docs/docs/wn/'.$this->request->params('action'));
        $content = Text::markdown($this->request->params('action'), '/Dev/Docs/docs/wn/', 'code');
        $this->template->content = $this->_view('/Dev/Docs/views/content')->set('content', $content);
        
        $this->template
                ->js('media/js/vendor/speakingurl.min.js', 'media/js/vendor/slugify.min.js')
                ->js('media/js/wnbs3.js', 'media/js/index.js');
//        $this->template->js[] = 'lala';
        
    }
    
    public function index()
    {
//        $sidebar = Text::markdown('sidebar', '/Docs/docs/wn/homepage');
//        $this->template->sidebar = $this->_view('/Docs/views/sidebar')->set('text', $sidebar);
        $this->template->title = 'Webnigger!';
//        $this->template->content = $this->request->params('action');
    }
    
    public function tutorial()
    {
        $this->template->title = 'Webnigger Tutorial';
//        $this->template->content = $this->modpath;
    }  
    
    public function examples()
    {
        $this->template->title = 'Webnigger Examples';
//        $this->template->content = 'Examples';
    } 
    
    public function download()
    {
        $this->template->title = 'Webnigger download';
//        $this->template->content = 'Download';
    } 
    
    public function support()
    {
        $this->template->title = 'Webnigger Support';
//        $this->template->content = 'Support';
    } 
    
    public function forum()
    {
        $this->template->title = 'Webnigger Forum';
//        $this->template->content = 'Forum';
    } 
}
