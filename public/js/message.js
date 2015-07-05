 
 function GC_ClientMessage(type, data) {
	this.type=type,
	this.data=data;
 }
 
 GC_ClientMessage.prototype.stringify=function() {
	return JSON.stringify({
		type: this.type,
		data:this.data
	});
 }
 