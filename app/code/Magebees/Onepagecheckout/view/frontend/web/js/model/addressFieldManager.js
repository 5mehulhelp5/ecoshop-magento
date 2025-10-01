define([
    'jquery'
], function($) {
    window.oscAddress = {
        init: function(options) {
			const obj = JSON.parse(options);
            if(obj){
                this.options = obj;
            }
            if(obj.oneFields){
                this.oneFields = obj.oneFields;
            }
            if(obj.twoFields){
                this.twoFields = obj.twoFields;
            }
            if(obj.lastFields){
                this.lastFields = obj.lastFields;
            }
        },
        fieldAfterRender: function(fieldName){
            if($(".field[name='"+fieldName+"']").length > 0){
                if(this.isTwoField(fieldName)){
                    $(".field[name='"+fieldName+"']").addClass('two-fields');
                }else{
					$(".field[name='"+fieldName+"']").addClass('one-field');
                }
                if(this.isLastField(fieldName)){
                    $(".field[name='"+fieldName+"']").addClass('last');
                }
            }
        },
        isOneField: function(fieldName){
            if(this.oneFields){
                var found = false;
                $.each(this.oneFields, function(){
                    if(fieldName.match('.'+this+'$')){
                        found = true;
                        return true;
                    }
                })
                return found;
            }
            return false;
        },
        isTwoField: function(fieldName){
            if(this.twoFields){
                var found = false;
                $.each(this.twoFields, function(){
                    if(fieldName.match('.'+this+'$')){
                        found = true;
                        return true;
                    }
                })
                return found;
            }
            return false;
        },
        isLastField: function(fieldName){
            if(this.lastFields){
                var found = false;
                $.each(this.lastFields, function(){
					if(fieldName.match('.'+this+'$') != "" && fieldName.match('.'+this+'$') != null){
                        found = true;
                        return true;
                    }
                })
                return found;
            }
            return false;
        }
    };
    return window.oscAddress;
});