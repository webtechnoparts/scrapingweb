<?php
error_reporting(1);

require_once 'vendor/autoload.php';
require_once 'simple_html_dom.php';

DEFINE('_DIR_PROJECT',__DIR__);
DEFINE('_API_KEY_GOOGLE','AIzaSyDKFpi_L_Fa7PXItKyJVenbwBL6Im-Ya-k');
DEFINE('_ID_MOTORE_RICERCA','00d2aa5247d474cda');
//GET https://www.googleapis.com/customsearch/v1?key=INSERT_YOUR_API_KEY&cx=017576662512468239146:omuauf_lfve&q=lectures

//**********************************************************************/
/*
 * Setta il nome del file csv contentente la lista dei siti da spaccare
 */
DEFINE('_FILE_SPACCA','test_file.csv');
DEFINE('_COLONNA_SITO',1);
DEFINE('_COLONNA_RAG_SOC',0);
DEFINE('_COLONNA_EMAIL',2);
//********************************************************************//
//********************************************************************//

function lanciaCurl($indirizzo)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $indirizzo); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $page = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //curl_error($ch); 
    curl_close($ch);
    //die($page);
    if($page===false || $http_code != 200)
    {
        //pulisco indirizzo
        if($http_code == 301) //sito spostato su altro indirizzo caso diredirect 
        {
            
             $html = str_get_html($page);
             
             if(!is_bool($html))  
             {    
                foreach($html->find('a') as $link)
                {
                    $link_find = isset($link->href) ? $link->href : $link->plaintext;
                    $page = lanciaCurl($link_find);
                    
                }    
             }   
             else
             {
                 //splitta l indirizzo 
                 $arrAdd = explode("http", strtolower($indirizzo));
                 if(count($arrAdd) > 0)
                 {
                    $new_indirizzo = 'https'.$arrAdd[1];
                    
                    $page = lanciaCurl($new_indirizzo);
                    
                 }
             }
             
        }    
        if(strpos($indirizzo, 'https') === false )
        {
            $new_indirizzo = 'https://'.$indirizzo;
            $page = lanciaCurl($new_indirizzo);
        }
        if( strpos($indirizzo, 'http') === false)
        {
            $new_indirizzo = 'http://'.$indirizzo;
            $page = lanciaCurl($new_indirizzo);
        }
        
    }
    return $page;
}

function vaisuGoogle(&$listaEmail,$rag_soc)
{
    //$indirizzo = 'https://www.google.com/search?q='.urlencode($rag_soc.' indirizzo email contatti');
    $query = urlencode($rag_soc.' indirizzo email contatti');
   $lancio = \Httpful\Request::get('https://www.googleapis.com/customsearch/v1?key='._API_KEY_GOOGLE.'&cx='._ID_MOTORE_RICERCA.'&q='.$query)->send();
   $result = array();
   $result = (array)$lancio->body;
   foreach($result['items'] as $kk => $vv)
   {
       $aa = (array)$vv;
       $snippet = $aa['snippet'];
       $arrSnippet = explode(" ", $snippet);
       for($i=0;$i<=count($arrSnippet)-1;$i++) 
       {
            if(strpos($arrSnippet[$i],'@')!== false )
                        {

                            array_push($listaEmail, $arrSnippet[$i]);
                        }
       }
       //echo '<p>'.$aa['snippet'].'</p>';
   }
   
    
    
    
    
   
    
}

