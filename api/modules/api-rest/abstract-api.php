<?php

require_once "exceptions.php";

abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */

    public function __construct($request) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
    /**
     * Conexion a la base
     */
      


        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $this->request = $this->_cleanInputs($_POST);
            break;
        case 'GET':
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            $this->file = file_get_contents("php://input");
            parse_str($this->file, $this->payload);
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }
    }

    public function processAPI() {
        try {

            if ($this->endpoint == "auth") {

                $token = exchange($_GET['username'], $_GET['password']);
                if ($token != null)
                {
                    $resp = array("token" => $token);
                    return $this->_response($resp, 200);
                } else {

                    // Invalid token
                    throw new InvalidCredentialsException();
                }

            } else {
                if ((int)method_exists($this, $this->endpoint) > 0) {
                    // check if the user has been authenticated
                    if ($this->User == null)
                        throw new UnauthorizedException();

                    // exec endpoint function
                   return $this->{$this->endpoint}($this->args);
                }

                // endpoint not found
                throw new NotFoundException();
            }

        // Exception Strategy
        } catch (BadRequestException $e){
            return $this->_response("Bad Request", 400);
        } catch(InvalidCredentialsException $e){
            return $this->_response("Invalid username or password", 401);
        } catch(UnauthorizedException $e){
            return $this->_response("Unauthorized", 401);
        } catch (NotFoundException $e) {
            return $this->_response("Resource Not Found", 404);
        } catch (SQLException $e){
            return $this->_response("SQL Error", 500);
        } catch(Exception $e){
            return $this->_response("Unexpected Application Error", 500);
        }
    }

    protected function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return "{ \"data\": " . json_encode($data) . ", \"status\": $status }";
    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',   
            401 => 'Unauthorized',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }
}

?>