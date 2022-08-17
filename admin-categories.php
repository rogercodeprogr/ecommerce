<?php

use \rogercodeprogr\PageAdmin;
use \rogercodeprogr\Model\User;
use \rogercodeprogr\Model\Category;
use \rogercodeprogr\Model\Product;

//Rota para listagem de  categorias
$app->get("/admin/categories",function(){

	//Verifica se está logado
	User::verifyLogin();

  $categories = Category::listAll();
	$page = new PageAdmin();
	$page->setTpl("categories",[
		'categories'=>$categories

	]);

});

//Rota para carregar o formulário de cadastro de categorias
$app->get("/admin/categories/create",function(){
   
   //Verifica se está logado
	User::verifyLogin();

	$page = new PageAdmin();
	$page->setTpl("categories-create");

});


//Rota para salvar a categoria
$app->post("/admin/categories/create",function(){
   
   //Verifica se está logado
	User::verifyLogin();

    $category = new Category();

	$category->setData($_POST);
	$category->save();
	header("Location:/admin/categories");
	exit;

});

//Rota para exclusão da categoria
$app->get("/admin/categories/:idcategory/delete",function($idcategory){

 //Verifica se está logado
  User::verifyLogin();
  $category = new Category();
  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto
  $category->get((int)$idcategory);
  $category->delete();
  header("Location:/admin/categories");
  exit;

});

//Rota para edição de categoria
  $app->get("/admin/categories/:idcategory",function($idcategory){

     //Verifica se está logado
  User::verifyLogin();	

  $category = new Category();
  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto

  $category->get((int)$idcategory);
  $page = new PageAdmin();
  $page->setTpl("categories-update",[
  	'category'=>$category->getValues()

  ]);
  
});

//Rota para salvar a edição

  $app->post("/admin/categories/:idcategory",function($idcategory){

  //Verifica se está logado
	User::verifyLogin();	

  $category = new Category();

  //A linha abaixo faz um casting, porque o id que está no navegador é interpretado como texto
  $category->get((int)$idcategory);
  $page = new PageAdmin();

  $category->setData($_POST);
  $category->save();

  //Redireciona para todas as categorias
  header("Location:/admin/categories");
  exit;
  
});




//Produtos X Categories
$app->get("/admin/categories/:idcategory/products", function($idcategory){  
  

  User::verifyLogin();
   
  $category = new Category();

  $category->get((int)$idcategory);

  $page = new PageAdmin();

  $page->setTpl("categories-products",[
    'category'=>$category->getValues(),
    'productsRelated'=>$category->getProducts(),
    'productsNotRelated'=>$category->getProducts(false)
  ]);
});


   //Rota para salvar produto na categoria
  $app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory,$idproduct){  

  User::verifyLogin();
   
  $category = new Category();
  $category->get((int)$idcategory);
  $product = new Product();
  $product->get((int)$idproduct);
  $category->addProduct($product);
  header("Location:/admin/categories/".$idcategory."/products");
  exit;


});

//Rota para remover o produto da categoria
  $app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory,$idproduct){  

  User::verifyLogin();
   
  $category = new Category();
  $category->get((int)$idcategory);
  $product = new Product();
  $product->get((int)$idproduct);             
  $category->removeProduct($product);
  header("Location:/admin/categories/".$idcategory."/products");
  exit;

});


?>


