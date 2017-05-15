(function($, Drupal) {

    Drupal.ThemesName = Drupal.ThemesName || {};

    Drupal.behaviors.actionThemesName = {
        attach: function(context) {
            Drupal.ThemesName.mobileMenu();
        }
    };

    Drupal.ThemesName.mobileMenu = function() {
        $('.navbar-toggle').mobileMenu({
            targetWrapper: '.header .menu',
        });
    }

    $(document).ready(function() {});

})(jQuery, Drupal);
