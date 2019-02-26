// add-collection-widget.js

const addWidget = function (e) {
    var list = jQuery(jQuery(this).attr('data-list'));

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

    updateItem(newElem);

    // append with animation
//    newElem.hide();
    newElem.appendTo(list);
//    newElem.show('blind', {}, 500);
    if (counter>1) {
        jQuery('html, body').animate({
            scrollTop: newElem.find('.lastName').offset().top - 200
        }, 1000);
        newElem.find('.lastName').focus();
    }

    // show end registration button
    jQuery('#end-red').show();
    jQuery('.select-neighbourhood').each(neighbourhood);

    updateItemTitles();

};

const changeRoomType = function() {
    //alert(  );
    var id = this.value;
    var roomTypeHtml = jQuery('#roomType_'+ id).html();
    var roomTypeInfo = jQuery(this).parents('.conference-member').find('.roomTypeInfo');
    roomTypeInfo.html(roomTypeHtml);
    var nh = jQuery(this).parents('.conference-member').find('.select-neighbourhood').parents('.form-group');
    if (jQuery('#roomType_'+ id).data('rooms')>1 ){
        nh.show();
    } else {
        // reset selected neighbourhood
        jQuery(this).parents('.conference-member').find('.select-neighbourhood').val('');
        nh.hide();
    }
};

const neighbourhood = function() {
    let selector = jQuery(this);
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
        }
    });

};
function fio(cm){
    return [
        jQuery(cm).find('.lastName').val(),
        jQuery(cm).find('.firstName').val(),
        jQuery(cm).find('.middleName').val()
    ].join(' ').trim();

}
const  validateNeighbourhoodRoomType = function(){

    var result = jQuery('.conference-member')
        .filter(function(){return jQuery(this).find('.select-neighbourhood').val()!==''})
        .filter(function(e){
            var n_num = jQuery(this).find('.select-neighbourhood').val();
            var n_roomType = jQuery('[data-num='+n_num+']')
                .find('.select-roomtype').val();
            var m_roomType = jQuery(this)
                .find('.select-roomtype').val();
//                return 'Класс участия для совместного проживания отличается'
            return ( n_roomType != m_roomType);
        })
        .map(function(e){
            var n_num = jQuery(this).find('.select-neighbourhood').val();
            var n = jQuery('[data-num='+n_num+']');
            return ' - '+fio(this)+' и '+fio(n);
        })
        .get()
        ;
    return (result.length>0)
        ? result
        : false;
};

// за комнату
// за одно место в двухместном номере
//
// прятать neighbourhood()

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
    if(jQuery('#members-fields-list').children().length === 0){
        jQuery('#no-users-reg').modal('show');
    } else if (jQuery('.representative:checked').length === 0) {
        jQuery('#no-representative').modal('show');
        jQuery('html, body').animate({
            scrollTop: jQuery('.representative').offset().top-400
        }, 1000);
    } else {
        // check user type with neighbourhood
        var errNH;
        if( errNH = validateNeighbourhoodRoomType()) {
            jQuery('#confirm-reg-warning')
                .show()
                .html(
                    'Вы выбрали совместное проживание для:<br />' +
                    errNH.join('<br />') + '<br />' +

                    'Но их классы участия различаются. <br />' +
                    'Если вы действительно хотите поселить данные участников вместе, просьба указать идентичные классы участия.'
                )
            ;
        } else {
            jQuery('#confirm-reg-warning').hide();
        }
        jQuery('#confirm-reg').modal('show');
    }
    return empty_flds;
};

const validateCode = function (force) {
    force = typeof force !== 'undefined' ?  force : false;
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
                    jQuery('<span></span>')
                        .addClass('error invalid-feedback d-block')
                );
            }
            let err = trg.find('.error');
            eml.addClass('is-invalid');
            if (data.found == 'email-invalid') {
                err.html('<span class="form-error-message">Ошибка формата почты</span>');
            }else {
                err.html('<span class="form-error-message">Пользователь с такой почтой уже зарегистрирован</span>');
            }
            jQuery('html, body').animate({
                scrollTop: err.offset().top-400
            }, 1000);

        } else {
            eml.removeClass('is-invalid');
            if(trg.find('.error')) {
                trg.find('.error').remove();
                jQuery('.inn').removeClass('is-invalid');
                jQuery('.kpp').removeClass('is-invalid');
            }
        }
        // $('body').scrollTo('#target');
//        console.log(data.found);
    });
    return false;
};

const validateInnKpp = function () {
    let data = {};
    data.inn = jQuery('.inn').val();
    data.kpp = jQuery('.kpp').val();
    data.conf_id= jQuery('.conf_id').val();

    jQuery.ajax({
        url: "/conference/registration-validate",
        data: data
    }).done(function(data) {
        let trg = jQuery('.inn').parent();
        if( data && data.found){
            if(!trg.find('.error').length) {
                trg.append(
                    jQuery('<span></span>')
                        .addClass('error invalid-feedback d-block')
                );
            }
            let err = trg.find('.error');
            jQuery('.inn').addClass('is-invalid');
            jQuery('.kpp').addClass('is-invalid');
            err.html('<span class="form-error-message">Организация \''+data.found+'\' уже зарегистрирована</span>');
            jQuery('html, body').animate({
                scrollTop: err.offset().top-400
            }, 1000);

        } else {
            if(trg.find('.error')) {
                trg.find('.error').remove();
                jQuery('.inn').removeClass('is-invalid');
                jQuery('.kpp').removeClass('is-invalid');
            }
        }
    });
};

