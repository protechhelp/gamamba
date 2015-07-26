var toplinkwish, toplinkcart, minicart, miniwish, compare, elem;

function preventClickDf(e){
	e = e||event;
	if(e.preventDefault){
		e.preventDefault();		//for firefox
	}
	else{
		e.returnValue = false;		//for IE , prevent default
	}				
}

function initBlock(){
	toplinkwish=($$('.top-link-wishlist')!='')?'.top-link-wishlist':'';
	toplinkcart=($$('.top-link-cart')!='')?'.top-link-cart':'';
	sidebarcart = '.mini-cart, .block-cart';
	minicartpro= '.mini-cartpro';
	miniwish=($$('.mini-wishlist')!='')?'.mini-wishlist':'.block-wishlist';
	compare=($$('.mini-compare-products')!='')?'.mini-compare-products':'.block-compare';
}

function getToplinkwish(){
	var la=$$('ul.links li a');
	var lw = []; 
	for(var i=0;i<la.length;i++){
		if(la[i].href.search('/wishlist/')!=-1)
			lw[lw.length] = la[i];
	}
	return lw;
}
function initcajax(){

	if(enable_ajax_cart){
		if(typeof productAddToCartForm != 'undefined'){		//do check form productAddtoCartForm exist
			productAddToCartForm.submit = function(args){
				if(this.validator && this.validator.validate()){
					url=$('product_addtocart_form').action;	//url cua form trong category view
					ajaxUpdate(url,'form');
				}
				return false;
			};		
		}
		updateDeleteLinks();
	}
	if(enable_ajax_compare){ 
		//updateProductCompareLinks();
		setInterval("updateProductCompareLinks()",100);
		updateRemovePCompareLinks();
		updateClearPCompareLinks();
	}
	if(enable_ajax_wishlist){
		updateRemoveWishLinks();
		if(islogin=="1"){
			setInterval("updateWishlistLinks()",100);}
			//updateWishlistLinks(); }
		updateWishlistAddCartLinks();
	}
	initBlock();
}

window.setPLocation= function(args){		// override setPlocation injected in inline script in btn addtocart, ...
		if (enable_ajax_cart && (args.search('checkout/cart/add') != -1 || args.search('options=cart') != -1 ) ){
			if(setfocus){				// for win parent  , detect by var setfocus
				opener.isCompare=1;
				opener.focus();
            	opener.ajaxUpdate(args,'url');
			}
			else
				ajaxUpdate(args,'url');		//for win popup
		}
		else if(enable_ajax_wishlist && args.search('wishlist/index/add')!=-1 ){
			if(opener.islogin!='0'){
				if(setfocus){
					opener.isCompare=1;
					opener.focus();
					opener.ajaxUpdate(args,'url');
				}
				else
					ajaxUpdate(args,'url');			
			}
			else{
				opener.focus();
				opener.location.href=args;
			}
		}
        else{
			opener.focus();
            opener.location.href = args;
        }		
	};
	
var win="";

window.popWin = function(url,newwin,para){
	win = window.open(url,newwin,para);
	win.focus();
};

window.onunload = function(){		// close win compare when win parent redirect or refresh
	if(win){
		win.close();
	}
};	

var deletePCompare=0;

window.setLocation = function(args){	// override setLocation injected in inline script in btn addtocart, ...

		//console.log(arguments.callee.caller.name);
		
		if(args.search('checkout/onepage')!=-1){	//if click btn checkout in sidebar-minicart
				window.location=args;
				return;
		}
		if(args.search('catalog/category')!=-1){	//if click btn selectbox to sort by in category
				window.location=args;
				return;
		}

		if ( enable_ajax_cart && (args.search('checkout/cart/add') != -1 || args.search('options=cart') != -1 || args.search('wishlist/index/cart')!=-1 || args.search('wishlist/index/cart')!=-1)){
            ajaxUpdate(args,'url');
		}
		else if (enable_ajax_compare && args.search('catalog/product_compare/remove') != -1 ){
				opener.deletePCompare=1;
				if(setfocus){				//for win parent  ,flag to detect is var setfocus
					
					opener.isCompare=1;
					opener.deletePCompare=1;

					opener.focus();
					opener.ajaxUpdate(args,'url');
					exit;
				}
				else
					ajaxUpdate(args,'url');		//for win popup
		}
        else
        {
            window.location.href = args;
        }
		
};

function addLayer(){
	str='<div id="blurmask"></div>';
	return str;
}

var hideConfirm = 0;	

function  assignAjaxUpdatetoLink(link, _hideConfirm, _msgAbort, _skipCond){
	var tmpLinks = document.links;
	for (var i=0; i<tmpLinks.length; i++){
		if (tmpLinks[i].href.search(link) != -1  ){
				if(typeof _skipCond !="undefined" && _skipCond !="" && tmpLinks[i].href.search(_skipCond) != -1) { continue; }
				tmpLinks[i].onclick = function(e){
					
					preventClickDf(e);
					
					if(typeof _hideConfirm =="undefined"){
						ajaxUpdate(this.href,'url');
					}
					else{
						if(confirm(_msgAbort)){
							hideConfirm = 1;
							ajaxUpdate(this.href,'url');
						}						
					}	
				};
		}
	}	
}

