export class WooCommerceCommon {

  constructor() {
    if (!(document.querySelector('body').classList.contains('woocommerce') || document.querySelector('body').classList.contains('page-id-200073') || document.querySelector('body').classList.contains('page-id-200064') || document.querySelector('body').classList.contains('page-id-196033') || document.querySelector('body').classList.contains('page-id-200202') || document.querySelector('body').classList.contains('page-id-200078'))) { return; }
    if (document.querySelector('body').classList.contains('woocommerce') && document.querySelector('body').classList.contains('archive')) { return; }
    this.setupListeners();
  }

  setupListeners() {
    this.initSwatches();
    let swatches = document.querySelectorAll('.products .swatch-anchor');
    if (swatches) {
      swatches.forEach((o) => {
        o.addEventListener('click', (e) => {
          this.onSwatchClick(e);
        });
      });
    }
  }

  initSwatches() {
    let disabledList = document.querySelectorAll('a.disabled');

    for (var i = 0; i < disabledList.length; i++) {
      var disabled = disabledList[i];
      disabled.href = '#';
      disabled.parentNode.classList.add('disabled');
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
    imageThumb[0].src = e.currentTarget.dataset.img;
    imageThumb[0].srcset = e.currentTarget.dataset.imgset;

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
            var i = query_params[x];
            if (query_params[i].indexOf("frame-color") != -1) {
                var query_var = query_params[i].split('=');
                query_params[i] = query_var[0] + '=' + newFrame;
                frame_updated = true;
            }
            newUrl += query_params[i];
            if (i+1 < query_params.length) {
                newUrl += '&';
            }
        }
        if (!frame_updated) {
            newUrl += 'attribute_frame-color=' + newFrame;
        }

        imageThumb[0].parentNode.href = newUrl;
    }
  }

}
