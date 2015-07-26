if(!window.SeoSuite)
    var SeoSuite=new Object();
SeoSuite.Methods={    
    enteredLabel:'You’ve entered <span>0</span> character(s).',
    titleSearchUse:'Most search engines use up to 70.',
    descriptionSearchUse:'Most search engines use up to 150.',    
        
    init:function(options){
        Object.extend(this,options||{});
    },
    
updateForm:function(){ 
    if ($('meta_title')) {
        this.addTitleLabel($('meta_title'));
        $('meta_title').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('meta_title').writeAttribute("onchange","SeoSuite.updateLabel(this)");
    }
    
    if ($('group_7meta_title')) {
        this.addTitleLabel($('group_7meta_title'));
        $('group_7meta_title').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('group_7meta_title').writeAttribute("onchange","SeoSuite.updateLabel(this)");
    }
    
    
    if ($('page_meta_title')) {
        this.addTitleLabel($('page_meta_title'));
        $('page_meta_title').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('page_meta_title').writeAttribute("onchange","SeoSuite.updateLabel(this)");                
    }
    
    if ($('meta_description')) {
        this.addDescriptionLabel($('meta_description'));
        $('meta_description').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('meta_description').writeAttribute("onchange","SeoSuite.updateLabel(this)");                
    }
    
    if ($('group_7meta_description')) {
        this.addDescriptionLabel($('group_7meta_description'));
        $('group_7meta_description').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('group_7meta_description').writeAttribute("onchange","SeoSuite.updateLabel(this)");                
    }
    
    if ($('page_meta_description')) {
        this.addDescriptionLabel($('page_meta_description'));
        $('page_meta_description').writeAttribute("onkeyup","SeoSuite.updateLabel(this)");
        $('page_meta_description').writeAttribute("onchange","SeoSuite.updateLabel(this)");                
    }
    
    setTimeout("SeoSuite.updateForm()", 1000);
},


addTitleLabel:function(el) {    
    try{
        if(el){                              
            entered=$(el).up('td').down('.entered');
            if (!entered) {                
                el.insert({after: '<p class="note entered">'+this.enteredLabel+' '+this.titleSearchUse+'</p>'});
                this.updateLabel(el);
            }
        }
    }catch(e){}
},

addDescriptionLabel:function(el) {    
    try{
        if(el){
            entered=$(el).up('td').down('.entered');
            if (!entered) {                
                el.insert({after: '<p class="note entered">'+this.enteredLabel+' '+this.descriptionSearchUse+'</p><p class="note">Leave blank to use Product Meta Description Template</p>'});
                this.updateLabel(el);
            }    
        }
    }catch(e){}
},


updateLabel:function(el) {    
    try{
        if(el){
            strlength = el.value.length;    
            row=$(el).up('td').down('.entered').down('span');            
            row.innerHTML = strlength;
        }
    }catch(e){}
}

};

Object.extend(SeoSuite,SeoSuite.Methods);
