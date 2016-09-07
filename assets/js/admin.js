(function ($) {
  'use strict';

  acf.add_filter('select2_args', function (args, $select, settings) {
    args.ajax.results = function (data) {
      $.each(data, function (index, element) {
        if ($('input[name^="acf[placement_positions]["][type=hidden][value="' + element.id + '"]').length) {
          element.disabled = true;
        }
      });
      return {
        results: data,
        more: true
      };
    };
    return args;
  });

})(jQuery);
