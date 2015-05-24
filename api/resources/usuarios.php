<?php

// db
require_once(__DIR__."/../db.php");
require_once(__DIR__."/../exceptions.php");


// usuarios

function getUsuario($id)
{
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	$SQL = "SELECT * FROM greits_usuarios WHERE id = $id";
    $result = $conn->query($SQL);

    $conn->close();

    if ($result === false)
    	throw new SQLException();

    $resp = $result->fetch_assoc();

    if (sizeof($resp) == 0)
    	throw new NotFoundException();

    return $resp;
}

function getUsuarios()
{
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	$SQL = "SELECT * FROM greits_usuarios";
    $result = $conn->query($SQL);

    $conn->close();

    if ($result === false)
    	throw new SQLException();

    $list = array();

    while ($row = $result->fetch_assoc()){
    	array_push($list, $row);
    }

    $resp = array("list" => $list);

    return $resp;
}

function insertUsuario()
{
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	$username = $_POST['username'];
	$password = $_POST['password'];
	$rolId = $_POST['rolId'];

	if ($username == null || $password == null || $rolId == null)
		throw new BadRequestException();

	$SQL = "INSERT INTO greits_usuarios (username, password, rol_id) VALUES ('$username', '$password', $rolId)";

	$result = $conn->query($SQL);
	$id = $conn->insert_id;

    $conn->close();

    if ($result === false)
    	throw new SQLException();

    return array("id" => $id);
}

function updateUsuario($id, $payload)
{
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	$username = $payload['username'];
	$password = $payload['password'];
	$rolId = $payload['rolId'];

	if ($id == null || $username == null || $password == null || $rolId == null)
		throw new BadRequestException();

	$SQL = "UPDATE greits_usuarios SET username = '$username', password = '$password', rol_id = $rolId WHERE id = $id";

	$result = $conn->query($SQL);
    $conn->close();

    if ($result === false)
    	throw new SQLException();

    return "";	
}

function removeUsuario($id)
{
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	if ($id == null)
		throw new BadRequestException();

	$SQL = "DELETE FROM greits_usuarios WHERE id = $id";

	$result = $conn->query($SQL);
    $conn->close();

    if ($result === false)
    	throw new SQLException();

    return "";	
}



?>