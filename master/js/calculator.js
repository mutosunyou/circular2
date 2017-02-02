$(document).ready(function() {
  // Add calculator key actions
  // ***************************
  $.extend( $.keyboard.keyaction, {
    // Memory storage functions (sorta)
    // ***************************
    clearall : function(base){
      base.$preview.val('');
      $.keyboard.keyaction.mc(base);
    },
    mc : function(base){ // memory clear
      base.memory = base.memory2 = '';
      base.$keyboard.find('.ui-keyboard-mc').removeClass(base.options.css.buttonAction);
    },
  });

  // Initialize keyboard
  // ********************
  $('#in_money').keyboard({
    layout : 'custom',
    display : {
      'clearall' : 'C',
      'mc'    : 'MC',
    },
    customLayout: {
      'default' : [
        '7 8 9 ',
        '4 5 6 ',
        '1 2 3 ',
        '0 {a} {clearall}'
      ],
    },

    // Turning restrictInput on (true), prevents yroot and but it
    restrictInput : true,  // Prevent keys not in the displayed keyboard from being typed in
    useCombos     : false, // don't want A+E to become a ligature
    wheelMessage  : '',    // clear tooltips

    // set up degree/radian/grads mode
    visible : function(e, kb, el){ // e = event, kb = keyboard object, el = original input
      var mode = kb.mode || 0, sel = '.ui-keyboard-';
      sel += (mode === 1) ? 'deg' : (mode === 2) ? 'grad' : 'rad';
      kb.$keyboard.find(sel).addClass(kb.options.css.buttonAction);
    },
    // multiple parameter functions highlight when first parameter is saved,
    // this makes sure the buttons aren't highlighted when the memory storage clears.
    change : function(e, kb, el){
      if (kb.memory2 === '') {
        kb.$keyboard.find('.ui-keyboard-xy, .ui-keyboard-yroot').removeClass(kb.options.css.buttonAction);
      }
    }
  })
    .addTyping()
    .getkeyboard();
});

$('#in_money_keyboard').change(function(){
  console.log($('#in_money_keyboard').val());
});


