/* ----------------- Start Document ----------------- */
(function ($) {
  "use strict";

  $(document).ready(function () {
    // Bidding Slider Average Value

    // Thousand Separator
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

    // Bidding Slider Average Value
    var avgValue =
      (parseInt($(".bidding-slider").attr("data-slider-min")) +
        parseInt($(".bidding-slider").attr("data-slider-max"))) /
      2;
    if ($(".bidding-slider").data("slider-value") === "auto") {
      $(".bidding-slider").attr({ "data-slider-value": avgValue });
    }

    // Bidding Slider Init
    $(".bidding-slider").bootstrapSlider();

    $(".bidding-slider").on("slide", function (slideEvt) {
      $(".biddingVal").text(ThousandSeparator(parseInt(slideEvt.value)));
    });
    $(".bidding-slider-widget").on("slide", function (slideEvt) {
      $(".bidding-slider").bootstrapSlider("setValue", slideEvt.value);
    });
    $(".bidding-slider-popup").on("slide", function (slideEvt) {
      $(".bidding-slider").bootstrapSlider("setValue", slideEvt.value);
    });

    $(".biddingVal").text(
      ThousandSeparator(parseInt($(".bidding-slider").val()))
    );

    //$(".range-slider-single").slider();

    /*--------------------------------------------------*/
    /*  Quantity Buttons
	  /*--------------------------------------------------*/
    function qtySum() {
      var arr = document.getElementsByName("qtyInput");
      var tot = 0;
      for (var i = 0; i < arr.length; i++) {
        if (parseInt(arr[i].value)) tot += parseInt(arr[i].value);
      }
    }
    qtySum();

    $(".qtyDec, .qtyInc").on("click", function () {
      var $button = $(this);
      var oldValue = $button.parent().find("input").val();

      if ($button.hasClass("qtyInc")) {
        $button
          .parent()
          .find("input")
          .val(parseFloat(oldValue) + 1);
      } else {
        if (oldValue > 1) {
          $button
            .parent()
            .find("input")
            .val(parseFloat(oldValue) - 1);
        } else {
          $button.parent().find("input").val(1);
        }
      }

      qtySum();
      $(".bidding-time").trigger("change");
      $(".qtyTotal").addClass("rotate-x");
    });

    $(".bidding-time-widget").on("change", function () {
      var value = $(this).val();

      $(".bidding-time-popup").val(value);
    });

    $(".bidding-time-popup").on("change", function () {
      var value = $(this).val();
      $(".bidding-time-widget").val(value);
    });
    /*--------------------------------------------------*/
    /*  Bidding
	  /*--------------------------------------------------*/
    $(".bid-now-btn").on("click", function (e) {
      e.preventDefault();
      $(".trigger-bid-popup").trigger("click");
    });

    $("#form-bidding").on("submit", function (e) {
      var success;

      var task_id = $("#form-bidding").data("post_id");
      var budget = $(".bidding-slider").val();
      var time = $("input[name='bid-time']").val();
      var proposal = $("textarea#bid-proposal").val();

      var ajax_data = {
        action: "workscout_task_bid",
        nonce: workscout_core.nonce, // pass the nonce here
        task_id: task_id,
        budget: budget,
        proposal: proposal,
        time: time,
        //'nonce': nonce
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          //refresh bids
          $(".mfp-close").trigger("click");
          $(".bidding-inner").hide();
          $(".bidding-inner-success").show();
          Snackbar.show({
            text: "Your bid has been placed!",
          });
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        })

        .then(function (data, textStatus, response) {
          if (success) {
            alert("then");
          }

          // In case your working with a deferred.promise, use this method
          // Again, you'll have to manually separates success/error
        });
      e.preventDefault();
    });

    /*--------------------------------------------------*/
    /*  Bidding actions
	  /*--------------------------------------------------*/

    $(document).on("click", ".bids-action-delete-bid", function (e) {
      e.preventDefault();
      var bid_id = $(this).parent().data("bid-id");
      var ajax_data = {
        action: "workscout_remove_bid",
        nonce: workscout_core.nonce, // pass the nonce here
        bid_id: bid_id,
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          //refresh bids
          console.log(data);
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        });

      // var referral = $(this).data("booking_id");

      // $("#send-message-from-widget textarea")
      //   .data("referral", referral)
      //   .data("recipient", recipient);
    });
    $(document).on("click", ".bids-action-accept-offer", function (e) {
      e.preventDefault();
      $(this).addClass("loading");
      var bid_id = $(this).parent().data("bid-id");
      var ajax_data = {
        action: "workscout_get_bid_data",
        nonce: workscout_core.nonce, // pass the nonce here
        bid_id: bid_id,
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          //refresh bids
          console.log(data);
          $(".bid-accept-popup h3").html(data.title);
          $(".bid-accept-popup .bid-acceptance").html(data.content);
          $(".bid-accept-popup .bid-proposal .bid-proposal-text").html(
            data.proposal
          );
          $(".bid-accept-popup #task_id").val(data.task_id);
          $(".bid-accept-popup #bid_id").val(data.bid_id);
          $(".bids-popup-accept-offer").trigger("click");
          $(".bids-action-accept-offer").removeClass("loading");
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        });

      // var referral = $(this).data("booking_id");

      // $("#send-message-from-widget textarea")
      //   .data("referral", referral)
      //   .data("recipient", recipient);
    });

    $(document).on("submit", "#accept-bid-form", function (e) {
      var task_id = $(".bid-accept-popup #task_id").val();
      var bid_id = $(".bid-accept-popup #bid_id").val();
      var ajax_data = {
        action: "workscout_accept_bid_on_task",
        nonce: workscout_core.nonce, // pass the nonce here
        bid_id: bid_id,
        task_id: task_id,
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
        success: function (data) {
          if (data.type == "success") {
            window.setTimeout(closepopup, 3000);
            //redirect to previous page
            history.back();
          } else {
          }
        },
      });

      e.preventDefault();
    });

    $(".fieldset-competencies > .field").sortable({
      items: ".resume-manager-data-row",
      cursor: "move",
      axis: "y",
      scrollSensitivity: 40,
      forcePlaceholderSize: !0,
      helper: "clone",
      opacity: 0.65,
    });

    $(document).on(
      "click",
      ".task-dashboard-action-contact-bidder",
      function (e) {
        e.preventDefault();
        var task = $(this).data("task");

        var ajax_data = {
          action: "workscout_get_bid_data_for_contact",
          nonce: workscout_core.nonce, // pass the nonce here

          task_id: task,
        };

        $.ajax({
          type: "POST",
          dataType: "json",
          url: workscout_core.ajax_url,
          data: ajax_data,
        })
          .done(function (data) {
            $(".bidding-widget").html(data.message);

            $(".contact-popup").trigger("click");
          })
          .fail(function (reason) {
            // Handles errors only
            console.debug("reason" + reason);
          });
        // $(".popup-with-zoom-anim").trigger("click");
      }
    );

    $(document).on("click", ".task-dashboard-action-review", function (e) {
      e.preventDefault();
      var task = $(this).data("task");

      var ajax_data = {
        action: "workscout_get_review_form",
        nonce: workscout_core.nonce, // pass the nonce here
        task_id: task,
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          $(".rate-form").html(data.message);
          $(".rate-popup").trigger("click");
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        });
    });

    $(document).on("submit", "#popup-commentform", function (e) {
      $("#popup-commentform button").addClass("loading").prop("disabled", true);

      $.ajax({
        type: "POST",
        dataType: "json",
        url: ws.ajaxurl,
        data: {
          action: "workscout_review_freelancer",
          data: $(this).serialize(),
          //'nonce': nonce
        },
        success: function (data) {
          if (data.success == true) {
            $("#popup-commentform").removeClass("loading").hide();
            $("#popup-commentform").hide();
            $(".workscout-rate-popup .notification").show().html(data.message);
            window.setTimeout(closepopup, 3000);
          } else {
            $(".workscout-rate-popup .notification")
              .removeClass("success")
              .addClass("error")
              .show()
              .html(data.message);
            $("#popup-commentform button")
              .removeClass("loading")
              .prop("disabled", false);
          }
        },
      });
      e.preventDefault();
    });

    $(".tasks-sort-by").on("change", function () {
      //refresh current page with value of select added to URL

      //submit form
      $("#tasks-sort-by-form").submit();
    });

    //message
    $(document).on("click", ".bids-action-send-msg", function (e) {
      var recipient = $(this).data("recipient");
      var referral = $(this).data("bid_id");

      $("#send-message-from-task textarea").val("");
      $("#send-message-from-task .notification").hide();

      $("#send-message-from-task textarea")
        .data("referral", referral)
        .data("recipient", recipient);

      $(".popup-with-zoom-anim").trigger("click");
    });

    $("body").on("submit", "#send-message-from-task", function (e) {
      $("#send-message-from-task button")
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
            $("#send-message-from-task button").removeClass("loading");
            $("#send-message-from-task .notification")
              .show()
              .html(data.message);
            window.setTimeout(closepopup, 3000);
          } else {
            $("#send-message-from-task .notification")
              .removeClass("success")
              .addClass("error")
              .show()
              .html(data.message);
            $("#send-message-from-task button")
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
        $("#send-message-from-task button")
          .removeClass("loading")
          .prop("disabled", false);
      }
    }

    //
    //  Edit bid on My Bids page
    //
    $(document).on("click", ".bids-action-edit-bid", function (e) {
      e.preventDefault();
      var bid_id = $(this).parent().data("bid-id");
      var ajax_data = {
        action: "workscout_get_bid_data_for_edit",
        nonce: workscout_core.nonce, // pass the nonce here
        bid_id: bid_id,
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          if (data.task_type == "hourly") {
            $(".bidding-detail-hourly").show();
            $(".bidding-detail-fixed").hide();
          } else {
            $(".bidding-detail-hourly").hide();
            $(".bidding-detail-fixed").show();
          }

          $(".bidding-time").val(data.time);
          $(".biddingVal").text(ThousandSeparator(parseInt(data.budget)));

          $(".bidding-widget #bid-proposal").val(data.proposal);
          $(".bidding-widget #bid_id").val(data.bid_id);
          // Bidding Slider Init

          $(".bidding-slider-popup").attr("data-slider-min", data.range_min);
          $(".bidding-slider-popup").attr("data-slider-max", data.range_max);
          $(".bidding-slider-popup").attr("data-slider-step", data.slider_step);
          // console.log(mySlider);
          $(".bidding-slider-popup").bootstrapSlider();
          $(".bidding-slider-popup").bootstrapSlider("setValue", data.budget);
          $(".bidding-slider-popup").on("slide", function (slideEvt) {
            $(".biddingVal").text(ThousandSeparator(parseInt(slideEvt.value)));
          });

          $(".popup-with-zoom-anim").trigger("click");
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        });
    });

    $("#form-bidding-update").on("submit", function (e) {
      var success;

      var bid_id = $("input[name='bid_id']").val();
      var budget = $(".bidding-slider-popup").val();
      var time = $("input[name='bid-time']").val();
      var proposal = $("textarea#bid-proposal").val();

      var ajax_data = {
        action: "workscout_update_bid",
        nonce: workscout_core.nonce, // pass the nonce here
        budget: budget,
        proposal: proposal,
        time: time,
        bid_id: bid_id,
        //'nonce': nonce
      };

      $.ajax({
        type: "POST",
        dataType: "json",
        url: workscout_core.ajax_url,
        data: ajax_data,
      })
        .done(function (data) {
          //refresh bids
          $(".mfp-close").trigger("click");
          $(".bidding-inner").hide();
          $(".bidding-inner-success").show();
          Snackbar.show({
            text: "Your bid has been updated!",
          });
          $("#my-bids-bid-id-" + bid_id + " #bid-info-budget strong").html(
            ThousandSeparator(parseInt(budget))
          );
          $("#my-bids-bid-id-" + bid_id + " #bid-info-time strong").html(time);
        })
        .fail(function (reason) {
          // Handles errors only
          console.debug("reason" + reason);
        })

        .then(function (data, textStatus, response) {
          if (success) {
            alert("then");
          }

          // In case your working with a deferred.promise, use this method
          // Again, you'll have to manually separates success/error
        });
      e.preventDefault();
    });

    var $tabsNav = $(".popup-tabs-nav"),
      $tabsNavLis = $tabsNav.children("li");

    $tabsNav.each(function () {
      var $this = $(this);

      $this
        .next()
        .children(".popup-tab-content")
        .stop(true, true)
        .hide()
        .first()
        .show();
      $this.children("li").first().addClass("active").stop(true, true).show();
    });

    $tabsNavLis.on("click", function (e) {
      var $this = $(this);

      $this.siblings().removeClass("active").end().addClass("active");

      $this
        .parent()
        .next()
        .children(".popup-tab-content")
        .stop(true, true)
        .hide()
        .siblings($this.find("a").attr("href"))
        .fadeIn();

      e.preventDefault();
    });

    var hash = window.location.hash;
    var anchor = $('.tabs-nav a[href="' + hash + '"]');
    if (anchor.length === 0) {
      $(".popup-tabs-nav li:first").addClass("active").show(); //Activate first tab
      $(".popup-tab-content:first").show(); //Show first tab content
    } else {
      anchor.parent("li").click();
    }

    $(
      ".workscout-bid-action-delete,.task-dashboard-action-delete,.bids-action-delete-bid"
    ).click(function () {
      return window.confirm(ws.i18n_confirm_delete);
    });

    /*--------------------------------------------------*/
    /*  Keywords
	/*--------------------------------------------------*/

    $(".keyword-input-container input").autocomplete({
      source: function (req, response) {
        $.getJSON(
          workscout_core.ajax_url +
            "?callback=?&action=workscout_incremental_skills_suggest",
          req,
          response
        );
      },
      select: function (event, ui) {
        // use ui.item.label as the input's value to display the label
        // use ui.item.value to display the value
        $(".keyword-input").val(ui.item.label);
        $(".keyword-input-button").trigger("click");
        $(".keyword-input").val("");
        //window.location.href = ui.item.link;
      },
      close: function (event, ui) {
        $(".keyword-input").val("");
      },
      minLength: 3,
    });

    $(".keywords-container").each(function () {
      var keywordLimit = $(this).data("limit");
 

      //if keywordlimit is undefined set it to 5
      if (typeof keywordLimit === "undefined") {
        keywordLimit = 5;
      }

      var keywordInput = $(this).find(".keyword-input");
      var keywordsList = $(this).find(".keywords-list");
      var hiddenInput = $(this).find(".keyword-input-real");
      var keywordCounter = keywordsList.children("span").length;
      
      // adding keyword
      function addKeyword() {
        var $newKeyword = $(
          "<span class='keyword'><span class='keyword-remove'></span><span class='keyword-text'>" +
            keywordInput.val() +
            "</span></span>"
        );
        keywordsList.append($newKeyword).trigger("resizeContainer");
        keywordInput.val("");
        // add $newkeyword to hidden input for form submit
        var keywordValue = keywordsList
          .find(".keyword-text")
          .map(function () {
            return $(this).text();
          })
          .get();
        keywordCounter++;

        hiddenInput.val(keywordValue);
      }

      // add via enter key
      keywordInput.on("keyup", function (e) {
        if (e.keyCode == 13 && keywordInput.val() !== "") {
          if(keywordCounter < keywordLimit){
          addKeyword();
          } else {
            Snackbar.show({
              text: "You can only add " + keywordLimit + " skills",
            });
          }
        }
      });

      // add via button
      $(".keyword-input-button").on("click", function (e) {
        e.preventDefault();
        if (keywordInput.val() !== "") {
           if(keywordCounter < keywordLimit){
            addKeyword();
           }  else {
            Snackbar.show({
              text: "You can only add " + keywordLimit + " keywords",
            });
          }
        }
      });

      // removing keyword
      $(document).on("click", ".keyword-remove", function () {
        $(this).parent().addClass("keyword-removed");

        function removeFromMarkup() {
          $(".keyword-removed").remove();
          keywordInput.val("");
          // add $newkeyword to hidden input for form submit
          var keywordValue = keywordsList
            .find(".keyword-text")
            .map(function () {
              return $(this).text();
            })
            .get();

          hiddenInput.val(keywordValue);
        }
        setTimeout(removeFromMarkup, 500);
        keywordsList.css({ height: "auto" }).height();
        keywordLimit--;

      });

      // animating container height
      keywordsList.on("resizeContainer", function () {
        var heightnow = $(this).height();
        var heightfull = $(this)
          .css({ "max-height": "auto", height: "auto" })
          .height();

        $(this).css({ height: heightnow }).animate({ height: heightfull }, 200);
      });

      $(window).on("resize", function () {
        keywordsList.css({ height: "auto" }).height();
      });

      // Auto Height for keywords that are pre-added
      $(window).on("load", function () {
        var keywordCount = $(".keywords-list").children("span").length;

        // Enables scrollbar if more than 3 items
        if (keywordCount > 0) {
          keywordsList.css({ height: "auto" }).height();
        }
      });
    });


    // if checkbox with id remote-job is checked, make input with id location not required
    // check also on page load if this chekcbox is checked
    if ($("#remote_position").is(":checked")) {
      $("#task_location").prop("required", false);
    } else {
      $("#task_location").prop("required", true);
    }
    $("#remote_position").on("change", function () {
      if ($(this).is(":checked")) {
        $("#task_location").prop("required", false);
      } else {
        $("#task_location").prop("required", true);
      }
    });
    /*--------------------------------------------------*/
    /*  Full Screen Tasks
  /*--------------------------------------------------*/

    /*--------------------------------------------------*/
    /*  Tippy JS 
	  /*--------------------------------------------------*/
    /* global tippy */
    tippy("[data-tippy-placement]", {
      delay: 100,
      arrow: true,
      arrowType: "sharp",
      size: "regular",
      duration: 200,

      // 'shift-toward', 'fade', 'scale', 'perspective'
      animation: "shift-away",

      animateFill: true,
      theme: "dark",

      // How far the tooltip is from its reference element in pixels
      distance: 10,
    });

    tippy("div.resumes", {
      target: "strong",
    });
    /*----------------------------------------------------*/
    /*  Indicator Bar
    /*----------------------------------------------------*/
    $(".indicator-bar").each(function () {
      var indicatorLenght = $(this).attr("data-indicator-percentage");
      $(this)
        .find("span")
        .css({
          width: indicatorLenght + "%",
        });
    });


    /*----------------------------------------------------*/
    /*  Ratings Script
/*----------------------------------------------------*/

    /*  Numerical Script
/*--------------------------*/
    $(".numerical-rating").numericalRating();

    $(".star-rating").starRating();

    
    //submit

    var hourly_min_field = $(".task-submit-form-container-hourly_min");
    var hourly_max_field = $(".task-submit-form-container-hourly_max");
    var budget_min_field = $(".task-submit-form-container-budget_min");
    var budget_max_field = $(".task-submit-form-container-budget_max");

   
    $('input[name="task_type"]').on("change", function () {
      var this_val = $(this).val();

      if (this_val == "fixed") {
        //using replaceWith switch the element
        hourly_min_field.replaceWith(budget_min_field);
        hourly_max_field.replaceWith(budget_max_field);
        hourly_min_field.find("input").attr("required", false);
        hourly_max_field.find("input").attr("required", false);
        budget_min_field.find("input").attr("required", true);
        budget_max_field.find("input").attr("required", true);
      } else {
        budget_min_field.replaceWith(hourly_min_field);
        budget_max_field.replaceWith(hourly_max_field);
        // Set the input fields as required
        hourly_min_field.find("input").attr("required", true);
        hourly_max_field.find("input").attr("required", true);
         budget_min_field.find("input").attr("required", false);
         budget_max_field.find("input").attr("required", false);
      }
    });
    //check which radio button is seelcted by defaulot



    // Project View

     const projectValue = $("#milestone-form").data("project-budget");
    const form = $('#milestone-form');
    const percentageInput = form.find('input[name="percentage"]');
    const amountPreview = $('#amount-preview');
    const remainingPercentageSpan = $('#remaining-percentage');
    
    // Update amount preview when percentage changes
    percentageInput.on('input', function() {
        const percentage = parseFloat($(this).val()) || 0;
        const amount = (projectValue * percentage) / 100;
        amountPreview.text(amount.toFixed(2));
    });


     $("#milestone-form").on("submit", function (e) {
       e.preventDefault();

       $.ajax({
         url: ws.ajaxurl,
         type: "POST",
         data: {
           action: "save_milestone",
           project_id: $('input[name="project_id"]').val(),
           nonce: $("#milestone_nonce").val(),
           title: $("#milestone-title").val(),
           percentage: $("#milestone-percentage").val(),
           description: $("#milestone-description").val(),
           due_date: $("#milestone-due-date").val(),
           amount: $("#milestone-amount").val(),
         },
         success: function (response) {
           if (response.success) {
             location.reload();
           } else {
             alert("Error saving milestone");
           }
         },
       });
     });

     // Handle milestone approval
     $(".approve-milestone").on("click", function () {
       const milestoneId = $(this).data("id");

       $.ajax({
         url: ws.ajaxurl,
         type: "POST",
         data: {
           action: "approve_milestone",
           milestone_id: milestoneId,
           project_id: $('input[name="project_id"]').val(),
           nonce: $("#milestone_nonce").val(),
         },
         success: function (response) {
           if (response.success) {
                     remainingPercentageSpan.text(response.data.remaining_percentage.toFixed(1));
                    
                    // Update max value for percentage input
                    percentageInput.attr('max', response.data.remaining_percentage);
                    
                    // Clear form
                    form[0].reset();
                    amountPreview.text('0.00');
             location.reload();
           } else {
             alert("Error approving milestone");
           }
         },
       });
     });



    // ------------------ End Document ------------------ //
  });
})(this.jQuery);

