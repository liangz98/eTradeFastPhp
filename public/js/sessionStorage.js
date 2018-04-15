var Storage = {
     isSessionStorage : window.sessionStorage ? true : false,
     set:function(key,value){
        if(this.isSessionStorage){
            window.sessionStorage.setItem(key,value);
        }
     },
     get:function(key){
       if(this.isSessionStorage){
            return window.sessionStorage.getItem(key);
        }
     },
     remove:function(key){
        if(this.isSessionStorage){
             if(typeof(key) == 'string'){
                window.sessionStorage.removeItem(key);
             }else{
                this.clear();
             }
        } 
     },
     clear:function(){
        window.sessionStorage.clear();
     }
}