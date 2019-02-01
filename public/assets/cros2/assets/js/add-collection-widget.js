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
    newElem.find('.conference-member').attr('data-num',counter);
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
    newElem.find('.firstName').on('change', neighbourhoodRename);
    newElem.find('.middleName').on('change', neighbourhoodRename);
    newElem.find('.lastName').on('change', neighbourhoodRename);

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
        // look options exist
        let options = selector.find('option[value='+i+']');
        let self_num = selector.parents('.conference-member')
            .attr('data-num');
        if( !options.length && Number(self_num) !== Number(i))
        {
            selector.append(
                jQuery("<option></option>")
                    .attr("value", i)
                    .text(fio)
            );
        }else{
            console.log(options);
        }
    });


    // newOptions.map( (key,value) => {
    //     selector.append($("<option></option>")
    //     .attr("value", value).text(key));
    // })
};

const validateInnKpp = function () {
    let data = {};
    data.inn = jQuery('.inn').val();
    data.kpp = jQuery('.kpp').val();
    data.conf_id= jQuery('.conf_id').val();
    console.log(data);
    jQuery.ajax({
        url: "/conference/registration-validate",
        data: data
    }).done(function(data) {
        if( data && data.found){
            let trg = jQuery('.inn').parent();
            if(!trg.find('.error').length) {
                trg.append(
                    jQuery('<span></span')
                        .html('DDD')
                        .addClass('error invalid-feedback d-block')
                );
                console.log(trg);
            }
            console.log(trg);
            let err = trg.find('.error');
            err.html('<span class="form-error-icon badge badge-danger text-uppercase">Ошибка</span> <span class="form-error-message">Организация \''+data.found+'\' уже зарегистрирована</span>');
            console.log(trg);
        }
        console.log(data.found);
//        jQuery( this ).addClass( "done" );
    });


    $.ajax();
};

const neighbourhoodRename =  function() {
    console.log('neighbourhoodRename', jQuery(this).val());

    let block = jQuery(this).parents('.conference-member');

    let num  = block.attr('data-num');

    let options = jQuery('.conference-member .select-neighbourhood option[value='+num+']');
    console.log(
        'neighbourhoodRename',
        '.conference-member .select-neighbourhood option[value='+num+']',
        options
    );

    var fio = [
        jQuery(block).find('.lastName').val(),
        jQuery(block).find('.firstName').val(),
        jQuery(block).find('.middleName').val()
    ].join(' ').trim();
    if(fio === "") {
        fio = 'Участник ' + (1+i);
    }

    jQuery.each(options,function (i,el) {
       console.log('option',el);
        jQuery(el)  .text(fio);
    });


    // jQuery.each( jQuery('.conference-member'), function(i,el){
    //
    //     selector.append(
    //         jQuery("<option></option>")
    //             .attr("value", 'value')
    //             .text(fio)
    //     );
    // });
    //
    // ;
};


jQuery(document).ready(function () {

    jQuery('.add-another-collection-widget').click(addWidget)
    // .click()
    ;
    jQuery('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;

    jQuery('.remove-collection-widget').click(function (e) {
        jQuery(this).parent().parent().parent().remove();
    });


    jQuery('.select-neighbourhood').each(neighbourhood);

    jQuery('.firstName').on('change', neighbourhoodRename);
    jQuery('.middleName').on('change', neighbourhoodRename);
    jQuery('.lastName').on('change', neighbourhoodRename);

    jQuery('.inn').on('change', validateInnKpp);
    jQuery('.kpp').on('change', validateInnKpp);

});