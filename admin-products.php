<?php

use \rogercodeprogr\PageAdmin;
use \rogercodeprogr\Model\User;
use \rogercodeprogr\Model\Product;

//Rota para listagem de  produtos
$app->get("/admin/products",function(){

	//Verifica se está logado
	User::verifyLogin();

  $products = Product::listAll();
	$page     = new PageAdmin();
	$page->setTpl("products",[
		'products'=>$products

	]);

});

//Rota para carregar o formulário de cadastro de produtos
$app->get("/admin/products/create",function(){
   
   //Verifica se está logado
	User::verifyLogin();

	$page = new PageAdmin();
	$page->setTpl("products-create");

});


//Rota para salvar o produto
$app->post("/admin/products/create",function(){
   
   //Verifica se está logado
	User::verifyLogin();

  $product = new Product(); 

	$product->setData($_POST);
	$product->save();
	header("Location:/admin/products");
	exit;

});

//Rota para exclusão do produto
$app->get("/admin/products/:idproduct/delete",function($idproduct){

 //Verifica se está logado
  User::verifyLogin();
  $product = new Product();
  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto
  $product->get((int)$idproduct);
  $product->delete();
  header("Location:/admin/products");
  exit;

});

//Rota para edição do produto
  $app->get("/admin/products/:idproduct",function($idproduct){

  //Verifica se está logado
  User::verifyLogin();	

  $product = new Product();
  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto

  $product->get((int)$idproduct);
  $page = new PageAdmin();
  $page->setTpl("products-update",[
  	'product'=>$product->getValues()

  ]);


  
});

//Rota para salvar a edição

  $app->post("/admin/products/:idproduct",function($idproduct){

  //Verifica se está logado
	User::verifyLogin();	

  $product = new Product();

  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto
  $product->get((int)$idproduct);
  $page = new PageAdmin();

  var_dump($_POST);
  $product->setData($_POST);
  $product->save();

  
  //Fazer upload do arquivo
  //$product->setPhoto($_FILES['file']);


  if ((int)$_FILES["file"]["size"] > 0)
   {
        $product->setPhoto($_FILES["file"]);
   }





  //Redireciona para todos produtos
  header("Location:/admin/products");
  exit;
  
});



?>


