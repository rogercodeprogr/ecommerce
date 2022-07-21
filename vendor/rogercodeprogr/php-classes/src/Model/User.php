<?php

namespace rogercodeprogr\Model;
use \rogercodeprogr\DB\Sql;
use \rogercodeprogr\Model;
use \rogercodeprogr\Mailer;

class User extends Model{

    const SESSION = "User";
    const SECRET = "HcodePhp7_Secret";
    const SECRET_IV = "HcodePhp7_Secret_IV";

    public static function login($login, $password)
    {
        $sql = new Sql();
        $result = $sql->Select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
        ));

        if(count($result) === 0){
            //\Exception é para ele pegar a exceção da classe principal
            throw new \Exception("Usuário inexistente ou senha inválida", 1);
        }
        
        //Ele pega na posição 0;
        $data = $result[0];

        if(password_verify($password,$data['despassword']))
        {
            $user = new User();
            $user->setData($data);
            $_SESSION[User::SESSION] = $user->getValues();
            return $user;
           
        }
        else{
            throw new \Exception("Senha inválida", 1);
        }

    }

    public  static function  verifyLogin($inadmin = true)
      //O parâmetro $inadmin verifica se o usuário que se logou é da administração
      //porque se logou na loja, não pode se logar na administração
    {
        if(
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)($_SESSION[User::SESSION]["iduser"] > 0)
            ||  //Verifica se o usuário é da administração
            !(bool)($_SESSION[User::SESSION]["inadmin"] !==$inadmin)
        )
        {
            header("location:/admin/login");
            exit;   
        }  

    }

    public static function  logout()
    {
        $_SESSION[User::SESSION] = null;
    }

    //Listagem dos usuários
    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) order by b.desperson");
    }

    //Método para salvar os dados dos usuários no banco
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
           ":desperson"=>$this->getdesperson(),
           ":deslogin"=>$this->getdeslogin(),
           ":despassword"=>$this->getdespassword(),
           ":desemail"=>$this->getdesemail(),
           ":nrphone"=>$this->getnrphone(),
           ":inadmin"=>$this->getinadmin()
        ));
        
        
        $this->setData($results[0]);
      //  return $results[0];
    }

    public function get($iduser)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser",
        array(":iduser"=>$iduser));
        
        $this->setData($results[0]);
    }

    //Método para salvar os registros
    public function update()
    {

        $sql = new Sql();
        $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
           ":iduser"=>$this->getiduser(), 
           ":desperson"=>$this->getdesperson(),
           ":deslogin"=>$this->getdeslogin(),
           ":despassword"=>$this->getdespassword(),
           ":desemail"=>$this->getdesemail(),
           ":nrphone"=>$this->getnrphone(),
           ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);

    }

    //Método para excluir
    public function delete()
    {
        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)",array(
            ":iduser"=>$this->getiduser()
        ));

    }

    //Método para recuperar a senha no email
    public static function getForgot($email, $inadmin = true)
    {
         $sql = new Sql();
         echo "Passando por aqui1";
         $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users USING(idperson) WHERE a.desemail = :email",array(":email"=>$email));

         if(count($results) === 0)
         {
             throw new \Exception("Não foi possível recuperar a senha");             
         }
         else
         {
            
            //var_dump($results[0]);
            $data     = $results[0];

            //Exemplo do código da hcode
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            //Fim do exemplo código da hcode


            //Exemplo do meu código

            /*$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser,:desip)",
                 array(":iduser"=>$data["iduser"],
                       ":idesip"=>$_SERVER["REMOTE_ADDR"]
         ));Fim do exemplo do meu código   */
         }

        // var_dump($results2[0]);
         /*Codigo do git hub hcode

         $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));*/


         //Fim do código git hub hcode


      
         
         if(count($results2) === 0)
         {
             throw new \Exception("Não foi possível recuperar a senha");
         }
         else
         {
             echo "Passando por aqui 4 ";    
             $dataRecovery = $results2[0];

            /* $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,User::SECRET,$dataRecovery["idrecovery"],MCRYPT_MODE_ECB));*/


             //Código do hcode git hub

             $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

             //Fim do código hcode git hub





             $link =  "localhost:8080/admin/forgot/reset?code=$code";

             $mailer = new Mailer($data["desmail"],$data["desperson"],"Redefinir senha Hcode Store","forgot",array(
                 "name"=>$data["desperson"],
                 "link"=>$link    
             ));
             



        $sql = new Sql();

        $results = $sql->select("
            SELECT *
            FROM tb_persons a
            INNER JOIN tb_users b USING(idperson)
            WHERE a.desemail = :email;
        ", array(
            ":email"=>$email
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha.");
            
        }
        else
        {

            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            if (count($results2) === 0)
            {

                throw new \Exception("Não foi possível recuperar a senha");

            }
            else
            {

                $dataRecovery = $results2[0];
                

               // $code = base64_encode(openssl_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

                //Código novo
               $code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

               $code = base64_encode($code);
                //Fim do código novo

                if ($inadmin === true) {
                    
                    //$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
                    $link = "http://localhost:8080/admin/forgot/reset?code=$code";

                } else {

                    $link = "http://localhost:8080/forgot/reset?code=$code";
                    //$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";

                }


                $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Hcode Store", "forgot", array(
                    "name"=>$data["desperson"],
                    "link"=>$link
                ));

                $mailer->send();
                return $data;

         }
     }
         
}


}
    
}

?>