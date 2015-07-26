if (typeof Function.prototype.bind != 'function'){
        Function.prototype.bind = function(scope) {
        var _function = this;
        return function(){
            return _function.apply(scope, arguments);
        }
    }
}

if (typeof window.console == 'undefined' || typeof window.console.log != 'function'){
    window.console = {
        log: function(){
        }
    }
}

var IframeTransportAgent = function(){
    this.defaultLocationHash = '#';
    this.lastMessage = null;
    this.traceMessage();
    this.observer = setInterval(this.traceMessage.bind(this), 1000);
}

IframeTransportAgent.prototype = {
    traceMessage: function(){
        var message = this.parseMessage(window.location.hash);
        console.log('a.traceMessage: '+message);
        if (message){
            this.lastMessage = message;
            if (typeof this[this.lastMessage] == 'function'){
                this[this.lastMessage]();
            }else{
                //No action available
            }
            window.location.hash = this.defaultLocationHash;
        }
    },

    success: function(){
        console.log('a.success');
        var payment = window.top.payment;
        payment.isMoneybookersPspDataValid[payment.currentMethod] = true;
        payment.save();
    },

    parseMessage: function(hash){
        if (hash){
            var matches = hash.match(/#(.*)/);
            if (matches && matches[1] !== undefined){
                return matches[1];
            }
        }
        return null;
    },

    validationError: function(){
        window.top.checkout.setLoadWaiting(false);
    }
}