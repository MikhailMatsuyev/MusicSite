<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Htmldom;
use App\Artist;
class ParseSiteAD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parseAD:site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse bio, name and photo artists from 1st page';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $upload_dir;
    
    public function __construct()
    {
        parent::__construct();
        $this->upload_dir = base_path() . '/public/uploads';
    }
    
    public function store($pars)
    {        
        foreach ($pars as $p)
        {
            $file=explode("/",$p['photo']);
            $newfile = $this->upload_dir."/".$file[7];
            if (!copy($p['photo'], $newfile)) {
                echo "не удалось скопировать $file[7]...\n";
            }
            $p['photo']=$file[7];

            Artist::create($p);
            exit();
        }    
    }
    
    public function handle()
    {
        //require_once 'simple_html_dom.php';    
        $connection = curl_init('http://www.artistdirect.com/music/pop/artists/877');
        $site='http://www.artistdirect.com';     
        //Устанавливаем адрес для подключения, по умолчанию методом GET
        //curl_ - с его помощью подключимся к нужному сайту и спарсим все
        //адреса страниц, информацию с которых нужно сохранить.
        //Затем с помощью curl_multi-* спарсим в режиме многопоточности
        //в массив каждую страницу.
        //
        curl_setopt($connection, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($connection, CURLOPT_HEADER, TRUE);       
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, TRUE);

        //Выполняем запрос
        //получили код нужной страницы сайта
        $html=curl_exec($connection);

        //Завершает сеанс
        curl_close($connection);

        //на целевой странице увидели, что ссылки хранятся в абзаце
        //с class=media
        $target_page = new Htmldom();
        $target_page->load($html);
        $hrefer=$target_page->find('p[class=media]');

        $cmi = curl_multi_init();
        foreach ($hrefer as $key)
        {
            $ci = curl_init($site.$key->first_child ()->href);

            curl_setopt($ci, CURLOPT_URL, $site.$key->first_child ()->href);
            curl_setopt($ci, CURLOPT_HEADER, false);
            curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ci, CURLOPT_TIMEOUT, 10);

            $tasks[$site.$key->first_child ()->href] = $ci;
            curl_multi_add_handle($cmi, $ci); 
        }

        $active_flow = null;
        $pars = array();

        do {
            //$active_flow - будет меняться
            $mrc = curl_multi_exec($cmi, $active_flow);
            curl_multi_select($cmi);        
        }
        while ($mrc == CURLM_CALL_MULTI_PERFORM);

        // выполняем, пока есть активные потоки
        while ($active_flow && ($mrc == CURLM_OK)) 
        {
        // если какой-либо поток готов к действиям
            if (curl_multi_select($cmi) != -1) {
                // ждем, пока что-нибудь изменится
                do 
                {
                    $mrc = curl_multi_exec($cmi, $active_flow);
                    // получаем информацию о потоке
                    $info = curl_multi_info_read($cmi);
                    // если поток завершился
                    if ($info['msg'] == CURLMSG_DONE) {
                        $ci = $info['handle'];
                        // ищем урл страницы по дескриптору потока в массиве заданий
                        $url = array_search($ci, $tasks);
                        // забираем содержимое
                        $tasks[$url] = curl_multi_getcontent($ci);
                        // удаляем поток из мультикурла
                        curl_multi_remove_handle($cmi, $ci);
                        // закрываем отдельное соединение (поток)
                        curl_close($ci);

                        $html = new Htmldom();
                        $html->load($tasks[$url]);
                        $photo=$html->find('div[id=artistPhoto]');

                        foreach ($photo as $key){
                            $title=$key->first_child ()->title;
                            $src=$key->first_child ()->first_child ()->src;
                            $pars[$title]['name']=$title;
                            $pars[$title]['photo']=$src;   
                        } 

                        $bio=$html->find('div[class=content]');
                        foreach ($bio as $key)
                        {
                            //на странице есть еще один класс "content", избавляемся от него
                            if ($key->first_child ()->tag==="li"){
                                continue;
                            } 
                            $pars[$title]['bio']=$key->innertext;
                        } 
                    }   
                }          
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        // закрываем мультикурл
        curl_multi_close($cmi);

        unset($cmi);
        unset($mrc);
        $this->store($pars);
    }
}

