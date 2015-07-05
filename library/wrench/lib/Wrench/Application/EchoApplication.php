<?php

namespace Wrench\Application;

use Wrench\Application\Application;
use Wrench\Application\NamedApplication;

/**
 * Example application for Wrench: echo server
 */


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



class EchoApplication extends Application
{

protected $lastTimestamp = null;
    /**
     * @see Wrench\Application.Application::onData()
     */

	protected $clients=array();


    public function onDisconnect($client)
    {
		unset($this->clients[$client->getId()]);
		echo "disconnected : ".$client->getId()."\n\n";
		$this->sendUserList();
    }


    public function onData($data, $client) {
		$this->handleMessage($data, $client);
    }
	
	
    public function onUpdate() {
    }
	
    public function onConnect($client) {
        $this->clients[$client->getId()]=$client;
		$client->send((string) new Message('connection', array(
			'id'=>$client->getId()
		)));
    }

	public function handleMessage($data, $client) {
		$data=json_decode($data, true);
		
		$type=$data['type'];
		
		if(method_exists($this, $type)) {
		
			array_unshift($data['data'], $client);
		
			return call_user_func_array(array(
				$this, $type
			), $data['data']);
		}
	}
	


	public function broadCast($message) {
	
		if(count($this->clients)) {
			foreach($this->clients as $client) {	
				$client->send((string) $message);
			}
		}	
	}


	public function sendUserList() {
			
		$clients=array();
		foreach($this->clients as $client) {
			$clients[]=array(
				'id'=>$client->getId(),
				'login'=>$client->login
			);
		}
		$message=new Message('userList', array(
			'clients'=>$clients
		));
		$this->broadCast($message);
	}

	public function register($client, $login) {
		$client->login=$login;
		$this->sendUserList();
	}
	
	public function message($client, $message) {
		$message=new Message('message', array(
			'message'=>$message,
			'client'=>array(
				'id'=>$client->getId(),
				'login'=>$client->login
			)
		));
		$this->broadCast($message);

	}

	
	
	
	
	
	
	
}