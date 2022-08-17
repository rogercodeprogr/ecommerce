<?php

use \rogercodeprogr\Page;
use \rogercodeprogr\Model\Product;
use \rogercodeprogr\Model\Category;
use \rogercodeprogr\Model\User;


$app->get('/', function() {
    //No momento que ele cria a instancia do objeto page por meio da classe Page, ele chama o construtor e carrega a header.

	$products = Product::listAll();

	$page = new Page();
	//carrega a index
	$page->setTpl("index",[
		'products'=>Product::checklist($products)

	]);

});


 $app->get("/categories/:idcategory",function($idcategory){

  //Verifica se está logado
  User::verifyLogin();	

  $category = new Category();
  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto

  $category->get((int)$idcategory);
  $page = new Page();
  $page->setTpl("category",[
  	'category'=>$category->getValues(),
    'products'=>Product::checklist($category->getProducts())
  ]);
  
});



?>