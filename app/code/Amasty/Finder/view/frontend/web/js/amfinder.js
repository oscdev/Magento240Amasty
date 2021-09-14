/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2015 Amasty (http://www.amasty.com)
 * @package Amasty_Finder
 */
define([
    'jquery',
    'underscore',
    'mage/translate',
    'Amasty_Finder/js/chosen.jquery.min'
], function ($, _) {
    $.widget('mage.amfinder', {
        options: {
            containerId: 'amfinder_Container',
            ajaxUrl: '',
            loadingText: '',
            isPartialSearch: 0,
            ajaxRequest: null,
            autoSubmit: false,
            isChosenEnable: false
        },
        selects: [],
        _isFirstLoad: 0,
        dropdownSelector: '[data-amfinder-js="select"]',
        dropdownTitle: '[data-amfinder-js="text"]',
        dropdownItem: '[data-amfinder-js="item"]',

        _create: function () {
            var self = this,
                isCustomDropdown,
                container = $('#' + self.options.containerId);

            container.find('[data-amfinder-js="title"]').on('click', function () {
                $(this).parent().toggleClass('open');
            });

            self.selects = container.find(self.dropdownSelector);

            _.each(self.selects, function (element) {
                var $element = $(element);

                if ($element.is('select') && self.options.isChosenEnable) {
                    $element.chosen();
                } else if ($element.is('div') && !isCustomDropdown) {
                    self._initCustomDropdown();
                    isCustomDropdown = true;
                }
            });

            container.on('change', self.dropdownSelector, self, self._onChange);
        },

        _onChange: function (event) {
            var parentId,
                select = this,
                self = event.data,
                selectToReload = null;

            if ($(select).is('select')) {
                parentId = select.value;
            } else {
                parentId = $(select).next().find('input:checked').val();
                select.value = parentId;
            }

            /* should load next element's options only if current is not the last one */
            for (var i = 0; i < self.selects.length; i++) {
                if (self.selects[i].id == select.id && i != self.selects.length - 1) {
                    selectToReload = self.selects[i + 1];
                    break;
                }
            }

            self._clearAllBelow(select);
            if (selectToReload && parentId != 0) {
                self._loadDropDownValues(selectToReload, parentId, $(select).attr('data-dropdown-id'));
            }

            if (parentId == 0 && self.ajaxRequest) {
                self.ajaxRequest.abort();
            }
        },

        _loadDropDownValues: function (selectToReload, parentId, parentDropdownId) {
            var self = this,
                $selectToReload = $(selectToReload),
                dropdownId = $selectToReload.attr('data-dropdown-id');

            if (!dropdownId) {
                return;
            }

            if (this.ajaxRequest) {
                this.ajaxRequest.abort();
            }

            this.ajaxRequest = $.ajax({
                url: self.options.ajaxUrl,
                data: {
                    dropdown_id: dropdownId,
                    parent_id: parentId,
                    use_saved_values: self._isFirstLoad,
                    parent_dropdown_id: parentDropdownId
                },
                type: "POST",
                dataType: "html",
                success: function (response) {
                    var selectedValue = 0,
                        isSelect = $selectToReload.is('select');

                    $selectToReload = isSelect ? $selectToReload : $selectToReload.next();
                    $selectToReload.append(response);
                    if ($selectToReload.children().length) {
                        $('[data-dropdown-id=' + dropdownId + ']').removeAttr("disabled");
                        selectedValue = isSelect ? $selectToReload.val() : $selectToReload.find('input:checked').val();
                    }

                    if (selectedValue && selectedValue != 0) {
                        if (isSelect) {
                            $selectToReload.change()
                        } else {
                            $selectToReload.prev().trigger('change');
                            self._fillCustomDropdown($selectToReload);
                        }
                    }

                    if (isSelect) {
                        $selectToReload.trigger("chosen:updated");
                    }
                }
            });
        },

        _clearAllBelow: function (select) {
            var self = this,
                startClearing = false,
                $currentSelect,
                isSelect,
                isLast,
                shouldShow,
                buttonsSelector = '#' + this.options.containerId + ' [data-amfinder-js="buttons"]';

            for (var i = 0; i < this.selects.length; i++) {
                $currentSelect = $(this.selects[i]);
                isSelect = $currentSelect.is('select');

                if (startClearing) {
                    $currentSelect.attr("disabled", "disabled");

                    if (!isSelect) {
                        $currentSelect.find(self.dropdownTitle).empty();
                        $currentSelect.find('[data-amfinder-js="text"]').text($.mage.__('Please Select ...'));
                        $currentSelect.next().empty();
                    } else {
                        $currentSelect.empty();
                    }
                }

                if (this.selects[i].id == select.id) {
                    startClearing = true;
                    if (i == 0) {
                        select.isFirst = true;
                    }
                    if (i == this.selects.length - 1) {
                        select.isLast = true;
                    }
                }

                if (isSelect) {
                    $currentSelect.trigger("chosen:updated");
                }
            }

            shouldShow = (this.options.isPartialSearch && (!select.isFirst || select.value > 0))
                || (!this.options.isPartialSearch && select.isLast && select.value > 0);

            if (shouldShow && this.options.autoSubmit && select.isLast && !this._isFirstLoad) {
                $(buttonsSelector + ' button.action').click();
            } else {
                if (shouldShow) {
                    $(buttonsSelector).show();
                } else {
                    $(buttonsSelector).hide();
                }
            }
        },

        _initCustomDropdown: function () {
            var self = this,
                containerSelect = '#' + self.options.containerId,
                dropdown = $(containerSelect + ' ' + self.dropdownSelector),
                dropdownTitle = self.dropdownTitle,
                dropdownWrapper = '[data-amfinder-js="wrapper"]',
                details = $(containerSelect + ' [data-amfinder-js="select-details"]'),
                resetButton = $(containerSelect + ' [data-amfinder="reset"]'),
                itemValue,
                attrName;

            if (details.find('input:checked').length) {
                _.each(details, function (item) {
                    attrName = $(item).find('input:checked').next().attr('name');
                    $(item).find('input:checked').parent().addClass('-active');
                    $(item).parents(dropdownWrapper).find(dropdownTitle).text(attrName);
                });
            }

            resetButton.on('click', function (e) {
                e.stopPropagation();
                self._resetSelectedValue(this);
                self._closeCustomDropdown(dropdown, details);
            });

            dropdown.on('click', function () {
                details.not($(this).next()).hide();
                dropdown.not(this).removeClass('-active');
                dropdown.removeClass('-reset');
                $(this).toggleClass('-active');
                $(this).next().toggle();

                if ($(this).hasClass('-active')) {
                    self._checkSelectedValue(this);
                }
            });

            details.on('change', '[data-amfinder-js="input-hidden"]', function () {
                $('#' + $(this).attr('data-name-js')).trigger('change');
            });

            details.on('click', '[data-amfinder-js="close"]', function () {
                self._closeCustomDropdown(dropdown, details);
            });

            $(document).on('click', function (e) {
                if ($(e.target).closest(dropdownWrapper).length === 0
                    || $(e.target).closest('.amfinder-item').length > 0
                ) {
                    self._closeCustomDropdown(dropdown, details);
                }
            });

            details.on('change', self.dropdownItem, function () {
                itemValue = $(this).find('[data-amfinder-js="input-hidden"]').attr('data-item-label');
                $(this).parents(dropdownWrapper).find(dropdownTitle).text(itemValue);
                $(this).siblings(self.dropdownItem).removeClass('-active');
                $(this).addClass('-active');
                self._closeCustomDropdown(dropdown, details);
            });
        },

        _closeCustomDropdown: function (dropdown, details) {
            dropdown.removeClass('-active -reset');
            details.hide();
        },

        _fillCustomDropdown: function (select) {
            var self = this,
                label = select.find('input:checked').next(),
                item = select.find('input:checked').parent(),
                $select = select.prev().find(self.dropdownTitle);

            $select.text(label.attr('name'));
            item.addClass('-active');
        },
        
        _checkSelectedValue: function (element) {
            var $dropdown = $(element);

            if ($dropdown.next().find('input:checked').length) {
                $dropdown.addClass('-reset');
            }
        },

        _resetSelectedValue: function (element) {
            var self = this,
                $element = $(element),
                dropdown = $element.parent(),
                dropdownDetails = dropdown.next();

            $element.prev().text($.mage.__('Please Select ...'));
            dropdownDetails.find('input:checked').prop("checked", false);
            dropdownDetails.find(self.dropdownItem).removeClass('-active');
            dropdown.removeClass('-reset');

            self._clearAllBelow($element.parent()[0]);
        }
    });

    return $.mage.amfinder;
});
