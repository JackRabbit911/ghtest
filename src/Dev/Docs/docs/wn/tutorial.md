## Tutorial
### Framework
#### Hello World!
Вывод простейшей строки можно осуществить двумя способами:
1. В файле init.php прописываем роут с callback функцией
<pre><code>Route::set('test', 'test')->callback(function(){
        echo '<h1>Hello, World!</h1>';
});</code></pre>
2. В файле init.php прописываем роут с указанием контроллера
<pre><code>Route::set('test', 'test')->defaults(array(
    'controller' => 'test',
    'action' => 'index'
));</code></pre>
Создаем контроллер Test примерно такой:
<pre><code><?php
namespace App\Controller;
use Core\Controller;
class Test extends Controller\Controller
{
        public function index()
        {
            echo '<h1>Hello, World</h1>';
        }
}
</code></pre>
Набираем в адресной строке браузера <code>site.com/test</code> и любуемся!

#### Роуты
Формируем адресное пространство сайта. Для этого есть механизм роутов. **Роут (Route)** - это класс, который устанавливает правила соответствия введенного адреса и конроллера/метода, который будет вызван.
Экземпляр класса Route создается статическим методом Route::set(), запись об этом производится в файле init.php модуля App или любого вашего модуля.
Метод defaults() принимает ассоциативный массив параметров, которые будут переданы в класс Request по умолчанию, т.е., в случае если парсер адресной строки не определит какие-либо параметры (например, по причине их отсутствия)

Если, например, роут выглядит так:
<pre><code>Route::set('default', '(<controller>(/<action>(/<param>))(.<ext>))(?<query>)')
    ->defaults(array(
        'controller' => 'index',
        'action' => 'index',
        'param' => NULL,
        ),
    ->filter('ext' => 'html|htm');
);</code></pre>
это означает, что данный экземляр роута называется **'default'**, 
первым необязятельным  сегментом строки URI является конроллер в модуле АРР, 
вторым необязательным сегментом строки URI является метод котроллера,
третьим необязательным сегментом строки URI является аргумент метода,
далее, через точку, может быть указано как-бы расширение файла, в данном случае допускаетмя только .htm или .html
далее может следовать query составляющая строки запроса.
При отсутствии сегментов URI, будут подставлены значения из массива, передаваемого в метод defaults().  
Например, URI вида <code>/news/hakasia/01-10-1967.html</code> означает, что будет вызван контроллер News, метод hakasia('01-10-1967'), т.е., мы получаем новости Хакасии за 01.10.1967 г.  
URI вида <code>/news/hakasia.html</code> означает, что будет вызван контроллер News, метод hakasia(), т.е., мы получаем новости Хакасии за все время существования Хакасии.  
URI вида <code>/news.html</code> означает, что будет вызван контроллер News, метод index(), т.е., мы получаем все новости за все время от сотворения мира.  
URI вида <code>/</code> означает, что будет вызван контроллер Index, метод index(), т.е., мы получаем, к примеру, главную страницу сайта.  