jQuery(document).ready(function ($) {

   if(typeof payamito_gf_form ==='undefined'){
       return;
   }
    const OTPTIME = payamito_gf_form.resend_time;
    const NOUNCE = payamito_gf_form.nonce;
    const FIELD_ID = "#input_" + payamito_gf_form.form_id + "_" + payamito_gf_form.field_id;
    const AJAX_FIELD_ID ="input_" + payamito_gf_form.form_id + "_" + payamito_gf_form.field_id;
    const FIELD = $(FIELD_ID);

    if (OTPTIME !== undefined && NOUNCE !== undefined && FIELD !== undefined) {
      
        function validate_field(field) {

            $([document.documentElement, document.body]).animate({
                scrollTop: field.offset().top - 35
            }, 1000);
            field.addClass("-error-field")
            if (field.val() === null || !field.val().trim().length) {
                notification(0, payamito_gf_form.invalid)
                return false;
            }
            return true;
        }

        $(document).on('click','#ajax_send_otp',function () {
            Spinner(type = "start");
            $.ajax({
                url: payamito_gf_form.ajaxurl,
                type: 'POST',
                data: {
                    'action': "payamito_gf_validation",
                    'nonce': payamito_gf_form.nonce,
                    "phone_number": document.getElementById(AJAX_FIELD_ID).value,
                    'form_id': payamito_gf_form.form_id,

                }
            }).done(function (r, s) {

                if (s == 'success' && r != '0' && r != "" && typeof r === 'object') {
                    notification(r.e, r.message)
                    if (r.e == 1) {
                        $("#otp_div").removeClass("payamito-gf-none");
                        $("#otp_div").addClass("payamito-gf-block")
                        timer();
                    }
                }
            }).fail(function () {

            })
                .always(function (r, s) {
                    Spinner(type = "close");
                });
          
        });

        if (typeof payamito_gf_submit != "undefined") {
              ShowResend(payamito_gf_submit.show);
          }
      

        function notification(ty = -1, m) {
            switch (ty) {
                case ty = -1:
                    iziToast.error({
                        timeout: 10000,
                        title: payamito_gf_form.error,
                        message: m,
                        displayMode: 2
                    });
                    break;
                case ty = 0:
                    iziToast.warning({
                        timeout: 10000,
                        title: payamito_gf_form.warning,
                        message: m,
                        displayMode: 2
                    });
                    break;
                case ty = 1:
                    iziToast.success({
                        timeout: 10000,
                        title: payamito_gf_form.success,
                        message: m,
                        displayMode: 2
                    });
            }
        }

        function Spinner(type = "start") {
            let spinner = $("body");
            if (type == "start") {
                $.LoadingOverlay("show", { 'progress': true });
                $("form").bind("keypress", function (e) {
                    if (e.keyCode == 13) {
                        return false;
                    }
                });
            } else {
                $.LoadingOverlay("hide");
            }
        }

        function timer() {

            var timer = OTPTIME;
            var innerhtml = $("#send_otp").val()
            $("#send_otp").prop('disabled', true);
            var Interval = setInterval(function () {

                seconds = parseInt(timer);
                seconds = seconds < 10 ? "0" + seconds : seconds;
                $("#send_otp").val(seconds + ":" + payamito_gf_form.second)
                if (--timer <= 0) {
                    timer = 0;
                    $("#send_otp").removeAttr('disabled');
                    $("#send_otp").val(innerhtml);
                    clearInterval(Interval);
                }
            }, 1000);
        }

        function payamito_gf_SendOTP() {
            if (validate_field(FIELD)) {

                Spinner(type = "start");
                $.ajax({
                    url: payamito_gf_form.ajaxurl,
                    type: 'POST',
                    data: {
                        'action': "payamito_gf_validation",
                        'nonce': payamito_gf_form.nonce,
                        "phone_number": FIELD.val(),
                        'form_id': payamito_gf_form.form_id,

                    }
                }).done(function (r, s) {

                    if (s == 'success' && r != '0' && r != "" && typeof r === 'object') {
                        notification(r.e, r.message)
                        if (r.e == 1) {
                            $("#otp_div").removeClass("payamito-gf-none");
                            $("#otp_div").addClass("payamito-gf-block")
                            timer();
                        }
                    }
                }).fail(function () {

                })
                    .always(function (r, s) {
                        Spinner(type = "close");
                    });
            }
        }

        function payamito_gf_CreateButton() {
            $(FIELD_ID).after(' <div id="otp_div" class="payamito-gf-none"><br><label class="gfield_label" style="margin: 6px 0px; margin-top: 12px;" for="otp" >' + payamito_gf_form.title + '</label><br><input type="number" name="otp" id="otp" class="payamito-gf-otp-field" placeholder ="'+payamito_gf_form.placeholder+'" ><div>');
            $('#otp').after('<input type="button" class=" button payamito-gf-none payamito-gf-opt-button"   name="send_otp" id="send_otp" style="padding: 6px;margin-right: 2px;" value="' + payamito_gf_form.text + '" >');

        }
        function ShowResend(show) {
        
            if (show == true) {
                payamito_gf_CreateButton();
                $("#otp_div").removeClass("payamito-gf-none");
                $("#otp_div").addClass("payamito-gf-block");

                $("#send_otp").removeClass("payamito-gf-none");
                $("#send_otp").addClass("payamito-gf-inline-block");
            } else {
                payamito_gf_CreateButton();
                $("#otp_div").addClass("payamito-gf-none");
                $("#otp_div").removeClass("payamito-gf-block");

                $("#send_otp").addClass("payamito-gf-none");
                $("#send_otp").removeClass("payamito-gf-inline-block");

                if (typeof payamito_gf_submit != "undefined") {
                    notification(-1,payamito_gf_submit.message)
                }
              
            }
        }
        $("#send_otp").on('click', function () {

            payamito_gf_SendOTP();
        });


    }
    
});


