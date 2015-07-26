function init()
{
    // The method “getElem” can be used to get an item on the page by its id
    var hiddenElements = ['contactBlock','userInfoBlock','addressBlock',
        'notMandatoryRow', 'spacer1', 'spacer2', 'spacer3', 'spacer4'];
    for (var key in hiddenElements){
        var element = getElem('id', hiddenElements[key], 0);
        if (element){
            element.style.display="none";
        }
    }
}

