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
    private $target_page;
    private $site;
            
    public function __construct()
    {
        parent::__construct();
        $this->upload_dir = base_path() . '/public/uploads';
        $this->site='http://www.artistdirect.com';
    }
    
    private function store ($parser_date)
    {

        $file=explode("/",$parser_date['photo']);
        $newfile = $this->upload_dir."/".end($file);
        if (!copy($parser_date['photo'], $newfile)) {
            echo "не удалось скопировать". end($file)."...\n";
        }
        $parser_date['photo']=end($file);
        Artist::create($parser_date);
        
    }
    
    private function parseLinksFromStartPage ()
    {

        $connection = curl_init('http://www.artistdirect.com/music/pop/artists/877');
        
        //Устанавливаем адрес для подключения, по умолчанию методом GET
        //curl_ - с его помощью подключимся к нужному сайту и спарсим все
        //адреса страниц, информацию с которых нужно сохранить.
        //Затем с помощью curl_multi-* спарсим в режиме многопоточности
        //в массив каждую страницу.
        
        curl_setopt($connection, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($connection, CURLOPT_HEADER, true);       
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, TRUE);

        //Выполняем запрос и получаем код нужной страницы сайта в $html
        $html=curl_exec($connection);
        
        if ($html === FALSE) {
            echo "cURL Error: " . curl_error($connection);
        }
        
        $info_abt_curl = curl_getinfo($connection);
        echo 'Took ' . $info_abt_curl['total_time'] . ' seconds for url ' . $info_abt_curl['url'];
         
        //Завершаем сеанс
        curl_close($connection);
      
        //на целевой странице увидели, что ссылки хранятся в абзаце с class=media 
        
        $hrefer=preg_match_all("|<p class=\"media\">.*?href=\"(.*)\"|", $html, $matches); 
        return $matches[1];
    }
    
    
    public function handle()
    {
        $hrefer=$this->parseLinksFromStartPage();
        $mh = curl_multi_init(); //создаем набор дескрипторов cURL
        
        foreach ($hrefer as $i=>$v){
            $link = $this->site.$v;
            $ch[$i] = curl_init($link);
            curl_setopt($ch[$i], CURLOPT_HEADER, 0);          //Не включать заголовки в ответ
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);  //Убираем вывод данных в браузер
            curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 30); //Таймаут соединения
            curl_multi_add_handle($mh, $ch[$i]);
        }
        
        while (curl_multi_exec($mh, $running) == CURLM_CALL_MULTI_PERFORM); //Запускаем соединения
        usleep (100000);  //100мс. 
        $i=1;
        $status = curl_multi_exec($mh, $running);
        //Пока есть незавершенные соединения и нет ошибок мульти-cURL
        while ($running > 0 && $status == CURLM_OK) {
            curl_multi_select($mh, 4); //ждем активность на файловых дескрипторах. Таймаут 4сек
            usleep (500000);                 //500мс. 
            //Вдруг cURL хочет быть вызвана немедленно опять..
            while (($status = curl_multi_exec($mh, $running)) == CURLM_CALL_MULTI_PERFORM);
            //Если есть завершенные соединения
            while (($info = curl_multi_info_read($mh))) 
            {
                $easyHandle = $info['handle'];    //простой дескриптор cURL
                $one = curl_getinfo($easyHandle); //получаем инфу по каждому простому дескриптору
                $httpCode = $one['http_code'];
                
                if ($httpCode == 200) {    //если файл/страница успешно получена
                    echo  "\n".$i++." URL: ${one['url']} | HTTP code: $httpCode";
 
                    $tasks=curl_multi_getcontent($easyHandle);

                    if (preg_match("|<div id=\"artistPhoto\">.*?src=\"(.*)\"\s|", $tasks, $photo_src)){
                       var_dump($pars['photo']=$photo_src[1]); 
                    }else{
                       var_dump($pars['photo']="out photo"); 
                    };
                    
                    if (preg_match("|<div id=\"artistPhoto\"><a title=\"(.*)\"\shref|", $tasks, $title)){
                       var_dump($pars['name']=$title[1]); 
                    }else{
                       var_dump($pars['name']="out title"); 
                    };
                    
                    if (preg_match("|<div class=\"content\">(.*)<a\shref|", $tasks, $bio)){
                       var_dump($pars['bio']=$bio[1]); 
                    }else{
                       var_dump($pars['bio']="out bio"); 
                    };
                    
                    $this->store($pars);

                }elseif($httpCode >= 400){  
                    echo "\n"."URL: ${one['url']} | HTTP code: $httpCode";
                }
                curl_multi_remove_handle($mh, $easyHandle);
                curl_close($easyHandle);
            }
        } 
    }
}


