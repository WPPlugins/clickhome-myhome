jQuery(function ($) {
    if (!mh.hasOwnProperty('tenders')) mh.tenders = {};
    _.extend(mh.tenders, {
        packages: {
            data: {
                tender: null,

                categories: [],

                category: null,

                currentPackage: null
            },

            init: function () {
                $('.mh-product-wrapper .mh-loading').hide();
            },

            sync: function () { // console.log('mh.tenders.packages.sync()', self);
                $el = $(mh.events.getTarget());
                $wrapper = $el.closest('.mh-product-wrapper');
                $chkBox = $wrapper.find('.mh-checkbox');

                productId = parseInt($chkBox.data('package-id')); //console.log('productId', productId);

                // Sync original values (if viewing modal)
                $modal = $el.closest('#colorbox');
                if ($modal.length) {
                    $orig = $('.page .mh-products [data-package-id=' + productId + ']').closest('.mh-product-wrapper'); //$modal.find('[data-id]').val());
                    //console.log('isModal', $orig);
                    $orig.find('.mh-checkbox').prop('checked', $chkBox.prop('checked'));
                    //$orig.find('.mh-quantity-input').val($qty.val());
                }

                // Sync Model
                self.data.currentPackage = _.findWhere(self.data.category.packages, { id: productId }); console.log('currentPackage', self.data.currentPackage);
                if (!self.data.currentPackage.allowOtherPackages) { console.log('Dont allow other packages');
                    self.data.category.packages = _.each(self.data.category.packages, function (el) {
                        el.selected = (el.id == self.data.currentPackage.id) ? $chkBox.prop('checked') ? true : false : false;
                        //console.log(el);
                    });
                    $('.page .mh-products-body .mh-product-wrapper input.mh-checkbox:not([data-package-id=' + productId + '])').prop('checked', false);
                } else self.data.currentPackage.selected = $chkBox.prop('checked') ? true : false;

                // Don't sync if invalid data
                if (productId == 'NaN') {
                    console.warn('Invalid package ID');
                    return;
                } else console.log('Syncing...', self.data.currentPackage);

                // Sync Server
                if (self.syncTimeout) window.clearTimeout(this.syncTimeout);
                this.syncTimeout = window.setTimeout(function () {
                    var xhrParams = _.findWhere(self.xhr.actions, { myHomeAction: 'packageEdit' });

                    xhrParams['tenderId'] = self.vars.tenderId;
                    //xhrParams['categoryId'] = self.vars.categoryId;
                    xhrParams['packageId'] = self.data.currentPackage.id;
                    xhrParams['packageSelected'] = self.data.currentPackage.selected;

                    //console.log(xhrParams); return;

                    $.post(self.xhr.url, xhrParams, function (response) {
                        console.log('Updated success', response);
                        $wrapper.find('.mh-loading').hide();
                    }, "json")
                    .fail(function (response, status) { //console.log(response, status);
                        alert("The selection update has failed");
                        $wrapper.find('.mh-loading').hide();
                    });
                    $wrapper.find('.mh-loading').show();
                }, 1000);
            },

            syncTimeout: false,

            detailsModal: {
                $el: $('#mh-package-details'),

                open: function (categoryId, packageId) {
                    //console.log('detailsModal.open', categoryId, selectionId, $(mh.events.getTarget()));

                    //var selectedCategory = _.findWhere(self.categories, { Id: categoryId }); console.log('selectedCategory', selectedCategory);
                    self.data.currentPackage = _.findWhere(self.data.category.packages, { id: packageId }); console.log('currentPackage', self.data.currentPackage);

                    // Refresh selection data
                   // this.$el.find('[data-id]').val(self.data.currentPackage.id);
                    this.$el.find('[data-title]').text(self.data.currentPackage.name);
                    this.$el.find('[data-description]').text(self.data.currentPackage.description);
                    this.$el.find('[data-price]').text(self.data.currentPackage.sellPrice);
                    this.$el.find('[data-checkbox]').prop('checked', self.data.currentPackage.selected ? true : false).attr('data-package-id', self.data.currentPackage.id);
                    //this.$el.find('[data-quantity]').val(selection.count);
                    //console.log(this.$el.find('[data-id]').val(), selection.count);

                    // Build Slideshow
                    var $slideshow = this.$el.find('[data-slideshow-images]').empty();
                    if (self.data.currentPackage.imageIds.length) {
                        var photoParams = _.findWhere(self.xhr.actions, { myHomeAction: 'systemDocument' }); //console.log('photoParams', photoParams);
                        if (!photoParams) console.error('No params for MyHomeAction \'document\' loaded into js');
                        $.extend(photoParams, {
                            myHomeInline: 1,
                            myHomeThumb: 0,
                            myHomeCache: 0
                        });  //console.log($.param(photoParams));
                        _.each(self.data.currentPackage.imageIds, function (el, i) { //console.log(photoParams, $);
                            $slideshow.append($('<div><img src="' + self.xhr.url.replace('ajax', 'post') + '/?myHomeDocumentId=' + el + '&' + $.param(photoParams) + '" /></div>'));
                        });
                    } else {
                        $slideshow.append($('<div><div class="mh-no-photo"><i class="fa fa-picture-o"></i>No Photo<br/>Available</div></div>'));
                    }

                    // Toggle Slideshow
                    if (self.data.currentPackage.imageIds.length > 0) this.$el.find('[data-slideshow-images]').removeClass('mh-hide');
                    else this.$el.find('[data-slideshow-images]').addClass('mh-hide');

                    // Toggle Price
                    if (self.data.currentPackage.sellPrice > 0) this.$el.find('.mh-price').removeClass('mh-hide');
                    else this.$el.find('.mh-price').addClass('mh-hide');

                    // Disable non-editable
                    this.$el.find('[data-checkbox]').attr('disabled', !self.data.tender.isPackagesClientEditable);

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
                        },
                        onCleanup: function () {
                            //delete self.data.currentPackage;
                        }
                    })); //.on('afterChange', mh.colorbox.resize());
                },
            }
        }
    });
    var self = mh.tenders.packages;
});
