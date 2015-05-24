<?php
require_once 'modules/api-rest/abstract-api.php';
require_once 'crud.php';
require_once 'login.php';
require_once 'exceptions.php';

class MyAPI extends API
{
    protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);

        // Authentication
        $jwt = null;
        foreach (getallheaders() as $name => $value) {
            if ($name == 'Authorization') {
                $split = explode(" ", $value);
                if (sizeof($split) == 2)
                {
                    $jwt = $split[1];
                }
            }
        }

        if ($jwt != null)
        {
            try {
                $this->User = authenticate($jwt);
            } catch (Exception $e) {
                $this->User = null;
            }
        } else {
            $this->User = null;
        }
    }

    // api/users
    protected function usuarios() {

        if ($this->User->rol_id != 2)
            throw new UnauthorizedException();

        if ($this->method == 'GET') {
            if (sizeof($this->args) > 0){
                return $this->_response(getUsuario($this->args[0]), 200);
            } else {
                return $this->_response(getUsuarios(), 200);
            }
        }
        if ($this->method == 'POST') {
            return $this->_response(insertUsuario(), 201);
        }
        if ($this->method == 'PUT') {
            return $this->_response(updateUsuario($this->args[0], $this->payload), 200);
        }
        if ($this->method == 'DELETE') {
            return $this->_response(removeUsuario($this->args[0]), 200);
        }
    }
}
?>