const neighbourhoodRename =  function() {
    let block = jQuery(this).parents('.conference-member');

    let num  = block.attr('data-num');

    let options = jQuery('.conference-member .select-neighbourhood option[value='+num+']');

    var fio = [
        jQuery(block).find('.lastName').val(),
        jQuery(block).find('.firstName').val(),
        jQuery(block).find('.middleName').val()
    ].join(' ').trim();
    if(fio === "") {
        fio = 'Участник ' + (1+i);
    }

    jQuery.each(options,function (i,el) {
        jQuery(el)  .text(fio);
    });
};

var callback = null;
const removeConferenceMember = function (e) {
    let t = this;
    modalConfirm(function(e){
        jQuery(t).parents('.conference-member').remove();
        updateItemTitles();
    });
};

const updateItemTitles = function (){
    jQuery('.conference-member .title').each(function (i,n) {
        jQuery(this).html('Участник '+(1+Number(i)));
    });

};

const updateItem = function (item) {
    item.find('.firstName').on('change', neighbourhoodRename);
    item.find('.middleName').on('change', neighbourhoodRename);
    item.find('.lastName').on('change', neighbourhoodRename);
    updateItemTitles();
    item.find('.phone').each(function () {
        new IMask(this, {
            mask: [
                {mask: '{8}(000)000-00-00'},
                {mask: '+{7}(000)000-00-00'},
            ]
        });
    });

    item.find('.representative').on('click', representative);

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
        format: 'DD.MM.YYYY HH:mm',
        sideBySide: true,
    });

    // add remove button
    item.find('.remove-collection-widget').click(removeConferenceMember);

    item.find('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;
    item.find('input[type="file"]').on( 'change', function(e){
        var fileName = e.target.files[0]. name;
        var id = jQuery(this).attr('id');
        jQuery(this).parent().find('label[for='+id+']').text(fileName).attr('style','overflow: hidden');
        //alert('The file "' + fileName + '" has been selected.' );
    });

};

const representative = function (e) {

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
//                jQuery(t).parents('.conference-member').find('.email').attr('readonly',true);
                jQuery("#representative-modal").modal('hide');
                jQuery('input:checkbox.representative').not(t).prop('checked', false);
            });
        } else {
            jQuery('#representative-email').modal('show');
        }
    });
    return false;
};
const fixErrorLabels = function () {
   jQuery('.invalid-feedback').each(function () {
       jQuery(this).parent().parent().find('input').first().after(jQuery(this));
       jQuery(this).parent().parent().find('select').first().after(jQuery(this));
   });
};
jQuery(document).ready(function () {

    jQuery('.add-another-collection-widget')
        .click(addWidget)
    ;

    if( jQuery('.conference-member').length == 0 ){
        // Если нет участников добавлять одного
        jQuery('.add-another-collection-widget')
            .click();
    }

    jQuery('.select-roomtype')
        .on('change', changeRoomType)
        .change()
    ;

    jQuery('.remove-collection-widget').click(removeConferenceMember);


    jQuery.each( jQuery('.conference-member'), function(i,el){
        updateItem(jQuery(el));
    });

    jQuery('.select-neighbourhood').each(neighbourhood);

    jQuery('.inn')
        .on('change', validateInnKpp)
        .each(function () {
            new IMask(this, {
                mask: /^\d+$/
            });
        });
    jQuery('.kpp')
        .on('change', validateInnKpp)
        .each(function () {
            new IMask(this, {
                mask: /^\d+$/
            });
        });



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
    jQuery('textarea').on('change',function (e) {
        blockUnload = true;
    });
    jQuery('select').on('change',function (e) {
        blockUnload = true;
    });
    jQuery('#conference_organization_form_save').on('click',function (e) {
        blockUnload = false;
    });

    if (!jQuery('.ConferenceOrganization').val()) {
//        jQuery('.noedit').attr('disabled',false);
        jQuery('.noedit').removeClass('noedit');
    } else {
        jQuery('.noedit').filter(function () {
            jQuery(this)
                .removeClass('noedit')
                .attr('disabled',true);
            return !this.value;
        }).attr('disabled',false);
    }
    jQuery('input:disabled').each(function(e){
        var el = jQuery('<input type="hidden" name="" value="" />');
        el.attr('name', jQuery(this).attr('name'));
        el.attr('value', jQuery(this).attr('value'));
        el.appendTo( jQuery(this).parent() );
    });


    jQuery('input[type="file"]').on( 'change', function(e){
        var fileName = e.target.files[0]. name;
        var id = jQuery(this).attr('id');
        jQuery(this).parent().find('label[for='+id+']').text(fileName).attr('style','overflow: hidden');
        //alert('The file "' + fileName + '" has been selected.' );
    });
    updateItemTitles();
    fixErrorLabels();
});

var blockUnload = false;

var modalConfirm = function(_callback){

    callback = _callback;
    jQuery("#confirm-modal").modal('show');

};

var modalValidate = function(_callback){
    callback = _callback;
    jQuery("#representative-modal").modal('show');
    jQuery("#validation-code").val('');
    jQuery('#validation-code-error').hide();
};


