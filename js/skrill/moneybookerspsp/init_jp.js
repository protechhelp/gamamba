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

var IframeTransport = function(){
    this.parent = parent;
    this.defaultLocationHash = '#';
    this.lastMessage = null;
    this.traceMessage();
    this.observer = setInterval(this.traceMessage.bind(this), 1000);
    this.agentUrl = this.parent.document.referrer.split('checkout/onepage')[0]+'moneybookerspsp/processing/agent';
    this.transportAgent = null;
}

IframeTransport.prototype = {
    traceMessage: function(){
        var message = this.parseMessage(this.parent.location.hash);
        //console.log('t.traceMessage: '+message);
        if (message){
            this.lastMessage = message;
            if (typeof this[this.lastMessage] == 'function'){
                this[this.lastMessage]();
            }
            this.parent.location.hash = this.defaultLocationHash;
        }
    },

    save: function(){
        //window.save();
        console.log('t.save');
        var errorMessage = validate();
        if (errorMessage != '')
		{
            alert(errorMessage);
            var message = '#validationError';
            this.respond(message);
		}
		else
		{
            var frmTag = getElem('id', 'frontendForm', 0);
            showWaiting();
            frmTag.submit();
            //var message = '#success';
            //this.respond(message);
        }
    },

    parseMessage: function(hash){
        if (hash){
            var matches = hash.match(/#(.*)/);
            if (matches && typeof matches[1] != 'undefined'){
                return matches[1];
            }
        }
        return null;
    },

    getAgent: function(){
        if (this.transportAgent === null){
            console.log('new agent');
             var agent = document.createElement('iframe');
             agent.setAttribute('style', 'display: none;');
             agent.setAttribute('src', this.agentUrl);
             //agent.appendChild(document.createTextNode("Next page"));
             document.body.appendChild(agent);
             this.transportAgent = agent;
        }
        return this.transportAgent;
    },

    respond: function(response){
        this.getAgent().setAttribute('src', this.agentUrl+response);
    },

    destruct: function(){
        if (this.observer){
            clearInterval(this.observer);
        }
    }
}

function init()
{
    // The method “getElem” can be used to get an item on the page by its id
    var hiddenElements = ['paymentSelection','contactBlock','userInfoBlock','addressBlock',
        'buttonarea','notMandatoryRow', 'spacer1', 'spacer2', 'spacer3', 'spacer4'];
    for (var key in hiddenElements){
        var element = getElem('id', hiddenElements[key], 0);
        if (element){
            element.style.display="none";
        }
    }

    window.adjustTabView = function(onload)
    {
        setSendButtonState();

        showDefaultData();
        adjustPMView(onload);
        updateHolder();
        updateEmail();

        if (typeof window.messageTransport != 'undefined'){
            window.messageTransport.destruct();
            delete window.messageTransport;
        }

        console.log('new IframeTransport');
        window.messageTransport = new IframeTransport;
    };

}

function initAfter ()
    {
    var element = getElem('id', 'ccHolder', 0);
        
    element.value = '';
    }