function visitaSito(&$listaEmail,$indirizzo,$stato=0)
{
    $page = lanciaCurl($indirizzo);
    $html = str_get_html($page);
    //$html = file_get_html($indirizzo);
    if(!is_bool($html)) { 
        switch($stato)
        {
            case 0: 
            //caso in cui c'è il sito. 
            //Cerca gli indirizzi email in home page
            foreach($html->find('a') as $link)
            {
                $link_find = isset($link->href) ? $link->href : $link->plaintext;
                $testo_link = $link->plaintext;
                if(strpos($link_find,'@')!== false )
                {

                    array_push($listaEmail, $link_find);

                }        
                if(strpos(strtoupper($testo_link), 'CONTA') !== false) //si tratta della pagina contatti seguila
                {
                    $link_contatti = $link_find;
                }        
            }
            foreach($html->find('p') as $link)
                {
                    $link_find = $link->plaintext;
                    $arrayTesto = explode(" ", $link_find);
                    for($i=0;$i<=count($arrayTesto)-1;$i++)
                    {
                        if(strpos($arrayTesto[$i],'@')!== false )
                        {

                            array_push($listaEmail, $arrayTesto[$i]);
                        }
                    }
                            
                }
            if($link_contatti != '')
            {

                visitaSito($listaEmail, $link_contatti, 2);

            }    

            //cerca una pagina contatti se la trova cerchi le email li    
            break;
            case 2: 
                //pagina contatti
                foreach($html->find('a') as $link)
                {

                    //$link_find = $link->href;
                    $link_find = isset($link->href) ? $link->href : $link->plaintext;
                    $testo_link = $link->plaintext;
                    if(strpos($link_find,'@')!== false )
                    {
                        
                        array_push($listaEmail, $link_find);

                    }        
                }
                foreach($html->find('p') as $link)
                {
                    $link_find = $link->plaintext;
                    $arrayTesto = explode(" ", $link_find);
                    for($i=0;$i<=count($arrayTesto)-1;$i++)
                    {
                        if(strpos($arrayTesto[$i],'@')!== false )
                        {

                            array_push($listaEmail, $arrayTesto[$i]);
                        }
                    }
                            
                }
            break;
            case 1:
               
            break;
        }
    }

}


$h = fopen(_DIR_PROJECT.'/elaborato.csv', 'a') ;

if (($handle = fopen(_DIR_PROJECT.'/'._FILE_SPACCA, 'r')) !== false && $h !== false ) {
    // Leggi riga per riga fino alla fine del file
    $a = 0;
    
    while (($data = fgetcsv($handle, 1000, ';')) !== false) 
    {
            $listaEmail = array();
            if($a> 0) //escluso prima riga
            {
                $indirizzo = '';
                $indirizzo = trim($data[_COLONNA_SITO]);
                $email = '';
                $email = $data[_COLONNA_EMAIL]; 
                //SCRAPING DIRETTO DAL SITO
                /*if($indirizzo != '') //cerco dall indirizzo  
                {

                    visitaSito($listaEmail,$indirizzo);
                    $stampa =  $data[_COLONNA_RAG_SOC].';'.$data[_COLONNA_SITO].';'.implode(",", array_unique($listaEmail)). "\n";
                    fwrite($h, $stampa);
                } */  
                //SCRAPING TRAMITE RICERCA SU GOOGLE
                if($email=='') //vai su google e cerca nome azienda
                {
                    //$indirizzo = 'https://www.google.com/search?q='.urlencode($data[_COLONNA_RAG_SOC].' indirizzo email contatti');
                    //visitaSito($listaEmail,$indirizzo,1);
                    vaisuGoogle($listaEmail, $data[_COLONNA_RAG_SOC]);
                    $stampa =  $data[_COLONNA_RAG_SOC].';'.$data[_COLONNA_SITO].';'.implode(",", array_unique($listaEmail)).';2'. "\n";
                    fwrite($h, $stampa);
                    
                }
                else
                {
                    $stampa =  $data[_COLONNA_RAG_SOC].';'.$data[_COLONNA_SITO].';'.$data[_COLONNA_EMAIL]. "\n";
                    fwrite($h, $stampa);
                }
                    
                
            }

         $a++;   
        /* if($a == 10) 
         {
                 fclose($handle);
                fclose($h);
         }*/
    }
}
die($stampa);
die('ferma');












