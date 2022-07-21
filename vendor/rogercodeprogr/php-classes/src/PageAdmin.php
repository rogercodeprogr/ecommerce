<?php

namespace rogercodeprogr;
class PageAdmin extends Page{

 //Métodos mágicos
 public function __construct($opts = array(),$tpl_dir = "/views/admin/"){
    parent::__construct($opts,$tpl_dir);
 }
}

?>