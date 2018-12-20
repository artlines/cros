/* Tabs init B4Tabs Bootstrap 4 */
;(function ($) {
  'use strict';
  $.MainCore.components.B4Tabs = {
    
    _baseConfig: {/*none*/},
    
    pageCollection: $(),
    
    init: function (selector, config) {
      this.collection = selector && $(selector).length ? $(selector) : $();
      if (!$(selector).length) return;
      this.config = config && $.isPlainObject(config) ?
        $.extend({}, this._baseConfig, config) : this._baseConfig;
      this.config.itemSelector = selector;
      this.initTabs();
      return this.pageCollection;
    },
    initTabs: function () {
     
      var $self = this,
        collection = $self.pageCollection;
      
      this.collection.each(function (i, el) {
        //hardcode - Little fix for bootstrap 4 Alexey sorry:) it`s my fault
        var item = $('[role="tablist"]').find('.active');
        
           if (item.length == 1 && item.hasClass('active') && $(window).width() > 767) {
              $('a[href="#nav-7-1-default-hor-left--1"]').trigger('click');
              item.parent('li').addClass('active');
             item.css("cssText", "background: #5c97bf;");
           }
           $('[role="tablist"]').click(function(){item.removeAttr('style');
           }); 
        
        var WindowWeight = $(window).width(),
          
          $tabs = $(el),
          $tabsItem = $tabs.find('.nav-item'),
          tabsView = $tabs.data('tabs-mobile-type'), 
          controlClasses = $tabs.data('btn-classes'),
          context = $tabs.parent(),
          
          $tabsContent = $('#' + $tabs.data('target')),
          $tabsContentItem = $tabsContent.find('.tab-pane');
        if (WindowWeight < 767) {
          $('body').on('click', function () {
            if (tabsView) {
              $tabs.slideUp(200);
            } else {
              $tabs.find('.nav-inner').slideUp(200);
            }
          });
        } else {
          $('body').off('click');
        }
        if (WindowWeight > 767 && tabsView) {
          $tabs.removeAttr('style');
          $tabsContentItem.removeAttr('style');
          context.off('click', '.mobile-type-tabs');
          context.off('click', '[role="tab"]');
          if (tabsView == 'accordion') {
            $tabsContent.find('.mobile-type-tabs').remove();
          } else {
            context.find('.mobile-type-tabs').remove();
          }
          return;
        }
        if (WindowWeight < 768 && tabsView == 'accordion') {
          $self.accordionEffect($tabsContent, $tabsItem, $tabsContentItem, controlClasses);
        } else if (WindowWeight < 768 && tabsView == 'slide-up-down') {
          $self.UpDownSlideEffect(context, $tabs, controlClasses);
        }
        
        collection = collection.add($tabs);
      });
    },
    UpDownSlideEffect: function (context, menu, Button_Class) {
      if (context.find('.mobile-type-tabs').length) return;
      
      var HTMLactiveItem = menu.find('.active').html();
      //Make controls btn via a 
      $(menu).before('<a class="mobile-type-tabs ' + Button_Class + '" href="#">' + HTMLactiveItem + '</a>');
      
      context.on('click', '.mobile-type-tabs', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(menu).slideToggle(200);
      });
      context.on('click', '[role="tab"]', function (e) {
        e.preventDefault();
        var thisHTML = $(this).html(),
          $targetControlTabs = $(this).closest('ul').prev('.mobile-type-tabs');
        $targetControlTabs.html(thisHTML);
        $(menu).slideUp(200);
      });
    },
    accordionEffect: function (context, menuItem, menu, Button_Class) {
      if (context.find('.mobile-type-tabs').length) return;
      
      $(menu).before('<a class="mobile-type-tabs ' + Button_Class + '" href="#"></a>');
      menuItem.each(function () {
        var thisIndex = $(this).index(),
          thisHTML = $(this).find('[role="tab"]').html();
        if ($(this).find('[role="tab"]').hasClass('active')) {
          $(menu[thisIndex]).prev().addClass('active');
     
        }
        $(menu[thisIndex]).prev().html(thisHTML);
      });
      
      context.on('click', '.mobile-type-tabs', function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) return;
        var contextID = context.attr('id');
        context.find('.mobile-type-tabs').removeClass('active');
        console.log('THIS'+contextID);
        $('[data-target="' + contextID + '"]').find('.nav-link').removeClass('active');
        var $target = $(this).next(),
          targetID = $target.attr('id');
        if ($target.hasClass('fade')) {
          $(this).addClass('active');
          $('[href="#' + targetID + '"]').addClass('active');
          $(menu)
            .slideUp(200);
          $target
            .slideDown(200, function () {
              context.find('[role="tabpanel"]').removeClass('show active');
              $target.addClass('show active');
            });
        } else {
          $(this).addClass('active');
          console.log('Classes add 3');
          $(menu).slideUp(200);
          $target.slideDown(200);
        }
      });
    }
  }
})(jQuery);