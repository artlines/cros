$(function($)
{
    !$.easing && ($.easing = {});
    !$.easing.easeOutQuad && ($.easing.easeOutQuad = function(p){ return 1 - Math.pow( 1 - p, 2); });

    var     circle_length = $('.circle').length,
        dg = 360 / circle_length,
        circle_i = 0,
        circle_center = circle_length / 2;
    $('.circle').each(function(){
        if(circle_i == 0){
            $(this).children('img').addClass('menu_bottom');
        }
        else if(circle_i < circle_center){
            $(this).children('img').addClass('menu_right');
        }
        else if(circle_i == circle_center){
            $(this).children('img').addClass('menu_top');
        }
        else{
            $(this).children('img').addClass('menu_left');
        }
        $(this).attr('data-angle', circle_i * dg);
        circle_i++;
    });

    var circleController = {
        create: function (circle) {
            var obj = {
                angle: circle.data('angle'),
                element: circle,
                measure: $('<div />').css('width', 360 * 8 + parseFloat(circle.data('angle'))),
                update: circleController.update,
                reposition: circleController.reposition,
            };
            obj.reposition();
            return obj;
        },
        update: function (angle) {
            this.angle = angle;
            this.reposition();
        },
        reposition: function () {
            var radians = this.angle * Math.PI / 180, radius = 380 / 2;
            this.element.css({
                marginLeft: (Math.sin(radians) * radius - 50) + 'px',
                marginTop: (Math.cos(radians) * radius - 50) + 'px'
            });
        }
    }

    var spin = {
        circles: [],
        prep: function (circles) {
            for(var i = 0, circle; i < circles.length; i++){
                this.circles.push(circleController.create($(circles[i])));
            }
        }
    };


    spin.prep($('.circle'));
});