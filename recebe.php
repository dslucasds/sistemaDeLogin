<?php

//var_dump($_POST);

require_once 'configDB.php';

//limpando os dados de entrada POST
function verificar_entrada($entrada){
    $saída = trim($entrada);//comando pra remover os espaço
    $saída = htmlspecialchars($saída);
    $saída = stripcslashes($saída);
    
    return $saída;
}

if(isset($_POST['action']) && $_POST['action'] == 'registro'){
    $nomeCompleto = verificar_entrada($_POST['nomeCompleto']);
    $nomeUsúario = verificar_entrada($_POST['nomeUsuario']);
    $emailUsúario = verificar_entrada($_POST['emailUsuario']);
    $senhaUsúario = verificar_entrada($_POST['senhaUsuario']);
    $senhaUsúarioConfirmar = verificar_entrada($_POST['senhaUsuarioConfirmar']);
    $criado = date('Y-m-d');//cria a data ano mes dia
    
    //gerar um hash para as senha
    $senha = sha1($senhaUsúario);
    $senhaConfirmar = sha1($senhaUsúarioConfirmar);
    
    echo "Hash: " . $senha;

    //conferencia de senha no backend
    if($senha != $senhaConfirmar){
        echo "Senhas nao conferem";
        exit();
    }else{
        //verificando se o usuario existe no banco de dados
        //usando o mySQL prepared statement
        $sql = $conexão->prepare("SELECT nomeUsuario, email FROM . usuario WHERE nomeUsuario = ? or "
                . "email = ?");//evitar injeção de sql
        $sql->bind_param("ss", $nomeUsúario, $emailUsúario);
        $sql->execute(); //metodo do objeto
        $resultado = $sql->get_result();//tabela de banco
        $linha = $resultado->fetch_array(MYSQLI_ASSOC);
        if($linha['nomeUsuario'] == $nomeUsúario){
            echo "Nome [$nomeUsuário] indisponível.";
        }else if($linha['email'] == $emailUsúario){
            echo 'E-mail($emailUsuário) indisponível.';
        }else{
            //preparam a inserção no banco de dados
            $sql = $conexão->prepare("insert into usuario(nome, nomeUsuario, email, senha, criado)"
                    . "values(?,?,?,?,?)");
            
            $sql->bind_param("sssss", $nomeCompleto, $nomeUsuario, $emailUsuario, $senha, $criado);
        }
        
        if($sql->execute()){
            echo "cxadastrado com sucesso";
        }else{
            echo "algo deu errado. por favor. tente novamente";
        }               
    }
    
}
