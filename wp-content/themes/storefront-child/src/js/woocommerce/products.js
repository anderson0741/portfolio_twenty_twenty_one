import URLSearchParams from '@ungap/url-search-params';

export class WooCommerceProducts {
    filters = null;
    filterPrescription = null;
    filterGroups = null;
    activeFilters = {
        'frame': [],
        'lens-type': [],
        'shape': [],
        'style': [],
        'gender': [],
        'prescription': false,
    };
    offset = 0;
    orderby = 'popularity'
    activeFiltersList = [];
    filterList = null;
    numAvailable = null;
    resetFiltersButton = null;
    products = null;
    ajaxRequest = null;
    productCount = 0;
    featuredCount = 0;
    loadMoreButton = null;
    category = '';
    isGear = false;
    currentPath = window.location.pathname;
    searchString = '';
    featuredProductIDs = [];

    constructor(commonCode = null) {
        if (!(document.querySelector('body').classList.contains('woocommerce') && document.querySelector('body').classList.contains('archive'))) {
            return;
        }
        this.initFilters();
        this.initCategory();
        this.setupListeners();
        this.initSwatches();
    }

    initFilters() {

        this.filters = document.querySelector('.gunnar-filters');
        this.filterPrescription = document.querySelector('#filter-prescription');
        this.filterGroups = document.querySelector('.filter-groups');
        this.filterList = document.querySelector('#active-filter-list');
        this.numAvailable = document.querySelector('.number-available');
        this.resetFiltersButton = document.querySelector('#reset-filters-button');
        if (this.filters) {
            this.filters.querySelectorAll('.filter-group.filter-frame input:checked').forEach((filter) => {
                this.activeFilters['frame'].push(filter.name);
                this.activeFiltersList.push(filter.parentElement.innerText);
            });
            this.filters.querySelectorAll('.filter-group.filter-lens-type input:checked').forEach((filter) => {
                this.activeFilters['lens-type'].push(filter.name);
                this.activeFiltersList.push(filter.parentElement.innerText);
            });
            this.filters.querySelectorAll('.filter-group.filter-shape input:checked').forEach((filter) => {
                this.activeFilters['shape'].push(filter.name);
                this.activeFiltersList.push(filter.parentElement.innerText);
            });
            this.filters.querySelectorAll('.filter-group.filter-style input:checked').forEach((filter) => {
                this.activeFilters['style'].push(filter.name);
                this.activeFiltersList.push(filter.parentElement.innerText);
            });
            this.filters.querySelectorAll('.filter-group.filter-gender input:checked').forEach((filter) => {
                this.activeFilters['gender'].push(filter.name);
                this.activeFiltersList.push(filter.parentElement.innerText);
            });
        }


        if (this.filterList && this.resetFiltersButton) {
            if (this.activeFiltersList.length == 0) {
                this.resetFiltersButton.style.display = 'none';
                this.filterList.innerHTML = '';
            } else {
                this.filterList.innerHTML = '/ ' + this.activeFiltersList.join(' / ');
            }
        }
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('s')) {
            this.searchString = urlParams.get('s');
            this.orderby = 'relevance';
        }

    }

    initCategory() {
        let path = window.location.pathname.split('/');
        if (path.length < 3) {
            return;
        }
        if (path[1] == 'product-category') {
            if (path[2] == 'prescription') {
                this.filterPrescription.checked = true;
                this.activeFilters['prescription'] = true;
                this.category = path[path.length-1] == "" ? path[path.length-2]:path[path.length-1];
            } else if (path[2] == 'gear' || path[2] == 'giftcards') {
                this.isGear = true;
                this.category = path[path.length-1] == "" ? path[path.length-2]:path[path.length-1];
            } else {
                this.category = path[2];
            }
        }
    }

    initSwatches() {
        let disabledList = document.querySelectorAll('a.disabled');

        for (var i = 0; i < disabledList.length; i++) {
            var disabled = disabledList[i]
            disabled.href = '#';
            disabled.parentNode.classList.add('disabled');
        }
    }

    setupListeners() {
        window.addEventListener("popstate", (e) => {
            this.onPopState(e);
        });
        if (this.filters) {
            let filterHeaders = this.filters.querySelectorAll('.filter-header');
            jQuery(filterHeaders).on('click.filter-header', (e, v) => {
                this.openFilter(e);
            });

            this.filters.querySelectorAll('.filter-group input[type=checkbox]').forEach((o) => {
                o.addEventListener('change', (e) => {
                    this.onFilterChange(e);
                });
            });
        }

        if (!this.isGear) {
            jQuery('#available-in-65-35').show();
        }

        let swatches = document.querySelectorAll('.products .swatch-anchor');
        if (swatches) {
            swatches.forEach((o) => {
                o.addEventListener('click', (e) => {
                    this.onSwatchClick(e);
                });
            });
        }

        if (!this.isGear && this.filterPrescription) {
            this.filterPrescription.addEventListener('change', (e) => {
                this.prescriptionChanged(e);
            });
        }

        let orderby = document.querySelector('form.woocommerce-ordering select');
        if (orderby) {
            orderby.addEventListener('change', (e) => {
                this.orderbyChanged(e);
            });
        }

        if (this.resetFiltersButton) {
            this.resetFiltersButton.addEventListener('click', (e) => {
                this.activeFiltersList = [];
                this.activeFilters = {
                    'frame': [],
                    'lens-type': [],
                    'shape': [],
                    'style': [],
                    'gender': [],
                    'prescription': this.activeFilters['prescription'],
                };
                document.querySelectorAll('input:checked').forEach((o) => {
                    if (o.id == 'filter-prescription') {
                        return;
                    }
                    o.checked = false;
                });
                this.rebuildFilter();
            });
        }

        this.products = document.querySelector('ul.products');
        this.loadMoreButton = document.querySelector('#load-more');
        if (this.numAvailable) {
            this.productCount = this.numAvailable.querySelector('.product-count').innerHTML;
        }
        let fCount = document.querySelector('#gunnar-featured-count');
        if (fCount) {
            this.featuredCount = parseInt(fCount.innerHTML);
        }
        if (this.loadMoreButton) {
            if (this.productCount <= (this.offset + 12)) {
                this.loadMoreButton.style.display = 'none';
            } else {
                this.loadMoreButton.style.display = 'inline';
            }
            this.loadMoreButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadMoreProducts();
            });
        }
    }

    onSwatchClick(e) {
        e.preventDefault();
        if (e.currentTarget.parentNode.classList.contains('disabled')) {
            return;
        }

        var imageThumb = jQuery(e.currentTarget).closest('li.product').find('img.attachment-woocommerce_thumbnail');
        jQuery(e.currentTarget).closest('div.select').find('.selected').removeClass('selected');
        e.currentTarget.parentNode.classList.add('selected');

        if (!imageThumb.length || !e.currentTarget.dataset.img) {
            return;
        }
        var src = e.currentTarget.dataset.img;
        var srcset = e.currentTarget.dataset.imgset;
        if (this.activeFilters["lens-type"].length > 0) {
            var hierarchy = false;
            if (this.activeFilters["lens-type"].indexOf("amber-max") != -1) {
                if ("amberMaxImg" in e.currentTarget.dataset) {
                    src = e.currentTarget.dataset["amberMaxImg"];
                    srcset = e.currentTarget.dataset["amberMaxImgset"];
                    hierarchy = true;
                }
            }
            if (this.activeFilters["lens-type"].indexOf("amber") != -1 && !hierarchy) {
                hierarchy = true;
            }
            if (this.activeFilters["lens-type"].indexOf("clear") != -1 && !hierarchy) {
                if ("clearImg" in e.currentTarget.dataset) {
                    src = e.currentTarget.dataset["clearImg"];
                    srcset = e.currentTarget.dataset["clearImgset"];
                    hierarchy = true;
                }
            }
        }
        imageThumb[0].src = src;
        imageThumb[0].srcset = srcset;

        //Update url to link to selected frame color while preserving other query params
        var newFrame = e.currentTarget.href.split('?');
        newFrame = newFrame.length > 1 ? newFrame[1].split('=') : [];
        newFrame = newFrame.length > 1 ? newFrame[1] : '';
        if (newFrame != '') {
            var old = imageThumb[0].parentNode.href.split('?');
            var newUrl = old[0];
            if (old.length <= 1) {
                imageThumb[0].parentNode.href = e.currentTarget.href;
                return;
            }
            newUrl += '?';
            var query_params = old[1].split('&');
            var frame_updated = false;
            for (var x = 0; x < query_params.length; x++) {
                var i = query_params[x]
                if (query_params[i].indexOf("frame-color") != -1) {
                    var query_var = query_params[i].split('=');
                    query_params[i] = query_var[0] + '=' + newFrame;
                    frame_updated = true;
                }
                newUrl += query_params[i];
                if (i + 1 < query_params.length) {
                    newUrl += '&';
                }
            }
            if (!frame_updated) {
                newUrl += 'attribute_frame-color=' + newFrame;
            }

            imageThumb[0].parentNode.href = newUrl;
        }
    }

    onPopState(e) {
        let url = location.search;
        var query = url.substr(1);
        var params = {};
        query.split("&").forEach(function(part) {
            var item = part.split("=");
            params[item[0]] = decodeURIComponent(item[1]);
        });
        if (params['s']) {
            this.searchString = params['s'];
        }

        var urlFilters;
        if (params['pa_frame-material']) {
            urlFilters = params['pa_frame-material'].split(',');
            this.filters.querySelectorAll('.filter-group.filter-frame input[type=checkbox]').forEach((o) => {
                if (urlFilters.indexOf(o.parentElement.innerText) != -1) {
                    o.checked = true;
                } else {
                    o.checked = false;
                }
            });
        }
        //pa_lens-tint=amber&pa_shape=oval&pa_frame-style=full-rim
        if (params['pa_lens-tint']) {
            urlFilters = params['pa_lens-tint'].split(',');
            this.filters.querySelectorAll('.filter-group.filter-lens-type input[type=checkbox]').forEach((o) => {
                if (urlFilters.indexOf(o.parentElement.innerText) != -1) {
                    o.checked = true;
                } else {
                    o.checked = false;
                }
            });
        }

        if (params['pa_shape']) {
            urlFilters = params['pa_shape'].split(',');
            this.filters.querySelectorAll('.filter-group.filter-shape input[type=checkbox]').forEach((o) => {
                if (urlFilters.indexOf(o.parentElement.innerText) != -1) {
                    o.checked = true;
                } else {
                    o.checked = false;
                }
            });
        }

        if (params['pa_frame-style']) {
            urlFilters = params['pa_frame-style'].split(',');
            this.filters.querySelectorAll('.filter-group.filter-style input[type=checkbox]').forEach((o) => {
                if (urlFilters.indexOf(o.parentElement.innerText) != -1) {
                    o.checked = true;
                } else {
                    o.checked = false;
                }
            });
        }

        if (params['pa_gender']) {
            urlFilters = params['pa_gender'].split(',');
            this.filters.querySelectorAll('.filter-group.filter-gender input[type=checkbox]').forEach((o) => {
                if (urlFilters.indexOf(o.parentElement.innerText) != -1) {
                    o.checked = true;
                } else {
                    o.checked = false;
                }
            });
        }

        if ((this.activeFilters['prescription'] && !window.location.pathname.indexOf('prescription') != -1) || (!this.activeFilters['prescription'] && window.location.pathname.indexOf('prescription') != -1)) {
            this.filterPrescription.checked = !this.filterPrescription.checked;
        }
        this.activeFilters = {
            'frame': [],
            'lens-type': [],
            'shape': [],
            'style': [],
            'gender': [],
            'prescription': this.filterPrescription.checked,
        };
        this.activeFiltersList = [];
        this.initFilters();
        this.startSpinner();
        this.makeAjaxRequest();
    }

    orderbyChanged(e) {
        e.preventDefault();
        this.orderby = e.target.value;

        let el = document.querySelector('form.woocommerce-ordering select');
        let elClone = el.cloneNode(true);
        el.parentNode.replaceChild(elClone, el);
        document.querySelector('form.woocommerce-ordering select').value = this.orderby;
        document.querySelector('form.woocommerce-ordering select').addEventListener('change', (e) => {
            this.orderbyChanged(e);
        });
        this.rebuildFilter();
    }

    prescriptionChanged(e) {
        if (e.target.checked) {
            this.activeFilters['prescription'] = true;
            if (this.category == 'computer-eyewear') {
                this.category = 'rx-computer';
                this.currentPath = '/product-category/prescription/rx-computer/';
            } else if (this.category == 'gaming-eyewear') {
                this.category = 'rx-gaming';
                this.currentPath = '/product-category/prescription/rx-gaming/';
            } else if (this.category == 'sunglasses') {
                this.category = 'rx-sunglasses';
                this.currentPath = '/product-category/prescription/rx-sunglasses/';
            } else if (this.category == '' || this.category == 'eyewear') {
                this.category = 'prescription';
                this.currentPath = '/product-category/prescription/';
            }
        } else {
            this.activeFilters['prescription'] = false;
            if (this.category == 'rx-computer') {
                this.category = 'computer-eyewear';
                this.currentPath = '/product-category/computer-eyewear/';
            } else if (this.category == 'rx-gaming') {
                this.category = 'gaming-eyewear';
                this.currentPath = '/product-category/gaming-eyewear/';
            } else if (this.category == 'rx-sunglasses') {
                this.category = 'sunglasses';
                this.currentPath = '/product-category/sunglasses/';
            } else if (this.category == 'prescription') {
                this.category = '';
                this.currentPath = '/shop/';
            }
        }
        this.rebuildFilter();
    }

    openFilter(e) {
        if (e.currentTarget.classList.contains('active')) {
            this.filters.querySelectorAll('.active').forEach((o) => {
                o.classList.remove('active');
            });
            this.filterGroups.classList.remove('has-active');
            return;
        }
        if (!this.filterGroups.classList.contains('has-active')) {
            this.filterGroups.classList.add('has-active');
        }
        this.filters.querySelectorAll('.active').forEach((o) => {
            o.classList.remove('active');
        });

        if (e.currentTarget.classList[1] == undefined) {
            return;
        }
        var targetClass = e.currentTarget.classList[1];
        this.filters.querySelectorAll("." + targetClass).forEach((o) => {
            o.classList.add('active');
        });
    }

    onFilterChange(e) {
        let attribute = this.filters.querySelector('.filter-group.active').classList[1].replace('filter-', '');
        if (attribute == undefined) {
            return;
        }
        if (e.target.checked) {
            if (e.target.name.indexOf('transition') != -1) {
                this.activeFilters[attribute].push(e.target.name.substring(0, e.target.name.indexOf('-')) + '-react');
            }
            this.activeFilters[attribute].push(e.target.name);
            this.activeFiltersList.unshift(e.target.parentElement.innerText);
        } else {
            this.activeFilters[attribute] = this.activeFilters[attribute].filter((ele) => {
                return ele != e.target.name;
            });
            this.activeFiltersList = this.activeFiltersList.filter((ele) => {
                return ele != e.target.parentElement.innerText;
            });
        }
        this.rebuildFilter();
    }

    rebuildFilter(loadMore = false) {
        this.displayFilterList();
        this.startSpinner(loadMore);
        this.makeAjaxRequest(loadMore);
        this.buildNewUrl();
    }

    displayFilterList() {
        if (this.activeFiltersList.length == 0) {
            this.resetFiltersButton.style.display = 'none';
            this.filterList.innerHTML = '';
        } else {
            this.resetFiltersButton.style.display = 'inline';
            this.filterList.innerHTML = '/ ' + this.activeFiltersList.join(' / ');
        }
    }

    startSpinner(loadMore = false) {
        if (loadMore) {
            this.products.innerHTML += '<img class="ajax-spinner" src="/wp-content/themes/gunnars/assets/img/3d-red-glasses.gif"/>';
        } else {
            this.offset = 0;
            this.products.innerHTML = '<img class="ajax-spinner" src="/wp-content/themes/gunnars/assets/img/3d-red-glasses.gif"/>';
        }
        this.numAvailable.querySelector('.product-count').innerHTML = '...';
        this.loadMoreButton.style.display = 'none';
    }

    makeAjaxRequest(loadMore = false) {
        if (this.ajaxRequest != null) {
            this.ajaxRequest.abort();
        }
        var data = {
            'status': 'publish',
            'per_page': 12,
            'offset': this.offset,
            'active_filters': this.activeFilters,
            'include_swatches': 1,
            'include_vto': 1
        };
        if (this.orderby != 'relevance') {
            data['orderby'] = this.orderby;
            if (this.orderby == 'title-desc') {
                data['orderby'] = 'title';
                data['order'] = 'desc';
            } else if (this.orderby == 'title') {
                data['order'] = "asc";
            } else if (this.orderby == 'price-desc') {
                data['orderby'] = 'price';
                data['order'] = 'desc';
            } else if (this.orderby == 'price') {
                data['order'] = "asc";
            }
        } else {
            data['order'] = "asc";
        }
        if (this.category != "") {
            data['product_category'] = this.category;
        }
        if (this.searchString != "") {
            data['search'] = this.searchString;
            if (!this.activeFilters['prescription']) {
                data['exclude_categories'] = ['reading-glasses', 'prescription', 'optical-exclusive', 'rx-computer', 'rx-gaming', 'rx-sunglasses'];
            }
        }
        if (this.orderby == 'popularity') {
            data['featured'] = 1;
            data['org_orderby'] = data['orderby'];
            data['org_order'] = data['order'];
            data['orderby'] = 'date';
            data['order'] = 'desc';
            if (!loadMore) {
                this.featuredProductIDs = [];
            }
            this.ajaxRequest = jQuery.get('/wp-json/wc/v3/products', data, (featuredResponse, featuredStatus, featuredRequest) => {
                for (var x = 0; x < featuredResponse.length; x++) {
                    this.featuredProductIDs.push(featuredResponse[x].id);
                }
                data['per_page'] -= featuredResponse.length;
                var count_only = false;
                if (data['per_page'] < 1) {
                    data['per_page'] = 1;
                    count_only = true;
                }
                data['featured'] = 0;
                let ftotal = loadMore ? this.featuredCount : parseInt(featuredRequest.getResponseHeader('X-WP-Total'));
                if (!loadMore) {
                    this.featuredCount = ftotal;
                }
                data['offset'] = data['offset'] - ftotal >= 0 ? data['offset'] - ftotal : 0;

                data['orderby'] = data['org_orderby'];
                data['order'] = data['org_order'];
                this.ajaxRequest = jQuery.get('/wp-json/wc/v3/products', data, (response, status, request) => {
                    var total = 0;
                    if (ftotal != undefined) {
                        total += ftotal;
                    }
                    let nftotal = parseInt(request.getResponseHeader('X-WP-Total'));
                    if (nftotal != undefined) {
                        total += nftotal;
                    }
                    if (loadMore) {
                        total = this.productCount;
                    }
                    var responses = featuredResponse;
                    if (!count_only) {
                        responses = responses.concat(response);
                    }
                    this.onAjaxSuccess(responses, total, loadMore);
                });
            });
        } else {
            this.ajaxRequest = jQuery.get('/wp-json/wc/v3/products', data, (response, status, request) => {
                let total = parseInt(request.getResponseHeader('X-WP-Total'));
                this.onAjaxSuccess(response, total, loadMore);
            });
        }
    }

    onAjaxSuccess(response, total, loadMore) {
        let reshtml = this.buildProductHTML(response);
        if (loadMore) {
            this.products.removeChild(this.products.querySelector('.ajax-spinner'));
            this.products.innerHTML += reshtml;
        } else {
            this.products.innerHTML = reshtml;
        }
        if (total != undefined && total >= 0) {
            this.productCount = total;
        }
        this.numAvailable.querySelector('.product-count').innerHTML = this.productCount;
        this.loadMoreButton.querySelector('.product-count').innerHTML = this.productCount;
        this.loadMoreButton.querySelector('.product-showing-count').innerHTML = this.offset + 12
        if (this.productCount <= (this.offset + 12)) {
            this.loadMoreButton.style.display = 'none';
        } else {
            this.loadMoreButton.style.display = 'inline';
            this.loadMoreButton.querySelector('.load-more-count').innerHTML = ((this.productCount - (this.offset + 12)) > 12) ? 12 : (this.productCount - (this.offset + 12));
        }
        //Add Click listener for swatches now that there are new swatches on the dom
        let swatches = document.querySelectorAll('.products .swatch-anchor');
        if (swatches) {
            swatches.forEach((o) => {
                o.addEventListener('click', (e) => {
                    this.onSwatchClick(e);
                });
            });
        }
        this.initSwatches();
    }

    buildNewUrl() {
        if (window.history.pushState) {
            var newurl = new URL(window.location.protocol + "//" + window.location.host + this.currentPath);
            // var newurl =  + '?';
            if (this.searchString != '') {
                newurl.searchParams.set('s', this.searchString);
            }
            if (!(this.orderby == 'popularity' || (this.searchString != '' && this.orderby == 'relevance'))) {
                newurl.searchParams.set('orderby', this.orderby);
            } else {
              newurl.searchParams.delete('orderby');
            }
            if (this.activeFilters['frame'].length > 0) {
                var temp = [];
                for (var i = 0; i < this.activeFilters['frame'].length; i++) {
                    var val = this.activeFilters['frame'][i];
                    temp.push(this.filters.querySelector('input[name=' + val + ']').parentElement.innerText);
                }
                newurl.searchParams.set('pa_frame-material', temp);
            } else {
              newurl.searchParams.delete('pa_frame-material');
            }
            if (this.activeFilters['lens-type'].length > 0) {
                var temp = [];
                for (var i = 0; i < this.activeFilters['lens-type'].length; i++) {
                    var val = this.activeFilters['lens-type'][i];
                    temp.push(this.filters.querySelector('input[name=' + val + ']').parentElement.innerText);
                }
                newurl.searchParams.set('pa_lens-tint', temp);
            } else {
              newurl.searchParams.delete('pa_lens-tint');
            }
            if (this.activeFilters['shape'].length > 0) {
                var temp = [];
                for (var i = 0; i < this.activeFilters['shape'].length; i++) {
                    var val = this.activeFilters['shape'][i];
                    temp.push(this.filters.querySelector('input[name=' + val + ']').parentElement.innerText);
                }
                newurl.searchParams.set('pa_shape', temp);
            } else {
              newurl.searchParams.delete('pa_shape');
            }
            if (this.activeFilters['style'].length > 0) {
                var temp = [];
                for (var i = 0; i < this.activeFilters['style'].length; i++) {
                    var val = this.activeFilters['style'][i];
                    temp.push(this.filters.querySelector('input[name=' + val + ']').parentElement.innerText);
                }
                newurl.searchParams.set('pa_frame-style', temp);
            } else {
              newurl.searchParams.delete('pa_frame-style');
            }
            if (this.activeFilters['gender'].length > 0) {
                var temp = [];
                for (var i = 0; i < this.activeFilters['gender'].length; i++) {
                    var val = this.activeFilters['gender'][i];
                    temp.push(this.filters.querySelector('input[name=' + val + ']').parentElement.innerText);
                }
                newurl.searchParams.set('pa_gender', temp);
            } else {
              newurl.searchParams.delete('pa_gender');
            }
            window.history.pushState({
                path: newurl.toString()
            }, '', newurl.toString());
        }
    }

    loadMoreProducts() {
        this.offset += 12;
        this.rebuildFilter(true);
    }

    buildProductHTML(products) {
        if (products.length == 0) {
            return '<div class="no-search-results"><b>No Results For Current Filters:</b> <br/> Try a different filter configuration or <a href="/shop">click here</a> to view all products</div>'
        }
        var ret = '';
        for (var x = 0; x < products.length; x++) {
            ret += '<li class="post-' + products[x].id + ' product type-product status-' + products[x].status;
            if (products[x].images.length > 0) {
                ret += ' has-post-thumbnail';
            }
            for (var y = 0; y < products[x].categories.length; y++) {
                ret += ' product_cat-' + products[x].categories[y].slug;
            }
            if (x == 0) {
                ret += ' first';
            }
            ret += ' ' + products[x].stock_status + ' ' + products[x].tax_status;
            if (products[x].shipping_taxable) {
                ret += ' shipping-taxable'
            }
            if (products[x].purchasable) {
                ret += ' purchasable';
            }
            ret += ' product-type-' + products[x].type;
            ret += '">';
            ret += '<a href="' + products[x].permalink + '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
            ret += '<div class="product-title"><h2 class="woocommerce-loop-product__title">' + products[x].name + '</h2>';
            if (products[x].title_image) {
                ret += products[x].title_image
            }
            ret += '</div>';
            ret += '<div class="gunnar-title-grow"></div>';
            ret += '<span class="price">' + products[x].price_html + '</span>';
            if (products[x].gunnar_vto) {
                ret += products[x].gunnar_vto;
            }
            if (products[x].gunnar_banner) {
                ret += products[x].gunnar_banner;
            }
            if (products[x].images.length > 0) {
                ret += '<img width="300" src="' + products[x].images[0].src + '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="' + products[x].images[0].alt + '" sizes="(max-width: 300px) 100vw, 300px">';
            }
            ret += '</a>'
            ret += '<div class="gunnar-product-options-rating">';
            if (products[x].gunnar_swatches) {
                ret += products[x].gunnar_swatches;
            }
            ret += '</div>';
            ret += '</li>';
        }
        return ret;
    }

}
