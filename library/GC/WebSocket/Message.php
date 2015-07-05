<?php
namespace GC\WebSocket;



class Message
{

	protected $type;
	protected $data;

	public function __construct($type, $data) {
		$this->type=$type;
		$this->data=$data;
	}
	
	
	public function __toString() {
		
		return json_encode(
			array(
				'type'=>$this->type,
				'data'=>$this->data,
				'time'=>time()
			)
		);
	}
}




