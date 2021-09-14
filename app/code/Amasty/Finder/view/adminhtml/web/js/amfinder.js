define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    $(function() {
        setTimeout(function () {
            var finderAction = $.cookie("amfinder_action");
            if (finderAction) {
                $('#' + finderAction).trigger('click');
                $.cookie("amfinder_action", null);
            }
        }, 800);
    });

    var productGridSelector = 'div[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-products-section"]',
        universalGridSelector = 'div[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-universal-section"]',
        importGridSelector = 'div[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-import-section"]',
        importHistoryGridSelector = 'div[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-import-history-section"]',
        importImagesGridSelector = 'div[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-import-images-section"]',
        universalImportGridSelector = '[data-ui-id="amasty-finder-finder-edit-tabs-tab-content-universal-import-section"]',
        productsEditTab = 'amasty_finder_finder_edit_tabs_products_section',
        importHistoryTab = 'amasty_finder_finder_edit_tabs_import_history_section',
        universalEditTab = 'amasty_finder_finder_edit_tabs_universal_section',
        selectorRedirectOnProduct = productGridSelector + ' .col-action a, '
            + productGridSelector + ' #add_new, '
            + importGridSelector + ' .col-action a, '
            + importImagesGridSelector + ' .finder-import-button',
        selectorRedirectOnuniversal = universalGridSelector + ' .col-uaction a, '
            + universalImportGridSelector + ' .finder-import-button';

    $(selectorRedirectOnProduct).on('click', function () {
        $.cookie("amfinder_action", productsEditTab);
    });

    $(selectorRedirectOnuniversal).click(function () {
        $.cookie("amfinder_action", universalEditTab);
    });

    $(importHistoryGridSelector + ' .col-action a').click(function () {
        $.cookie("amfinder_action", importHistoryTab);
    });
});
