jQuery(function ($) { // console.log('tenderSelectionEdit.js');
    if (!mh.hasOwnProperty('tenders')) mh.tenders = {};
    _.extend(mh.tenders, {
        selectionsEdit: {

            data: {
                categories: [],

                category: null,

                currentSelection: null,

                currentOption: null
            },

            init: function () {
                //this.calcTotalPrices();
                this.calcTotalQuantities();
                $('.mh-product-wrapper .mh-loading').hide();
            },

            sync: function () {//console.log(event.target);
                $el = $(mh.events.getTarget());
                $wrapper = $el.closest('.mh-product-wrapper'); //console.log('$wrapper', $wrapper);
                $chkBox = $wrapper.find('.mh-checkbox');
                $qty = $wrapper.find('.mh-quantity-input');
                $note = $wrapper.find('.mh-note textarea');

                selectionId = parseInt($chkBox.data('placeholder-id')); //console.log('selectionId', selectionId);
                productId = parseInt($chkBox.data('option-id')); //console.log('productId', productId);

                // Sync checked/quantity values
                if ($el.is('.mh-checkbox')) {
                    $qty.val($chkBox.prop('checked') ? 1 : 0); //console.log('select', $qty);
                } else if ($el.is('.mh-quantity-input') || $el.is('.mh-quantity a')) { // console.log('quantity');
                    $chkBox.prop('checked', $qty.val() > 0);
                }

                // Sync original values (if viewing modal)
                $modal = $el.closest('#colorbox');
                if ($modal.length) { //console.log('isModal', $orig);
                    $orig = $('.page .mh-products [data-id][value=' + productId + ']').closest('.mh-product-wrapper'); //$modal.find('[data-id]').val());
                    $orig.find('.mh-checkbox').prop('checked', $chkBox.prop('checked'));
                    $orig.find('.mh-quantity-input').val($qty.val());
                }

                // Sync Model
                self.data.currentSelection = _.findWhere(self.data.category.selections, { placeholderId: selectionId }); //console.log('currentSelection', self.data.currentSelection);
                self.data.currentOption = _.findWhere(self.data.currentSelection.substitutionOptions, { optionId: productId }); //console.log('currentOption', self.data.currentOption);
                self.data.currentOption.selectCount = parseInt($qty.val());
                self.data.currentOption.clientComment = $note.val();

                // Don't sync if invalid data
                if (selectionId == 'NaN') {
                    console.warn('Invalid selectionn ID');
                    return;
                } else if (productId == 'NaN') {
                    console.warn('Invalid product ID');
                    return;
                } else console.log('Syncing...', self.data.currentOption);

                // Sync Server
                if (self.syncTimeout) window.clearTimeout(this.syncTimeout);
                this.syncTimeout = window.setTimeout(function () {
                    var xhrParams = _.findWhere(self.xhr.actions, {
                        myHomeAction: 'selectionEdit',
                    });
                    xhrParams.tenderId = self.data.tender.tenderid;
                    xhrParams.categoryId = self.data.category.optionCategoryId;
                    xhrParams.selections = [];

                    /*$.each($('.mh-wrapper-tender-selection-edit').serializeArray(), function (key, param) {
                        xhrParams[param.name] = param.value;
                    }); console.log(xhrParams);*/
                    _.each(self.data.category.selections, function (selection, i) {
                        var placeholder = {
                            placeholderId: selection.placeholderId,
                            substitutionOptions: []
                        };
                        _.each(selection.substitutionOptions, function (option, e) { //console.log(i, e, option);
                            placeholder.substitutionOptions.push({
                                optionId: option.optionId,
                                selectCount: option.selectCount,
                                clientComment: option.clientComment
                            });
                        });
                        xhrParams.selections.push(placeholder);
                    }); //console.log(xhrParams);

                    $.post(self.xhr.url, xhrParams, function (response) { console.log('Updated success', response);
                        //self.calcTotalPrices();
                        self.calcTotalQuantities();
                        //console.log(remainInt);
                        var $alert = $el.closest('.mh-products').find('.mh-alert');
                        $alert.addClass('on').css('height', $alert.find('> div').height() + 'px');
                        $('.mh-product-wrapper .mh-loading').hide();
                    }, "json")
                    .fail(function (response) { console.log(response, status);
                        alert("The selection update has failed");
                        $('.mh-product-wrapper .mh-loading').hide();
                    });
                    $wrapper.find('.mh-loading').show();
                }, 1000);
            },

            syncTimeout: false,

            adjustQuantityBy: function (amount) { //console.log('adjustQuantityBy()');
                var $input = $(mh.events.getTarget()).closest('.mh-quantity').find('.mh-quantity-input');
                $input.val(parseInt($input.val()) + amount);
                $input.trigger('change');
            },

            toggleNote: function (selectionId) {
                console.log('note.toggleNote()', selectionId);
                $(mh.events.getTarget()).closest('.mh-product-wrapper').toggleClass('on-note');
            },

            /* Moved to server-side response
            calcTotalPrices: function () {
                console.log('calcTotalPrices()');
                $('.mh-products').each(function () {
                    var amounts = $(this).find('.mh-product-wrapper').map(function () {
                        if (!$(this).find('.mh-checkbox').is(':checked')) return;
                        //console.log('checkbxes ', $(this).find('.mh-checkbox'));

                        var quantity = $(this).find('input.mh-quantity-input').val();
                        var price = $(this).find('[data-upgrade-price]').data('upgrade-price');
                        //console.log(quantity, price);

                        return quantity * price;
                    }).toArray();
                    //console.log(amounts);

                    var total = 0;
                    $.each(amounts, function (key, value) {
                        total += value;
                    });

                    total *= 100;
                    total = ~~total;
                    total = total + "";
                    total = total.substr(0, total.length - 2) + '.' + total.substr(-2);

                    var $total = $(this).find('.mh-total-price');
                    $total.empty().append(total);
                    if (total.split('.')[0] > 0 || total.split('.')[1] > 0)
                        $total.parent().show();
                    else
                        $total.parent().hide();
                });
            },*/

            calcTotalQuantities: function () {
                console.log('calcTotalQuantities()');
                var totalRemainInt = 0;

                _.each(self.data.category.selections, function (selection, i) {
                    var $selection = $('.mh-products[data-placeholder-id=' + selection.placeholderId + ']');
                    var selectedOptions = $selection.find('.mh-product-wrapper > .mh-checkbox:checked')
                    var totalSelectionQuantity = _.reduce($selection.find('.mh-product-wrapper > .mh-checkbox:checked + .mh-product .mh-quantity-input'), function (memo, el) { //console.log(memo, el.value);
                        return memo + parseInt(el.value);
                    }, 0); //console.log('totalSelectionQuantity', totalSelectionQuantity);
                    var remainInt = selection.totalCount - totalSelectionQuantity; //console.log('remainInt', remainInt);

                    // Side-menu
                    var $subBadge = $('.mh-product-categories .mh-active ul li a[data-placeholder-id=' + selection.placeholderId + '] small');
                    if (remainInt > 0) {
                        $subBadge.removeClass('mh-done').find('span').text(remainInt);
                    } else {
                        $subBadge.addClass('mh-done');
                    }

                    // Sticky Footer
                    var $stickyFooter = $selection.find('.mh-sticky-footer');
                    if (remainInt > 0) {
                        $stickyFooter.find('.mh-total-quantity-remain').text(remainInt);
                        $stickyFooter.find('.mh-total-quantity-selected').text(totalSelectionQuantity);
                        $stickyFooter.find('.mh-selections-complete').parent().hide();
                        $stickyFooter.find('.mh-selections-remain').parent().show();
                    } else {
                        if (remainInt == 0)
                            $stickyFooter.find('.mh-quantity-extra').hide();
                        else
                            $stickyFooter.find('.mh-quantity-extra').text('+' + (-remainInt) + ' extra').show();
                        $stickyFooter.find('.mh-selections-complete').parent().show();
                        $stickyFooter.find('.mh-selections-remain').parent().hide();
                    }

                    totalRemainInt += remainInt > 0 ? remainInt : 0; // Don't include negatives
                }); console.log(totalRemainInt);

                // Current Sidebar Category
                var $badge = $('.mh-product-categories .mh-active label > small');
                $badge.find('i').removeAttr('style');
                if (totalRemainInt > 0) {
                    $badge.removeClass('mh-done');//.find('span').text(totalRemainInt);//.show();
                    //$badge.find('.fa-check').hide();
                } else {
                    $badge.addClass('mh-done');
                    //$badge.find('span').hide();
                    //$badge.find('.fa-check').show();
                }
            },

            detailsModal: {
                $el: $('#mh-selection-details'),

                open: function (selectionId, productId) {
                    //console.log('detailsModal.open', categoryId, selectionId, $(event.target));
                    if ($(mh.events.getTarget()).closest('.mh-icons').length) return;

                    //var activeCategory = _.findWhere(self.selections, { id: categoryId }); console.log('activeCategory', activeCategory);
                    self.data.currentSelection = _.findWhere(self.data.category.selections, { placeholderId: selectionId }); //console.log('currentSelection', self.data.currentSelection);
                    self.data.currentOption = _.findWhere(self.data.currentSelection.substitutionOptions, { optionId: productId }); //console.log('currentOption', self.data.currentOption);

                    // Refresh selection data
                    //this.$el.find('[data-id]').val(productId); //self.data.currentOption.optionId);
                    //this.$el.find('[data-placeholder-id]').val(selectionId);
                    this.$el.find('[data-title]').text(self.data.currentOption.optionName);
                    this.$el.find('[data-description]').text(self.data.currentOption.optionDescription || '');
                    if (self.data.currentOption.upgradePrice) this.$el.find('[data-price]').text(self.data.currentOption.upgradePrice.formatMoney('$', 2, ',', '.'));
                    this.$el.find('[data-checkbox]').prop('checked', self.data.currentOption.selectCount ? true : false).attr('data-option-id', productId).attr('data-placeholder-id', selectionId);
                    this.$el.find('[data-quantity]').val(self.data.currentOption.selectCount);
                    //console.log(this.$el.find('[data-id]').val(), self.data.currentSelection.selectCount);

                    // Build Slideshow
                    var $slideshow = this.$el.find('[data-slideshow-images]').empty();
                    if (self.data.currentOption.documents.length) {
                        var photoParams = _.findWhere(self.xhr.actions, { myHomeAction: 'systemDocument' }); //console.log('photoParams', photoParams);
                        if (!photoParams) console.error('No params for MyHomeAction \'document\' loaded into js');
                        _.extend(photoParams, {
                            myHomeInline: 1,
                            myHomeThumb: 0,
                            myHomeCache: 0
                        });  //console.log($.param(photoParams));
                        _.each(self.data.currentOption.documents, function (el, i) { console.log(el);
                            $slideshow.append($('<div><img src="' + self.xhr.url.replace('ajax', 'post') + '/?myHomeDocumentId=' + el.docId + '&' + $.param(photoParams) + '" /></div>'));
                        });
                    } else {
                        $slideshow.append($('<div><div class="mh-no-photo"><i class="fa fa-picture-o"></i>No Photo<br/>Available</div></div>'));
                    }

                    // Toggle Slideshow
                    if (self.data.currentOption.documents.length > 0) this.$el.find('[data-slideshow-images]').removeClass('mh-hide');
                    else this.$el.find('[data-slideshow-images]').addClass('mh-hide');

                    // Toggle Price
                    if (self.data.currentOption.upgradePrice > 0) this.$el.find('.mh-price').show();
                    else this.$el.find('.mh-price').hide();

                    // Toggle Quantity
                    if (self.data.currentOption.quantityRequired) this.$el.find('.mh-quantity').show();
                    else this.$el.find('.mh-quantity').hide();

                    // Disable non-editable
                    this.$el.find('[data-checkbox]').attr('disabled', !self.data.tender.isSelectionsClientEditable);

                    // Open it
                    $.colorbox(_.extend({}, mh.colorbox.options, {
                        href: '#' + this.$el.attr('id'), //'#mh-variation-details',
                        width: '90%',
                        onComplete: function () {
                            if ($slideshow.hasClass('slick-initialized')) {
                                $slideshow.slick('removeSlide', null, null, true);
                                $slideshow.slick('unslick');
                            }
                            $slideshow.slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: true,
                                //fade: true,
                                dots: true
                            });
                        }
                    }));
                },
            }
        }
    });

    var self = mh.tenders.selectionsEdit;

    //////////////////////





    /* StickyFooter */
    var $window = $(window);
    var $stickies = $('.mh-sticky-footer').each(function () {
        var $this = $(this);
        var origBottomPadding;

        $this.checkSticky = function () {
            //console.log('chkSticky: #' + $this.attr('id'));
            $this.$parent = $this.closest('.mh-card');
            $this.params = {
                topLimit: ($this.$parent.offset().top) + $this.outerHeight() + 100,
                btmLimit: ($this.$parent.offset().top + $this.$parent.outerHeight())
            };

            if(!origBottomPadding) origBottomPadding = parseInt($this.$parent.css('padding-bottom'));
            $this.$parent.css('padding-bottom', origBottomPadding + $this.height());

            if (($window.scrollTop() + $window.height()) >= $this.params.topLimit &&
                ($window.scrollTop() + $window.height()) <= $this.params.btmLimit) {
                $this.addClass('is-fixed').css({
                    'opacity': 1,
                    'left': $this.$parent.offset().left + 1,
                    'width': $this.$parent.outerWidth() - 2
                });//.text('within parent: fixed');
            } else {
                $this.removeClass('is-fixed').attr('style', '');//.text('viewport outside parent: absolute');

                if (($window.scrollTop() + $window.height()) <= $this.params.topLimit)
                    $this.css('opacity', 0);
            }
        }
        // check the sticky element on page load
        $this.checkSticky();
        // check on sticky element on page resize
        $window.resize(function () {
            $this.checkSticky();
        });
        // check the sticky element on scrolling
        $window.bind('scroll', function () {
            $this.checkSticky();
        });
    });
});
