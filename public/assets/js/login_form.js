$('.show_pass').on('click', function(){
    if($(this).attr('data-visible') == 'true'){
        $(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').attr('data-visible', 'false');
        $('#password').attr('type', 'password');
    }
    else{
        $(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').attr('data-visible', 'true');
        $('#password').attr('type', 'text');
    }
});