function applyCreditForm(url, id) {
    credit_value = $(id).value;
    var data = {};
    var allShipping = document.getElementsByName("order[shipping_method]");
    for (index = 0; index < allShipping.length; ++index) {
        var shippingElement = allShipping[index];
        if (shippingElement.checked) {
            data['order[shipping_method]'] = shippingElement.value;
        }
    }
    new Ajax.Request(url, {
        method: 'post',
        parameters: {credit_value: credit_value},
        onException: '',
        onComplete: function(response) {
            if (response.responseText.isJSON()) {
                if (order) {
                    var res = response.responseText.evalJSON();
                    order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, data);
                    $('customercredit_balance').update("" + res.balance);
                }
            }
        }
    });
}
