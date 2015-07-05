<?php
namespace GC\WebSocket;


use Wrench\Application\Application;



class Server extends Application
{

protected $lastTimestamp = null;
    /**
     * @see Wrench\Application.Application::onData()
     */

	protected $clients=array();

	protected $callbacks=array();


	public function on($eventName, $callBack) {
		$this->callbacks[$eventName]=$callBack;
	}
	
	
	
	protected function executeCallback($name, $parameters) {
		if(isset($this->callbacks[$name])) {
			return call_user_func_array(
				array($this->callbacks[$name], '__invoke'),
				$parameters
			);
		}
	}


    public function onConnect($client) {
        $this->clients[$client->getId()]=$client;
		$client->send((string) new Message('connect', array(
			'id'=>$client->getId()
		)));
		
		return $this->executeCallback('connect', array($this, $client));
		
    }


    public function onDisconnect($client) {
		unset($this->clients[$client->getId()]);
		return $this->executeCallback('disconnect', array($this, $client));
    }
	
	
	
	


    public function onData($data, $client) {
		$this->handleMessage($data, $client);
    }
	
	
    public function onUpdate() {
		return $this->executeCallback('update', array($this, null));
    }
	


	public function handleMessage($data, $client) {
		$data=json_decode($data, true);
		
		print_r($data);
		
		
		$type=$data['type'];
		
		$parameters['data']=$data['data'];
		
		if(isset($this->callbacks[$type])) {
		
			array_unshift($parameters, $client);
			array_unshift($parameters, $this);

			return call_user_func_array(array(
				$this->callbacks[$type], '__invoke'
			), $parameters);
		}
		else {
			//print_r($data);
		}
	}
	
	
	public function synchronize($fromClient, $messageType, $data) {
		$message=new Message($messageType, $data);
		if(count($this->clients)) {
			foreach($this->clients as $client) {	
				if($client->getId()!=$fromClient->getId()) {
					$client->send((string) $message);
				}
			}
		}
	}


	public function broadCast($messageType, $data) {
		$message=new Message($messageType, $data);
		if(count($this->clients)) {
			foreach($this->clients as $client) {	
				$client->send((string) $message);
			}
		}	
	}

	
	
	
	
	
	
}