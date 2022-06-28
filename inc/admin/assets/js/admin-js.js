jQuery(document).ready(function($) {

  $(".payamito-gravity-form-modal ").click(function() {
      debugger;
      $('#payamito-gravity-form-modal').modal();
  })

  $('.payamito-gf-tag-modal').click(function(){
      $(this).CopyToClipboard();
   
      });

      $('.payamito-gf-tag-modal').jTippy({trigger:'click' ,theme: 'green',position:'bottom', size: 'small',title:'کپی شد'});
});