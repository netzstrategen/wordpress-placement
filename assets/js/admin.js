(function ($) {
  'use strict';

  if (typeof acf === 'undefined') {
    return;
  }
  acf.add_filter('select2_args', function (args, $select, settings) {
    args.ajax.results = function (data) {
      $.each(data.results, function (index, element) {
        if ($('input[name^="acf[placement_positions]["][type=hidden][value="' + element.id + '"]').length) {
          element.disabled = true;
        }
      });
      return data;
    };
    return args;
  });

})(jQuery);