/* ----------------- Start Document ----------------- */
(function ($) {
"use strict";

function starsOutput(firstStar, secondStar, thirdStar, fourthStar, fifthStar) {
		return(''+
			'<span class="'+firstStar+'"></span>'+
			'<span class="'+secondStar+'"></span>'+
			'<span class="'+thirdStar+'"></span>'+
			'<span class="'+fourthStar+'"></span>'+
			'<span class="'+fifthStar+'"></span>');
	}

$.fn.numericalRating = function(){

	this.each(function() {
		var dataRating = $(this).attr('data-rating');

		// Rules
	    if (dataRating >= 4.0) {
	        $(this).addClass('high');
	    } else if (dataRating >= 3.0) {
	        $(this).addClass('mid');
	    } else if (dataRating < 3.0) {
	        $(this).addClass('low');
	    }

	});

}; 

/*  Star Rating
/*--------------------------*/
$.fn.starRating = function(){


	this.each(function() {

		var dataRating = $(this).attr('data-rating');
		if(dataRating > 0) {
			// Rating Stars Output
			
			var fiveStars = starsOutput('star','star','star','star','star');

			var fourHalfStars = starsOutput('star','star','star','star','star half');
			var fourStars = starsOutput('star','star','star','star','star empty');

			var threeHalfStars = starsOutput('star','star','star','star half','star empty');
			var threeStars = starsOutput('star','star','star','star empty','star empty');

			var twoHalfStars = starsOutput('star','star','star half','star empty','star empty');
			var twoStars = starsOutput('star','star','star empty','star empty','star empty');

			var oneHalfStar = starsOutput('star','star half','star empty','star empty','star empty');
			var oneStar = starsOutput('star','star empty','star empty','star empty','star empty');

			// Rules
	        if (dataRating >= 4.75) {
	            $(this).append(fiveStars);
	        } else if (dataRating >= 4.25) {
	            $(this).append(fourHalfStars);
	        } else if (dataRating >= 3.75) {
	            $(this).append(fourStars);
	        } else if (dataRating >= 3.25) {
	            $(this).append(threeHalfStars);
	        } else if (dataRating >= 2.75) {
	            $(this).append(threeStars);
	        } else if (dataRating >= 2.25) {
	            $(this).append(twoHalfStars);
	        } else if (dataRating >= 1.75) {
	            $(this).append(twoStars);
	        } else if (dataRating >= 1.25) {
	            $(this).append(oneHalfStar);
	        } else if (dataRating < 1.25) {
	            $(this).append(oneStar);
	        }
		}
	});

}; 
})(jQuery);

