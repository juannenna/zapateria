<?php
require_once 'modules/php-jwt/Exceptions/BeforeValidException.php';
require_once 'modules/php-jwt/Exceptions/ExpiredException.php';
require_once 'modules/php-jwt/Exceptions/SignatureInvalidException.php';
require_once 'modules/php-jwt/Authentication/JWT.php';
require_once 'db.php';

$secret = "shhhh";

function authenticate($jwt){
	return JWT::decode($jwt, $secret);
}

function exchange($username, $password){

	$jwt = null;
	$user = null;
	$db = $GLOBALS['db'];
	$conn = new mysqli($db->host, $db->usr, $db->pass, $db->dbname);

	$SQL = "SELECT id, rol_id, username FROM greits_usuarios WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($SQL);

    if($result )
    {
    	while($row = $result->fetch_assoc()){
        	$user = array(
        		'id' => $row['id'],
        		'rol_id' => $row['rol_id'],
        		'username' => $row['username']
        		);
        }
    }

    if ($user != null)
	{
		$jwt = JWT::encode($user, $secret);
	}

	$conn->close();
	
	return $jwt;
}

?>
