<?php

namespace rogercodeprogr\Model;
use \rogercodeprogr\DB\Sql;
use \rogercodeprogr\Model;
use \rogercodeprogr\Mailer;


class Category extends Model{


    //Listagem dos usuários
    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_categories order by descategory");
    }

    //Método para salvar as informações das categorias
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",array(
           ":idcategory"=>$this->getidcategory(),
           ":descategory"=>$this->getdescategory(),
           
        ));
        
        $this->setData($results[0]);

        Category::updateFile();
      //  return $results[0];
    }

    public function get($idcategory)
    {

        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",['idcategory'=>$idcategory]);       
        $this->setData($results[0]);

    }


    public function delete()
    {

        $sql = new Sql();
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory
            ",['idcategory'=>$this->getidcategory()
        ]);

        Category::updateFile();

    }

    //Método para mostrar automaticamente as categorias dos produtos no site
    public static function updateFile()
    {
        $categories = Category::listall();

        $html = [];
        foreach ($categories as $row)
        {          

           array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');  

        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

    }


    public  function getProducts($related = true)
    {


        $sql = new Sql();
        if ($related === true)
        {
            return $sql->select("select * from tb_products where idproduct  
                          in( select a.idproduct
                              from tb_products a
                              inner join tb_productscategories b on
                              a.idproduct = b.idproduct
                              where b.idcategory = :idcategory)
                    ",[
                        ':idcategory'=>$this->getidcategory()

                    ]);        

        }
        else
        {
            return $sql->select("select * from tb_products where idproduct  
                          not in( select a.idproduct
                              from tb_products a
                              inner join tb_productscategories b on
                              a.idproduct = b.idproduct
                              where b.idcategory = :idcategory)
                    ",[
                        ':idcategory'=>$this->getidcategory()

                    ]);        
        }


    }

    //Método para adicionar produto
    public function addProduct(Product $product)
    {

       $sql = new Sql();
       $sql->query("INSERT INTO tb_productscategories(idcategory,idproduct) values(:idcategory, :idproduct)",[ 
         ':idcategory'=>$this->getidcategory(),
         ':idproduct'=>$product->getidproduct()   
        ]); 

    }


    //Método para removerproduto
    public function removeProduct(Product $product)
    {

       $sql = new Sql();
       $sql->query("DELETE FROM tb_productscategories WHERE idcategory =:idcategory AND idproduct =:idproduct ",[         
         ':idcategory'=>$this->getidcategory(),
         ':idproduct'=>$product->getidproduct()   

        ]); 

    }

    
}

?>