function  updateDeleteLinks(){	
	if(_skipProductlink){
		assignAjaxUpdatetoLink('checkout/cart/delete',true,'Are you sure you would like to remove this item from the shopping cart?',_skipProductlink);
	}
	assignAjaxUpdatetoLink('checkout/cart/delete',true,'Are you sure you would like to remove this item from the shopping cart?',_skipProductlink);
}

function  updateRemovePCompareLinks(){	
	assignAjaxUpdatetoLink('catalog/product_compare/remove',true,'Are you sure you would like to remove this item from the comparison list?');
}
function  updateClearPCompareLinks(){	
	assignAjaxUpdatetoLink('catalog/product_compare/clear',true,'Are you sure you would like to remove all products from your comparison?');	
}
function  updateProductCompareLinks(){	
	assignAjaxUpdatetoLink('catalog/product_compare/add');	
}

function  updateRemoveWishLinks(){	
	assignAjaxUpdatetoLink('wishlist/index/remove',true,'Are you sure you would like to remove this item from the your wishlist?');	
}
function  updateWishlistLinks(){	
	assignAjaxUpdatetoLink('wishlist/index/add');	
}
function  updateWishlistAddCartLinks(){	
	assignAjaxUpdatetoLink('wishlist/index/cart');		
}
 
function fixcenter(){
	var theWidth, theHeight;
	// Window dimensions:
	if (window.innerWidth) {
		theWidth=window.innerWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth) {
		theWidth=document.documentElement.clientWidth;
	}
	else if (document.body) {
		theWidth=document.body.clientWidth;
	}
	if (window.innerHeight) {
		theHeight=window.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight) {
		theHeight=document.documentElement.clientHeight ;
	}
	else if (document.body) {
		theHeight=document.body.clientHeight;
	}
	var midheight=(parseInt(theHeight/2)-parseInt($('zoptions').getHeight()/2))+'px';
	$('options-tab').setStyle({top:midheight});
}
function addOptionscart(){
	var midwidth=0;
	str='		<div id="options">';
	str=str+'		<DIV id="options-tab" style="top:'+midwidth+'px;">';
	str=str+'			<!--block content-->';
	str=str+'			<DIV id="zoptions">';
	str=str+'					<div id="process" style="display:block;"></div>							';
	str=str+'					<div id="fancybox-wrap-clone" style="display:none;"><div id="fancybox-outer"><div id="fancy-bg-n" class="fancy-bg"></div><div id="fancy-bg-ne" class="fancy-bg"></div><div id="fancy-bg-e" class="fancy-bg"></div><div id="fancy-bg-se" class="fancy-bg"></div><div id="fancy-bg-s" class="fancy-bg"></div><div id="fancy-bg-sw" class="fancy-bg"></div><div id="fancy-bg-w" class="fancy-bg"></div><div id="fancy-bg-nw" class="fancy-bg"></div><div id="confirmbox"></div>';
	str=str+'					<a id="fancybox-close" style="display: inline;" onclick="$(\'confirmbox\').innerHTML=\'\';$(\'fancybox-wrap-clone\').setStyle({display:\'none\'});$(\'options\').setStyle({display:\'none\'});"></a></div></div>';
	str=str+'			</DIV>';
	str=str+'		</DIV>	';
	str=str+'	</div>';
	//str=str+'<div id="fixbox" style="Z-INDEX: 10002; POSITION: fixed;height:100px;width:100px;DISPLAY: block; TOP: 158px; PADDING-TOP: 0px; LEFT: 584px">This box has its position fixed applied to it. For IE6, it is set to absolute positioning using the * html hack and works just like fixed positioning.The "View the CSS" and "learn about using it" links to the right are also absolutely positioned, but they dont behave like a fixed element in IE6.</div>';
	return str;

}
function fixURLProducttypes(){
	$$(classBtnAddtocart).each(function(el){
		link = String(el.readAttribute('onclick'));
		if(link.search('checkout/cart/add') != -1 || link.search('options=cart') != -1){

		}else{
			if( link.search("setLocation") != -1 ){
				link = link.replace("')","?options=cart')");
				el.writeAttribute('onclick', link);
			}
		}
	});
}

function initfunc(){
	fixURLProducttypes();
	initcajax();
	//myElement="sometext";myElement.appendToTop('body');
		var s = addOptionscart()+addLayer();
		var f = $$('body')[0].insert({top:s});
	callinit();		//callback init to assign event onclick again for links( addtocompare,addtowishlist ), because sometime grid container product be changed or reload, eg: ext ajaxhometab renew grid product by ajax each click other tab 
}
function callinit(){
	initcajax();
}

//window.onload=initfunc;
(function() {	// dom ready
	  if (document.loaded) {	//case dom loaded before have this script.
			initfunc();
	  } else {
			document.observe('dom:loaded', initfunc );
	  }
	})();	// same jquery $(function(){ }) 
