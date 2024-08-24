<?php

class Result 
	{
    public $message;
    public $return_code;
	public function __construct(string $message, bool $return_code) 
		{
        $this->message = $message;
		$this->return_code = $return_code;
		}
	}


?>

