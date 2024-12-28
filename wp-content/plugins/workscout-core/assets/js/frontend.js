  /* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){
  // Contact Form Ajax

  // Fun Facts
  function funFacts() {
    /*jslint bitwise: true */
    function hexToRgbA(hex) {
      var c;
      if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
        c = hex.substring(1).split("");
        if (c.length == 3) {
          c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c = "0x" + c.join("");
        return (
          "rgba(" +
          [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(",") +
          ",0.07)"
        );
      }
    }

    $(".fun-fact").each(function () {
      var factColor = $(this).attr("data-fun-fact-color");

      if (factColor !== undefined) {
        $(this)
          .find(".fun-fact-icon")
          .css("background-color", hexToRgbA(factColor));
        $(this).find("i").css("color", factColor);
      }
    });
  }
  funFacts();

  $("#workscout-activities-list a.close-list-item").on("click", function (e) {
    var $this = $(this),
      id = $(this).data("id"),
      nonce = $(this).data("nonce");

    $.ajax({
      type: "POST",
      dataType: "json",
      url: ws.ajaxurl,
      data: {
        action: "remove_activity",
        id: id,
        nonce: nonce,
      },
      success: function (data) {
        if (data.success == true) {
          $this
            .parent()
            .addClass("wait")
            .fadeOut("normal", function () {
              $this.remove();
            });
        } else {
        }
      },
    });
    e.preventDefault();
  });

  $("#workscout-clear-activities").on("click", function (e) {
    var $this = $(this),
      nonce = $(this).data("nonce");

    $.ajax({
      type: "POST",
      dataType: "json",
      url: ws.ajaxurl,
      data: {
        action: "remove_all_activities",
        nonce: nonce,
      },
      success: function (data) {
        if (data.success == true) {
          $("ul#workscout-activities-list li:not(.cleared)").remove();
          $("li.cleared").show();
          $this.parent().parent().find(".pagination-container").remove();
        } else {
        }
      },
    });
    e.preventDefault();
  });

  $("body").on("submit", "#send-message-from-widget", function (e) {
    $("#send-message-from-widget button")
      .addClass("loading")
      .prop("disabled", true);

    $.ajax({
      type: "POST",
      dataType: "json",
      url: ws.ajaxurl,
      data: {
        action: "workscout_send_message",
        recipient: $(this).find("textarea#contact-message").data("recipient"),
        referral: $(this).find("textarea#contact-message").data("referral"),
        message: $(this).find("textarea#contact-message").val(),
        //'nonce': nonce
      },
      success: function (data) {
        if (data.type == "success") {
          $("#send-message-from-widget button").removeClass("loading");
          $("#send-message-from-widget .notification")
            .show()
            .html(data.message);
          //window.setTimeout( closepopup, 3000 );
        } else {
          $("#send-message-from-widget .notification")
            .removeClass("success")
            .addClass("error")
            .show()
            .html(data.message);
          $("#send-message-from-widget button")
            .removeClass("loading")
            .prop("disabled", false);
        }
      },
    });
    e.preventDefault();
  });

  function closepopup() {
    var magnificPopup = $.magnificPopup.instance;
    if (magnificPopup) {
      magnificPopup.close();
      $("#send-message-from-widget button")
        .removeClass("loading")
        .prop("disabled", false);
    }
  }

  $("#send-message-from-chat").on("submit", function (e) {
    var message = $(this).find("textarea#contact-message").val();

    if (message) {
      $(this).find("textarea#contact-message").removeClass("error");
      $(".loading").show();
      $(this).find("button").prop("disabled", true);
      $.ajax({
        type: "POST",
        dataType: "json",
        url: ws.ajaxurl,
        data: {
          action: "workscout_send_message_chat",
          recipient: $(this).find("input#recipient").val(),
          conversation_id: $(this).find("input#conversation_id").val(),
          message: message,
          //'nonce': nonce
        },
        success: function (data) {
          if (data.type == "success") {
            $(this).addClass("success");
            refreshMessages();
            $("#send-message-from-chat textarea").val("");
            $("#send-message-from-chat button").prop("disabled", false);
          } else {
            $(this).addClass("error");
          }
        },
      });
    } else {
      $(this).find("textarea#contact-message").addClass("error");
    }
    e.preventDefault();
  });

  // Default Bootstrap Range Slider
  var currencyAttr = $(".range-slider").attr("data-slider-currency");
 function ThousandSeparator(nStr) {
   nStr += "";
   var x = nStr.split(".");
   var x1 = x[0];
   var x2 = x.length > 1 ? "." + x[1] : "";
   var rgx = /(\d+)(\d{3})/;
   while (rgx.test(x1)) {
     x1 = x1.replace(rgx, "$1" + "," + "$2");
   }
   return x1 + x2;
 }
  $(".range-slider").bootstrapSlider({
    formatter: function (value) {
      return (
        currencyAttr +
        ThousandSeparator(parseInt(value[0])) +
        " - " +
        currencyAttr +
        ThousandSeparator(parseInt(value[1]))
      );
    },
  });

  //pikaday
  $(".job-manager-datepicker").pikaday({
    // parse(dateString, format) {
    //   // dateString is the result of `toString` method
    //   const parts = dateString.split("/");
    //   const day = parseInt(parts[0], 10);
    //   const month = parseInt(parts[1], 10) - 1;
    //   const year = parseInt(parts[2], 10);
    //   return new Date(year, month, day);
    // },
  });

  // $('#send-message-from-booking').on('submit',function(e) {

  //   var message = $(this).find('textarea#contact-message').val();

  //   if(message){
  //     $(this).find('textarea#contact-message').removeClass('error');
  //     $('.loading').show();
  //     $(this).find('button').prop('disabled', true);
  //      $.ajax({
  //           type: 'POST', dataType: 'json',
  //           url: workscout.ajaxurl,
  //           data: {
  //               'action': 'workscout_send_message_chat',
  //               'recipient' : $(this).find('input#recipient').val(),
  //               'conversation_id' : $(this).find('input#conversation_id').val(),
  //               'message' : message,
  //               //'nonce': nonce
  //              },
  //           success: function(data){

  //               if(data.type == "success") {
  //                   $(this).addClass('success');
  //                   refreshMessages();
  //                   $('#send-message-from-chat textarea').val('');
  //                   $('#send-message-from-chat button').prop('disabled', false);
  //               } else {
  //                   $(this).addClass('error')
  //               }

  //           }
  //       });
  //    } else {
  //       $(this).find('textarea#contact-message').addClass('error');

  //    }
  //     e.preventDefault();
  // });

  $(document).on("click", ".booking-message", function (e) {
    var recipient = $(this).data("recipient");
    var referral = $(this).data("booking_id");

    $("#send-message-from-widget textarea")
      .data("referral", referral)
      .data("recipient", recipient);

    $(".send-message-to-owner").trigger("click");
  });

  function refreshMessages() {
    if ($(".message-bubbles").length) {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: ws.ajaxurl,
        data: {
          action: "workscout_get_conversation",
          conversation_id: $(
            "#send-message-from-chat input#conversation_id"
          ).val(),
          //'nonce': nonce
        },
        success: function (data) {
          if (data.type == "success") {
            $(".message-bubbles").html(data.message);
          }
          $(".loading").hide();
        },
        complete: function () {
          setTimeout(refreshMessages, 4000);
        },
      });
    }
  }
  setTimeout(refreshMessages, 4000);

  function avatarSwitcher() {
    var readURL = function (input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          $(".profile-pic").attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }
    };

    $(".workscout-avatar-form .file-upload").on("change", function () {
      readURL(this);
    });

    $(".workscout-avatar-form .upload-button").on("click", function () {
      $(".file-upload").click();
    });
  }
  avatarSwitcher();

  // ------------------ End Document ------------------ //
});

})(this.jQuery);
/**/