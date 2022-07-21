<?php

use \rogercodeprogr\PageAdmin;
use \rogercodeprogr\Model\User;

//Rota para listar todos os usuários
$app->get('/admin/users',function(){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
	//Chama o método para listar todos os usuários
	$users = User::listAll();
    $page = new PageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));

});

//Rota para create
$app->get('/admin/users/create',function(){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
    $page = new PageAdmin();
	$page->setTpl("users-create");

});

//Rota para excluir
$app->get('/admin/users/:iduser/delete',function($iduser){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);	
    $user->delete();
	header("Location:/admin/users");
	exit;
    
});


//Rota para editar. É para trazer o formulário preenchido.
$app->get('/admin/users/:iduser/',function($iduser){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
    $page = new PageAdmin();
	$page->setTpl("users-update",array(
		"user"=>$user->getValues()
	));

});

//Rota para salvar
$app->post('/admin/users/create',function(){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
	
    $user = new User();
	$_POST['inadmin'] = (isset($_POST["inadmin"]))?1:0;
	$user->setData($_POST);
    $user->save();
	header("Location:/admin/users");
	exit;

});

//Rota para salvar a edição do usuário
$app->post('/admin/users/:iduser',function($iduser){
	
	//Chama o método verifyLogin para verificar se está logado
	User::verifyLogin();
	$user = new User();
	//Faz o casting - Quando o id está na URL está no formato texto
	$user->get((int)$iduser);
	$_POST['inadmin'] = (isset($_POST["inadmin"]))?1:0;
	$user->setData($_POST);
    $user->update();
	header("Location:/admin/users");
	exit;


});

?>


