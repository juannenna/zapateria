<?php
class NotFoundException extends Exception {
	public function errorMessage(){ 
		return "Resource Not Found";
	}
}
class BadRequestException extends Exception {
	public function errorMessage(){ 
		return "Bad Request";
	}
}
class SQLException extends Exception {
	public function errorMessage(){ 
		return "SQL Error";
	}
}
class UnauthorizedException extends Exception {
    public function errorMessage(){ 
        return "Unauthorized";
    }
}

class InvalidCredentialsException extends Exception {
    public function errorMessage(){ 
        return "Unauthorized";
    }
}
?>