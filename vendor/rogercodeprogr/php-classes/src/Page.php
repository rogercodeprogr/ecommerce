<?php
    namespace rogercodeprogr;
    use Rain\Tpl;
    class Page{

        //Defina-se como privado para outras classes não terem acesso.
        private $tpl;
        private $options = [];
        private $default = [
            "header"=>true,
            "footer"=>true,
            "data"=>[]
        ];
        //Métodos mágicos
        public function __construct($opts = array(),$tpl_dir = "/views/"){
           
            $this->options = array_merge($this->default, $opts);
            
            $config = array(
                "tpl_dir"   => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,   
                "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/views-cache/", 
                "debug"     => false // set to false to improve the speed
            );

            //Variável $config passa as configurações para o Tpl
            Tpl::configure( $config );

            //Para ter acesso aos métodos de outras classes usa-se o $this
            $this->tpl = new tpl;
            

            $this->setData($this->options["data"]);
            
            //draw é o método do tpl
            if ($this->options["header"] === true) $this->tpl->draw("header");
        }

        public function __destruct(){
           if($this->options["footer"] === true) $this->tpl->draw("footer");
        }

        private function setData($data = array())
        {
            foreach($data as $key=>$value){
                $this->tpl->assign($key,$value);
            }
        }

        public function setTpl($name, $data = array(), $returnHtml=false)
        {
            $this->setData($data);
            return $this->tpl->draw($name,$returnHtml);
        }


    }



?>