 function GC_Client(url, login) {
 	this.url=url;
	this.id=null;
	this.socket=null;
	this.login=login;
	this.handlers={};
 }
 
  
 
 GC_Client.prototype.send=function(messageType, data) {
	this.socket.send(new GC_ClientMessage(messageType, data).stringify());
 }
 
 
 GC_Client.prototype.on=function(eventName, callback) {
	this.handlers[eventName]=callback;
 }
 
 
 
 GC_Client.prototype.handleMessage=function(message) {
	var data=JSON.parse(message.data);

	if(typeof(this.handlers[data.type])!='undefined') {
		//console.debug('handling');
		return this.handlers[data.type](data);
	}
	else {
		//console.debug(data);
	}
  }
  
  


GC_Client.prototype.close=function() {
	this.socket.close();
}
 
 GC_Client.prototype.connect=function() {
	this.socket=new WebSocket(this.url);
	
	var self=this;
	
	this.socket.onmessage=function(message) {
		self.handleMessage(message);
	}
 }

 