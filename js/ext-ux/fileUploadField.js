/*
 * Ext JS Library 2.2
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.form.FileUploadField = Ext.extend(Ext.form.TextField,  {
    /**
     * @cfg {String} buttonText The button text to display on the upload button (defaults to
     * 'Browse...').  Note that if you supply a value for {@link #buttonCfg}, the buttonCfg.text
     * value will be used instead if available.
     */
    buttonText: '',
    /**
     * @cfg {Boolean} buttonOnly True to display the file upload field as a button with no visible
     * text field (defaults to false).  If true, all inherited TextField members will still be available.
     */
    buttonOnly: false,
    /**
     * @cfg {Number} buttonOffset The number of pixels of space reserved between the button and the text field
     * (defaults to 3).  Note that this only applies if {@link #buttonOnly} = false.
     */
    buttonOffset: 3,
    /**
     * @cfg {Object} buttonCfg A standard {@link Ext.Button} config object.
     */

    // private
    readOnly: true,
    
    /**
     * @hide 
     * @method autoSize
     */
    autoSize: Ext.emptyFn,
    
    // private
    initComponent: function(){
        Ext.form.FileUploadField.superclass.initComponent.call(this);
        
        this.addEvents(
            /**
             * @event fileselected
             * Fires when the underlying file input field's value has changed from the user
             * selecting a new file from the system file selection dialog.
             * @param {Ext.form.FileUploadField} this
             * @param {String} value The file value returned by the underlying file input field
             */
            'fileselected'
        );
    },
    
    // private
    onRender : function(ct, position){
        Ext.form.FileUploadField.superclass.onRender.call(this, ct, position);
        
        this.wrap = this.el.wrap({cls:'x-form-field-wrap x-form-file-wrap'});

        this.el.addClass('x-form-file-text');
        
        this.el.dom.removeAttribute('name');
        //nuevo en comparacion
		this.createFileInput(this.cls);
		//
		
        this.fileInput = this.wrap.createChild({
            id: this.getFileInputId(),
            name: this.name||this.getId(),
            cls: 'x-form-file ' + (this.cls ? this.cls : ''),
           // cls: 'x-form-file',
            tag: 'input', 
            type: 'file',
            size: 1
        });

        var btnCfg = Ext.applyIf(this.buttonCfg || {}, {
            text: this.buttonText
        });
        
        this.button = new Ext.Button(Ext.apply(btnCfg, {
            renderTo: this.wrap,
            cls: 'x-form-file-btn' + (btnCfg.iconCls ? ' x-btn-icon' : '')
        }));
        
        if(this.buttonOnly){
            this.el.hide();
            
            this.wrap.setWidth(this.button.getEl().getWidth());
        }
        
        this.fileInput.on('change', function(){
            var v = this.fileInput.dom.value;
            this.setValue(v);
            this.fireEvent('fileselected', this, v);
        }, this);
    },
    
    // private
    getFileInputId: function(){
        return this.id+'-file';
    },
    
    // private
    onResize : function(w, h){
        Ext.form.FileUploadField.superclass.onResize.call(this, w, h);
        
        this.wrap.setWidth(w);
        
        if(!this.buttonOnly){
        	
			var bw = this.button.getEl().getWidth();
			if(bw==0){bw=24;}
			//var w = this.wrap.getWidth() - this.button.getEl().getWidth() - this.buttonOffset;
			var w = w - bw - this.buttonOffset;
            this.el.setWidth(w);			
        }
    },// private
	preFocus : Ext.emptyFn,
    
    // private
    getResizeEl : function(){
        return this.wrap;
    },

    // private
    getPositionEl : function(){
        return this.wrap;
    },

    // private
    alignErrorIcon : function(){
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2, 0]);
    },
	//nuevo tambien
	createFileInput : function(cls) {

            var cls=(this.initialConfig.cls ? this.initialConfig.cls + ' fileinput' : 'fileinput');
		this.fileInput = this.wrap.createChild({
			id: this.getFileInputId(),
			name: this.name||this.getId(),
			cls: 'x-form-file ' + (cls ? cls : ''),
			tag: 'input',
			type: 'file',
			size: 1
		});
		if(this.disabled){
			this.fileInput.dom.disabled = true;
		}

	},
	addFileListener : function() {
		this.fileInput.on({
			change: function(){
				var v = this.fileInput.dom.value;
				this.setValue(v);
				this.fireEvent('fileselected', this, v);
			},
			mouseover: function() {
                        //    if (!this.button.disabled){
                                this.button.addClass(['x-btn-over','x-btn-focus']);
                         //   }
				
			},
			mouseout: function(){
				this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click']);
			},
			mousedown: function(){
                         //   if (!this.button.disabled){
                                this.button.addClass('x-btn-click');
                         //   }
				
			},
			mouseup: function(){
				this.button.removeClass(['x-btn-over','x-btn-focus','x-btn-click']);
			},
			scope : this
		});
	},
	reset : function(){  //parche de la web para el reset            
		this.fileInput.removeAllListeners();
		this.fileInput.remove();
		this.createFileInput();
		this.addFileListener();
		Ext.form.FileUploadField.superclass.reset.call(this);
	},
	onDestroy : function(){
        if(this.fileInput){
            Ext.destroy(this.fileInput);
        }
		if(this.button){
			this.button.destroy();
		}
        Ext.form.FileUploadField.superclass.onDestroy.call(this);
    },
	onEnable: function(){
		Ext.form.FileUploadField.superclass.onEnable.call(this);
		this.fileInput.dom.disabled = false;
		this.button.enable();
	},
	onDisable: function(){
		Ext.form.FileUploadField.superclass.onEnable.call(this);
		this.fileInput.dom.disabled = true;
		this.button.disable();
	}
	//fin de lo nuevo
    
});
Ext.reg('fileuploadfield', Ext.form.FileUploadField);