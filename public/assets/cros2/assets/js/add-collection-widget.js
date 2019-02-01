// add-collection-widget.js

const addWidget = function (e) {
    var list = jQuery(jQuery(this).attr('data-list'));
    var limit = list.attr('data-LimitUsersByOrg');
    // Try to find the counter of the list or use the length of the list
    var counter = list.data('widget-counter') | list.children().length;
    if( list.children().length>=limit){
        console.log(list.children());
        return;
    }
    // grab the prototype template
    var newWidget = list.attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, counter);
    // And store it, the length cannot be used if deleting widgets is allowed
    list.data('widget-counter', counter);

    // create a new list element and add it to the list
    var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
    newElem.attr('data-num',counter);
    console.log(list.attr('data-widget-tags'),jQuery(list.attr('data-widget-tags')),newElem,list);

    // Increase the counter
    counter++;

    // add remove button
    newElem.find('.remove-collection-widget').click(function (e) {
        jQuery(this).parent().parent().parent().parent().remove();
    });

    newElem.find('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;

    // append with animation
    newElem.hide();
    newElem.appendTo(list);
    newElem.show('blind',{},500);
    // show end registration button
    jQuery('#end-red').show();
    jQuery('.select-neighbourhood').each(neighbourhood);
};

const changeRoomType = function() {
    //alert(  );
    let id = this.value;
    let roomTypeHtml = jQuery('#roomType_'+ id).html();
    console.log('#roomType_'+ id,roomTypeHtml);
    let roomTypeInfo = jQuery(this).parents('.conference-member').find('.roomTypeInfo');
    console.log(jQuery(this).parents('.conference-member'));
    roomTypeInfo.html(roomTypeHtml);

};

const neighbourhood = function() {
    let selector = jQuery(this);
//    selector.empty();
//    console.log( jQuery('.conference-member'));
    jQuery.each( jQuery('.conference-member'), function(i,el){
        var fio = [
            jQuery(el).find('.lastName').val(),
            jQuery(el).find('.firstName').val(),
            jQuery(el).find('.middleName').val()
        ].join(' ').trim();
        if(fio === "") {
            fio = 'Участник ' + (1+i);
        }
//        console.log(fio);
        selector.append(
            jQuery("<option></option>")
                .attr("value", 'value')
                .text(fio)
        );
    });

    // newOptions.map( (key,value) => {
    //     selector.append($("<option></option>")
    //     .attr("value", value).text(key));
    // })
};

jQuery(document).ready(function () {

    jQuery('.add-another-collection-widget').click(addWidget)
    // .click()
    ;
    jQuery('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;
    jQuery('.select-neighbourhood').each(neighbourhood);


});