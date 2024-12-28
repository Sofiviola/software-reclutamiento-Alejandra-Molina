/* ----------------- Start Document ----------------- */
(function ($) {
  "use strict";

  $(document).ready(function () {
    $(".tasks-list-container").on(
      "update_task_results",
      function (event, page, append, loading_previous) {
        var results = $(".tasks-list-container");

        var filter = $("#workscout-frelancer-search-form-tasks");
        var data = filter.serializeArray();

        var style = results.data("style");
        var tax_category = results.data("category");
        var tax_feature = results.data("feature");
        var per_page = results.data("per_page");
        var custom_class = results.data("custom_class");
        var order = results.data("orderby");

        data.push({ name: "action", value: "workscout_get_tasks" });
        data.push({ name: "page", value: page });
        data.push({ name: "style", value: style });
        data.push({ name: "per_page", value: per_page });
        data.push({ name: "custom_class", value: custom_class });
        data.push({ name: "order", value: order });

        var has_listing_category_search = false;
        var has_listing_feature_search = false;

        $.each(data, function (i, v) {
          if (v.name.substring(0, 15) == "tax-task_catego") {
            if (v.value) {
              has_listing_category_search = true;
            }
          }

          if (v.name.substring(0, 15) == "task_skill") {
            if (v.value) {
              has_listing_feature_search = true;
            }
          }
        });

        if (!has_listing_category_search) {
          if (tax_category) {
            data.push({ name: "tax-task_category", value: tax_category });
          }
        }
        if (!has_listing_feature_search) {
          if (tax_feature) {
            data.push({ name: "task_skill", value: tax_feature });
          }
        }

        $.ajax({
          type: "post",
          dataType: "json",
          url: workscout_core.ajax_url,
          data: data,
          beforeSend: function (xhr) {
            results.addClass("loading");
          },
          success: function (data) {
            
            results.removeClass("loading");
            $(results).html(data.html);
            	$("#titlebar .count_jobs,.filters-headline .count_jobs").html(
                data.counter
              );
              
              if (data.counter == 1) {
                $("#titlebar .tasks_text,.filters-headline .tasks_text").html(
                  ws.single_task_text
                );
              } else {
                $("#titlebar .tasks_text,.filters-headline .tasks_text").html(
                  ws.plural_task_text
                );
              }
            $("div.pagination-container").html(data.pagination);
//scroll page to top
            
            $(".tasks-list-container").triggerHandler(
              "update_task_results_success"
            );
          },
        });
      }
    );

    $(document)
      .on(
        "change",
        ".sort-by-select .orderby, #workscout-frelancer-search-form-tasks.ajax-search select,  #workscout-frelancer-search-form-tasks.ajax-search input:not(.range-slider):not(#location_search)",
        function (e) {
          console.log("trigger");
          console.log(e);
          var target = $(".tasks-list-container");
          target.triggerHandler("update_task_results", [1, false]);
          //job_manager_store_state( target, 1 );
        }
      )
      .on("keyup", function (e) {
        if (e.which === 13) {
          e.preventDefault();
          $(this).trigger("change");
        }
      });

    $(".range-slider").on("slideStop", function () {
      
       var target = $(".tasks-list-container");
       target.triggerHandler("update_task_results", [1, false]);
    });

    // trigger change event on all inputs except those that have class .range-slider


    if (
      $("#workscout-frelancer-search-form-tasks:not(.main-search-form)").length
    ) {
      document.getElementById(
        "workscout-frelancer-search-form-tasks"
      ).onkeypress = function (e) {
        var key = e.charCode || e.keyCode || 0;
        if (key == 13) {
          if ($("#location_search:focus").length) {
            return false;
          }
          var target = $("div#listeo-listings-container");
          target.triggerHandler("update_task_results", [1, false]);
          e.preventDefault();
        }
      };
    }

    var filter_by_radius_status = $(".filter_by_radius").prop("checked");
    if (filter_by_radius_status) {
      $(".search_location")
        .find(".widget_range_filter-inside")
        .addClass("slider-enabled");
      //  $("#radius-range").slider("enable");
    } else {
      //$("#radius-range").slider("disable");
    }

    $(".filter_by_radius").change(function (event) {
      $(this)
        .parents(".search_location")
        .find(".widget_range_filter-inside")
        .toggleClass("slider-enabled");

      var ckb_status = $("#radius_check").prop("checked");
      if (ckb_status) {
        $("#radius-range").slider("enable");
      } else {
        $("#radius-range").slider("disable");
      }
    });

    $(".filter_by_check").change(function (event) {
      // alert("ok" );
      $(this)
        .parents(".widget")
        .find(".widget_range_filter-inside")
        .toggleClass("slider-enabled");
    });

    //$(document).on('click', 'div.pagination-container a', function(e) {
    $("div.pagination-container.ajax-search").on("click", "a", function (e) {
      e.preventDefault();
      var results = $(".tasks-list-container");
      var filter = $("#workscout-frelancer-search-form-tasks");
      var page = $(this).parent().data("paged");
      console.log(page);
      if (page == "next") {
        var page = $(".pagination li.current").data("paged") + 1;
      }
      if (page == "prev") {
        var page = $(".pagination li.current").data("paged") - 1;
      }
      results.triggerHandler("update_task_results", [page, false]);

      $("body, html").animate(
        {
          scrollTop: $("#titlebar, .page-title").offset().top,
        },
        600
      );

      return false;
    });

    // ------------------ End Document ------------------ //
  });
})(this.jQuery);
/**/
