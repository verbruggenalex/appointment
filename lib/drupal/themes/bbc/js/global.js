(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.global = {
    attach: function (context, settings) {
      // Make items per page clone control the original one.
      // @todo, this only needs to be loaded set on certain views.
      $(".view-footer select[name='items_per_page']", context).once('controlOriginalItemsPerPage').each(function () {
        var id = '#' + $(this).attr('id').replace('-clone', '');
        if ($(id).length) {
          $(this).change(function () {
            $(id).val($(this).val());
            $(id).trigger('change');
          });
        }
      });
      // When the appointment form date changes, change the calendars date.
      $("#edit-date-select").change(function () {
        // We wait until all ajax requests have finished.
        $(document).ajaxStop(function () {
          if (settings.calendar.length) {
            // Change date on calendar(s).
            settings.calendar.forEach(function (calendar) {
              calendar.gotoDate($("#edit-date-select").val());
            });
          }
        });
      });

      // document.ready event does not work with BigPipe. The workaround is to
      // check the document state every 100 milliseconds until it is completed.
      // @see https://www.drupal.org/project/drupal/issues/2794099#comment-13274828
      var timesRun = 0;
      var checkReadyState = setInterval(() => {
        timesRun += 1;
        // We cancel this operation after 10 seconds.
        if (timesRun === 100) {
          clearInterval(checkReadyState);
        }
        // If the calendar(s) are built, we add our dayRender callback.
        if (document.readyState === "complete" && typeof settings.calendar != "undefined") {
          clearInterval(checkReadyState);
          settings.calendar.forEach(function (calendar) {
            calendar.setOption("dayRender", function (dayinfo) {
              // @see: https://dev.to/racztiborzoltan/javascript-format-date-as-yyyy-mm-dd-4ef3
              var date = new Date(dayinfo.date.getTime() - dayinfo.date.getTimezoneOffset() * 60 * 1000).toISOString().split('T')[0];
              if (date != $("#edit-date-select").val()) {
                $("#edit-date-select").val(date);
                $("#edit-date-select").trigger('change');
              }
            });
          });
        }
      }, 300);
    }
  }

})(jQuery, Drupal, drupalSettings);
