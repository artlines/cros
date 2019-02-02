// add-collection-widget.js

const addWidget = function (e) {
    var list = jQuery(jQuery(this).attr('data-list'));

    console.log(list);
    var limit = list.attr('data-LimitUsersByOrg');
    // Try to find the counter of the list or use the length of the list
    var counter = list.data('widget-counter');
    if (list.children().length >= 10) {
        console.log('max children', list.children().length);
        jQuery("#limit-reg").modal('show');
        return;
    }
    // grab the prototype template
    var newWidget = list.attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, counter);
    // And store it, the length cannot be used if deleting widgets is allowed

    // create a new list element and add it to the list
    var newElem = jQuery(newWidget);

    newElem.attr('data-num', counter);

    // Increase the counter
    counter++;
    list.data('widget-counter', counter);

    // add remove button
    newElem.find('.remove-collection-widget').click(removeConferenceMember);

    newElem.find('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;
    updateItem(newElem);

    // append with animation
//    newElem.hide();
    newElem.appendTo(list);
//    newElem.show('blind', {}, 500);

    jQuery('html, body').animate({
        scrollTop: newElem.find('.lastName').offset().top-200
    }, 1000);
    newElem.find('.lastName').focus();

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

const validateRequired = function () {
    var empty_flds = 0;
    let r = jQuery("[required]").filter(function() {
        return !jQuery.trim(jQuery(this).val());
    }).each(function() {
        jQuery(this).addClass('is-invalid');
        jQuery('html, body').animate({
            scrollTop: jQuery(this).offset().top-200
        }, 1000);
        jQuery(this).focus();
        return false;
    }).length;
    if (r) {
        jQuery('#required-reg').modal('show');
        return false;
    }
    jQuery('#confirm-reg').modal('show');
    return empty_flds;
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
        let trg = jQuery('.inn').parent();
        if( data && data.found){
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
            jQuery('.inn').addClass('is-invalid');
            jQuery('.kpp').addClass('is-invalid');
            err.html('<span class="form-error-icon badge badge-danger text-uppercase">Ошибка</span> <span class="form-error-message">Организация \''+data.found+'\' уже зарегистрирована</span>');
            jQuery('html, body').animate({
                scrollTop: err.offset().top-400
            }, 1000);

            console.log(trg);
        } else {
            if(trg.find('.error')) {
                trg.find('.error').remove();
                jQuery('.inn').removeClass('is-invalid');
                jQuery('.kpp').removeClass('is-invalid');
            }
        }
        // $('body').scrollTo('#target');
        console.log(data.found);
    });

    validateRequired();
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
var callback = null;
const removeConferenceMember = function (e) {
    let t = this;
    modalConfirm(function(e){
        console.log('confirm1',e,t);
        jQuery(t).parents('.conference-member').remove();
    })
};
const updateItem = function (item) {
    item.find('.firstName').on('change', neighbourhoodRename);
    item.find('.middleName').on('change', neighbourhoodRename);
    item.find('.lastName').on('change', neighbourhoodRename);

    item.find('.phone').each( function () {
        new IMask( this, {
            mask: [
                { mask:  '{8}(000)000-00-00' },
                { mask: '+{7}(000)000-00-00' },
            ]
        });
    });

};
jQuery(document).ready(function () {

    jQuery('.add-another-collection-widget').click(addWidget)
    // .click()
    ;
    jQuery('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;

    jQuery('.remove-collection-widget').click(removeConferenceMember);


    // modalConfirm('.remove-collection-widget', function(confirm){
    //     if(confirm) {
    //         console.log('confirm', confirm);
    //     }else{
    //         console.log('no confirm', confirm);
    //     }
    // });
    //
    // modalConfirm2('.remove-collection-widget2', function(confirm){
    //     if(confirm) {
    //         console.log('confirm2', confirm);
    //     }else{
    //         console.log('no confirm2', confirm);
    //     }
    // });

    jQuery.each( jQuery('.conference-member'), function(i,el){
        updateItem(jQuery(el));
    });

    jQuery('.select-neighbourhood').each(neighbourhood);

    jQuery('.inn').on('change', validateInnKpp);
    jQuery('.kpp').on('change', validateInnKpp);


    jQuery('.validateRequired').on('click', validateRequired);


    jQuery("#confirm-modal-btn-yes").on("click", function(e){
        if(callback) {
            callback(e);
            callback = null;
        }
        jQuery("#confirm-modal").modal('hide');
    });

    jQuery("#confirm-modal-btn-no").on("click", function(){
        callback = null;
        jQuery("#confirm-modal").modal('hide');
    });

});


var modalConfirm = function(_callback){

    callback = _callback;
    // jQuery(btn_id).on("click", function(){
        jQuery("#confirm-modal").modal('show');
    // });

    // jQuery("#confirm-modal").modal('show');

};




var modalConfirm2 = function(btn_id,callback){

    jQuery("#confirm-modal").modal('show');
};


// modalConfirm(function(confirm){
//     if(confirm){
//         //Acciones si el usuario confirma
//         $("#result").html("CONFIRMADO");
//     }else{
//         //Acciones si el usuario no confirma
//         $("#result").html("NO CONFIRMADO");
//     }
// });