// add-collection-widget.js

const addWidget = function (e) {
    var list = jQuery(jQuery(this).attr('data-list'));

    console.log(list);
    var limit = list.attr('data-LimitUsersByOrg');
    // Try to find the counter of the list or use the length of the list
    var counter = list.data('widget-counter');
    if (list.children().length >= limit) {
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
    selector
        .find('option[value]')
        .filter(function(){return this.value})
        .remove();
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
        if( jQuery.trim(jQuery(this).val()) ){
            jQuery(this).removeClass('is-invalid');
            return false;
        }
        return true;
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
    if(jQuery('#members-fields-list').children().length){
        jQuery('#confirm-reg').modal('show');

    } else {
        jQuery('#no-users-reg').modal('show');
    }
    return empty_flds;
};

const validateCode = function (force=false) {
    let data = {
        code: jQuery('#validation-code').val()
    };

    if(data.code.length<3 && !force){
        return
    }

    jQuery.ajax({
        url: "/conference/registration-validate-code",
        data: data
    }).done(function (data) {
        if (data && data.found) {
            jQuery('#validation-code-error').hide();
            callback();
        } else {
            jQuery('#validation-code-error').show();
        }
    });
};

const validateEmail = function () {
    let t = this;

    let data = {
        email: jQuery(t)
            .parents('.conference-member')
            .find('.email')
            .val(),
        conf_id: jQuery('.conf_id')
            .val()
    };

    jQuery.ajax({
        url: "/conference/registration-validate-email",
        data: data
    }).done(function (data) {
        let eml = jQuery(t)
            .parents('.conference-member')
            .find('.email');
        let trg = eml.parent();
        if( data && data.found){
            if(!trg.find('.error').length) {
                trg.append(
                    jQuery('<span></span')
                        .addClass('error invalid-feedback d-block')
                );
                console.log(trg);
            }
            console.log(trg);
            let err = trg.find('.error');
            eml.addClass('is-invalid');
            err.html('<span class="form-error-message">Пользователь с такой почтой уже зарегистрирована</span>');
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
        console.log(data.found);    });
    return false;
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
                        .addClass('error invalid-feedback d-block')
                );
                console.log(trg);
            }
            console.log(trg);
            let err = trg.find('.error');
            jQuery('.inn').addClass('is-invalid');
            jQuery('.kpp').addClass('is-invalid');
            err.html('<span class="form-error-message">Организация \''+data.found+'\' уже зарегистрирована</span>');
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

    item.find('.phone').each(function () {
        new IMask(this, {
            mask: [
                {mask: '{8}(000)000-00-00'},
                {mask: '+{7}(000)000-00-00'},
            ]
        });
    });
    item.find('.representative').on('click', representative);
    console.log('item.find(\'.email\')', item.find('.email'));
    item.find('.email').on('change', validateEmail);
    item.find('.select-roomtype').children().each(function () {
        let v = jQuery(this).attr("value");
        let look = jQuery('#select-proto')
            .find('[value=' + v + ']');
        if (look.length>0) {
            jQuery(this).text(look.first().text());
        }
        else {
        //    console.log(jQuery(this).parent());
            jQuery(this).remove();
        }
    });

    item.find('.datetimepicker').datetimepicker({
        locale: 'ru',
        format: 'YYYY.MM.DD hh:mm',
        sideBySide: true,
    });

//    let jQuery('').children()

    // #select-proto
    // .select-roomtype
};

const representative = function (e) {
    //console.log(this,e,jQuery(this).prop('checked'));
    //jQuery(this).prop('checked', false);

    let t = this;
    if(!jQuery(t).prop('checked')){
        return;
    }

    let data = {
        email: jQuery(t).parents('.conference-member').find('.email').val()
    };

    jQuery.ajax({
        url: "/conference/registration-email-code",
        data: data
    }).done(function (data) {
        if (data && data.found) {
            modalValidate(function(e){
                // jQuery(t).parents('.representative-member').remove();
                jQuery(t).prop('checked', true);
                jQuery("#representative-modal").modal('hide');
                jQuery('input:checkbox.representative').not(t).prop('checked', false);
            });
        } else {
            jQuery('#representative-email').modal('show');
        }
    });
    return false;
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


    jQuery('#validation-code').on('change', validateCode);

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

    jQuery('#representative-modal').find('.btn-primary').on("click", function(){
        validateCode(true);
    });

    jQuery(window).bind('beforeunload', function(e){
        if(!blockUnload) return;
        e.returnValue = 'Если вы покинете это страницу, данные о регистрации будет потеряны?';
        return e.returnValue;
    });
    jQuery('input').on('change',function (e) {
        blockUnload = true;
    });
    jQuery('#conference_organization_form_save').on('click',function (e) {
        blockUnload = false;
    });


});
var blockUnload = false;

var modalConfirm = function(_callback){

    callback = _callback;
    // jQuery(btn_id).on("click", function(){
    jQuery("#confirm-modal").modal('show');
    // });

    // jQuery("#confirm-modal").modal('show');

};

var modalValidate = function(_callback){
    callback = _callback;
    jQuery("#representative-modal").modal('show');
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