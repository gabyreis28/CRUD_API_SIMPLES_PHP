<?php

/*
 Author: Gabriela Dias
 Description: API CRUD Rest [GET, POST, PUT, DELETE]
*/

define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('BANCO', 'cadastro');

$conn = new mysqli(HOST, USER, PASS, BANCO); // connexão com banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['id'])) {
        $id     = $conn->real_escape_string($_GET['id']);
        $sql    = $conn->query("SELECT * FROM usuario WHERE id='$id'  "); //ORDER BY $id
        $result = $sql->fetch_assoc();
    } else {
        $result = array();
        $sql    = $conn->query("SELECT * FROM usuario");
        while ($r = $sql->fetch_assoc()) {
            $result[] = $r;
        }
    }
    exit(json_encode($result));
					 
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['nome']) && isset($_POST['sexo']) && isset($_POST['email'])) {
        $nome  = $conn->real_escape_string($_POST['nome']);
        $sexo  = $conn->real_escape_string($_POST['sexo']);
        $email = $conn->real_escape_string($_POST['email']);

        $conn->query("INSERT INTO usuario (nome,sexo,email) VALUES ('$nome','$sexo','$email') ");
        exit(json_encode(array(
            "status" => "sucesso",
            "nome" => $nome,
            "sexo" => $sexo,
            "email" => $email
        )));
    } else {
        exit(json_encode(array("status" => "erro")));
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
	
    if (!isset($_GET['id'])) exit(json_encode(array("status" => false, "mensagem" => "Necessario passar o <ID>")));

    $result = urldecode(file_get_contents('php://input', true));

    $id = $conn->real_escape_string($_GET['id']);

    if (strpos($result, '=') !== false) {

        $allPairs = array();
        $result   = explode('&', $result);

        foreach ($result as $pair) {
            $pair               = explode('=', $pair);
            $allPairs[$pair[0]] = $pair[1];
        }

        if (isset($allPairs['nome']) && isset($allPairs['sexo']) && isset($allPairs['email'])) {
            $conn->query("UPDATE usuario SET nome='" . $allPairs['nome'] . "', email='" . $allPairs['email'] . "' , sexo='" . $allPairs['sexo'] . "' WHERE id='$id' ");
        } else if (isset($allPairs['nome'])) {
            $conn->query("UPDATE usuario SET nome='" . $allPairs['nome'] . " 'WHERE id='$id' ");
        } else if (isset($allPairs['sexo'])) {
            $conn->query("UPDATE usuario SET sexo='" . $allPairs['sexo'] . " 'WHERE id='$id' ");
        } else if (isset($allPairs['email'])) {
            $conn->query("UPDATE usuario SET email='" . $allPairs['email'] . " 'WHERE id='$id' ");
        } else {
			exit(json_encode(array("status" => false, "mensagem" => "Não foi localizado nenhum valor input!")));
		}
		
        exit(json_encode(array("status" => 'atualizado com sucesso')));
		
    } else{
        exit(json_encode(array("status" => false, "mensagem" => "Necessario verificar os parametros BODY")));
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
	
    if (!isset($_GET['id'])) exit(json_encode(array("status" => false, "mensagem" => "Necessario passar o <ID>")));

    $id = $conn->real_escape_string($_GET['id']);
    $conn->query("DELETE FROM usuario WHERE id='$id' ");
	
    exit(json_encode(array("status" => false, "mensagem" => "Excluido com sucesso")));
}
