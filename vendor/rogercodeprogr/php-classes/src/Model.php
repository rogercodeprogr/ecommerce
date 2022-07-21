<?php
    namespace rogercodeprogr;

    class Model{
        private $values = [];

    public function __call($name, $args)
    {
        //É a definição de getter e setter para qualquer classe
        $method = substr($name,0,3);
        $fieldname = substr($name,3, strlen($name));
        switch($method)
        {
            case "get":
               return (isset($this->values[$fieldname])) ? $this->values[$fieldname]:null ;
            break;
            case "set":
                $this->values[$fieldname] = $args[0];
            break;    

        }


    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set".$key}($value);
        }
    }

    public function getValues()
    {
        return $this->values;
    }

    }




?>