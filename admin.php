<?php

use \rogercodeprogr\PageAdmin;
use \rogercodeprogr\Model\User;


//Rota para o login

//Rota para o pageAdmin
$app->get('/admin', function() {

	User::verifyLogin();
	
    //No momento que ele cria a instancia do objeto page por meio da classe Page, ele chama o construtor e carrega a header.

	$page = new PageAdmin();
	//carrega a index
	$page->setTpl("index");

});


$app->get('/admin/login', function(){

	$page = new PageAdmin([
		//Desabilitar o header e o footer
		//A página de login é outra
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("login");

});

$app->post('/admin/login',function(){
	User::login($_POST["login"],$_POST["password"]);
	//Redireciona para a página da administração
	header("Location:/admin");
	exit;

});

//Rota para fazer o logout
$app->get('/admin/logout',function(){
	User::logout();
	header("Location:/admin/login");
	exit;
});



//Rota para esqueceu a senha
$app->get('/admin/forgot', function(){
	
	$page = new PageAdmin([
		//Desabilitar o header e o footer
		//A página de login é outra
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot");

});

//Rota para esqueceu a senha - método post
$app->post('/admin/forgot',function(){		
	$user = new User();	
	$user = User::getForgot($_POST['email']);	
	header("Location:/admin/forgot/sent");
	exit;

});


$app->get("/admin/forgot/sent",function(){

	$page = new PageAdmin([
		//Desabilitar o header e o footer
		//A página de login é outra
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");

});







?>