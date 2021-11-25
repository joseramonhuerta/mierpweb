if (window.btoa === undefined ) {
	Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
	window.atob = function(){ return Base64.decode.apply(Base64, [].slice.call(arguments)) };
	window.btoa = function(){ return Base64.encode.apply(Base64, [].slice.call(arguments)) };
}

LocalPrint = Ext.extend(Ext.util.Observable, {

	port: 8100,
	protocol: 'atica',
	version: '0.0.0',
	_version: '0.0.0',
	winPath: '',
	macPath: '',
	debPath: '',
	callback:'',
	
	constructor: function(config){
		Ext.apply(this, config || {});
		
		// Eventos
		this.addEvents(
			'installed',
			'notinstalled',
			'version',
			'mac',
			'print',
			'beforeinstall'
		);
		
		// callbacks
		var me = this;
		this.onVersionName = 'onVersion_'+(new Date()*1);
		window[this.onVersionName] = function(){ me.onVersion.apply(me, [].slice.call(arguments)); }
		
		this.onInstallName = 'onInstall_'+(new Date()*1);
		window[this.onInstallName] = function(){ me.onInstall.apply(me, [].slice.call(arguments)); }
		
		this.onMacName = 'onMac_'+(new Date()*1);
		window[this.onMacName] = function(){ me.onMac.apply(me, [].slice.call(arguments)); }
		
		this.onPrintName = 'onPrint_'+(new Date()*1);
		window[this.onPrintName] = function(){ me.onPrint.apply(me, [].slice.call(arguments)); }
		
		this.onCloseName = 'onClose_'+(new Date()*1);
		window[this.onCloseName] = function(){ me.onClose.apply(me, [].slice.call(arguments)); }

		this.onSmartCardName = 'onSmartCard_'+(new Date()*1);
		window[this.onSmartCardName] = function(){ me.onSmartCard.apply(me, [].slice.call(arguments)); }
		
		if (this.id == undefined) 
			throw("Falta el id del panel a mostrar");
		else
			this.init();
	},
	
	init: function(){
		if(Ext.isWindows){
			Ext.get(this.id+'_descarga').set({ href: this.winPath });
		}
		
		if(Ext.isMac){
			Ext.get(this.id+'_descarga').set({ href: this.macPath });
		}
		
		if(Ext.isLinux){
			Ext.get(this.id+'_descarga').set({ href: this.debPath });
		}
		
		Ext.get(this.id+'_close').on('click', this.closePanel, this);
		
		this.listen();
		this.reintentos = 0;
		this.getVersion();
		this.on('version', function(response){
			if (!this.isUpdated()){
				this.closeService();
				this.install();
			}
			else {
				this.fireEvent('installed', response);
			}
		}, this, { single: true });
	},
	
	install:function(){
		this.fireEvent('beforeinstall');
		Ext.fly(this.id).fadeIn({
			duration:0.3,
			callback: function(){
				//Forzar descarga dependiendo del sistema operativo del usuario
				if(Ext.isWindows){
					document.location='ImpresionLocal/LocalPrint.msi';
				}
				
				if(Ext.isMac){
					document.location='ImpresionLocal/localprint.dmg';
				}
				
				if(Ext.isLinux){
					document.location='ImpresionLocal/localprint.deb';
				}
			}
		});
	},
	
	onInstall: function(response){
		this.failFlag = false;
		this._version = response.version;
		this.isUpdated();
		if(this.updated)
			this.fireEvent('installed');
		else
			this.fireEvent('notinstalled');
	},
	
	waitForInstall: function(){
		var script = document.createElement('script');
		var comando = { "accion": "Version", "callback": this.onInstallName }
		var me = this;
		
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		
		script.onerror = function() {
			me.waitForInstall();
		}

		this.failFlag = true;

		script.onreadystatechange= function () {
			if (script.readyState == 'loaded') {
				script.onreadystatechange = function () { }
				if (me.failFlag){
					me.waitForInstall();
				}
			}
		}

		document.body.appendChild(script);
	},
	
	listen: function(){
		var iframe = document.createElement('iframe');
		var comando = { "accion": "Escucha", "port": this.port }
		
		iframe.src = 'javascript:document.location=\'atica://' + btoa(Ext.encode(comando)) + '\';';
		iframe.style.display = "none";
		document.body.appendChild(iframe);
	},
	
	isUpdated: function(){
		var version = this.version.split('.'),
			_version = this._version.split('.');
		
		if (parseInt(version[0]) > parseInt(_version[0]) || parseInt(version[1]) > parseInt(_version[1]) || parseInt(version[2]) > parseInt(_version[2]))
			this.updated = false;
		else
			this.updated = true;
		
		return this.updated;
	},
	
	onVersion: function(response){
		this.failFlag = false;
		this._version = response.version;
		this.fireEvent('version');
		
	},
	
	getVersion: function(){
		var script = document.createElement('script');
		var comando = { "accion": "Version", "callback": this.onVersionName }
		var me = this;
		
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		
		me.reintentos++;
		script.onerror = function() {
			if (me.reintentos >= 20){
				me.install();
			}
			else {
				me.getVersion();
			}
		}

		this.failFlag = true;

		script.onreadystatechange= function () {
			if (script.readyState == 'loaded') {
				script.onreadystatechange = function () { }
				if (me.failFlag){
					if (me.reintentos >= 20){
						me.install();
					}
					else {
						me.getVersion();
					}
				}
			}
		}

		document.body.appendChild(script);
	},
	
	onMac: function(response){
		this.fireEvent('mac', response);
	},
	
	getMac: function(){
		if(!this.updated) return;
		
		var script = document.createElement('script');
		var comando = { "accion": "GetMac", "callback": this.onMacName }
		var me = this;
		
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		
		document.body.appendChild(script);
	},
	
	directPrint: function(config){
		if(!this.updated) return;
		
		var iframe = document.createElement('iframe');
		var comando = Ext.apply(config, { "accion": "Imprimir"});
		iframe.src = 'javascript:document.location=\'atica://' + btoa(Ext.encode(comando)) + '\';';
		iframe.style.display = "none";
		document.body.appendChild(iframe);
	},
	
	onPrint: function(response){
		this.fireEvent('print', response);
	},
	// { "nombreImpresora": "PDF", "datos": "http://www.upc.tax/test.pdf", "grafica": 1 }
	print: function(config){
		if(!this.updated) return;
		
		var script = document.createElement('script');
		var comando = Ext.apply(config, { "accion": "Imprimir","callback": this.onPrintName})
		var me = this;
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		document.body.appendChild(script);
	},
	
	closePanel: function(){
		Ext.fly(this.id).fadeOut({
			duration:0.3
		});
		
		this.listen();
		
		this.waitForInstall();
	},
	
	onClose: function(response){},
	
	closeService: function(){
		var script = document.createElement('script');
		var comando = { "accion": "Close", "callback": this.onCloseName }
		var me = this;
		
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		
		document.body.appendChild(script);
	},

	smartCard: function(config) {
		var script = document.createElement('script');
		var comando = Ext.applyIf(config,{callback:this.onSmartCardName});
		script.src = 'http://localhost:8100/?comm=' + btoa(Ext.encode(comando)) + '&t='+(new Date()*1);
		document.body.appendChild(script);
	},

	onSmartCard: function(response) {
		this.fireEvent('smartcard', response);
	}
});

	
