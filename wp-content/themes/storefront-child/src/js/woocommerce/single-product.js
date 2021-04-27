export class WooCommerceSingleProduct {
  cartForm = null;
  addToCartButton = null;
  buyFromButton = null;
  addonPopup = null;
  currentVariation = null;
  spinner = null;

  constructor() {
    if (!document.querySelector('body').classList.contains('single-product')) { return; }
    this.initAttributeDescriptions();
    this.setupListeners();
    this.changeAvailabilityLocation();
  }

  initAttributeDescriptions() {
      //Add attribute descriptions if any
      document.querySelectorAll('.attribute-description-info').forEach((info) => {
        let className = info.dataset.arrtibute_name;
        let descriptionInfo = JSON.parse(info.dataset.attribute_descriptions);
        let options = document.querySelector('.' + className).children
        for (var x = 0; x < options.length; x++) {
          var option = options[x]

                if (descriptionInfo[option.firstChild.dataset.value]) {

                    option.innerHTML += ' <a href="#" class="show_attribute_description">(i)</a><div class="attribute_description"><p>' + descriptionInfo[option.firstChild.dataset.value] + '</p></div>';
                }
        }
      });
  }

  changeAvailabilityLocation() {
    if (document.querySelector('.availability_date')) {
      document.querySelector('.single_variation_wrap').prepend(document.querySelector('.availability_date'));
    }
  }

  setupListeners() {
    this.cartForm = document.querySelector('form.cart');
    var lensTintQuestionMark = jQuery('<a href="#lens-description" class="attribute-help">&nbsp;&nbsp;(i)</a>').on('click', function(e) {
      e.preventDefault();
      let target = jQuery(this.getAttribute('href'));

      jQuery('html, body').stop().animate({
         'scrollTop': target.offset().top-jQuery('#masthead').height()
      }, 500, 'swing', function () {});

    });
    jQuery(this.cartForm).find('[for="pa_lens-tint"]').after(lensTintQuestionMark);
    this.setActiveSlideForSingleImage();
    jQuery(this.cartForm).on('found_variation.gunnars-single-product', (e, v) => {
      this.setActiveSlideForSingleImage();
      this.currentVariation = v;
      if (v && v.display_price && this.isHighIndexChecked) { this.onHighIndexChange(jQuery('.wc-pao-addon-field.wc-pao-addon-checkbox[type=checkbox]')); }
      else if (v && v.price_html) { this.updateAddToCartButton(v.price_html); }
      else if (v && v.display_price) { this.updateAddToCartButton('<span class="price">$' + v.display_price.toFixed(2) + '</span>'); }

      // Update our product type links
      var queryString = '';
      // var attributes = Object.entries(v.attributes);
      // for (const [attribute, val] of attributes) {
      for (var x = 0; x < Object.keys(v.attributes).length; x++) {
        var attribute =  Object.keys(v.attributes)[x];
        var val = v.attributes[attribute];
        if (!queryString) {
          queryString = '?';
        } else {
          queryString = queryString + '&';
        }
        queryString = queryString + encodeURIComponent(attribute) + '=' + encodeURIComponent(val);
      }
      jQuery('.gpt-link').each(function() {
        jQuery(this).attr('href', jQuery(this).data('baselink') + queryString);
      });

      this.setupThumbnailScrollArrows()
    });

    //Prevent deselection of attributes
    jQuery('.swatch-anchor').click(function(e){
    	if(e.currentTarget.parentElement.classList.contains('selected')) {
    		e.preventDefault();
    		e.stopPropagation();
        }
    });
    jQuery('.reset_variations').remove();

    //Show attribute description
    jQuery('.show_attribute_description').on('click', (e) => {
        e.preventDefault();
        let description = e.currentTarget.parentElement.querySelector('.attribute_description');
        if (description.classList.contains('show')) {
          description.classList.remove('show');
          e.currentTarget.innerText = '(i)';
        } else {
          description.classList.add('show');
          e.currentTarget.innerText = '(x)';
        }
    });
    jQuery('.show_attribute_description').on('blur', (e) => {
        e.preventDefault();
        let description = e.currentTarget.parentElement.querySelector('.attribute_description');
        description.classList.remove('show');
        e.currentTarget.innerText = '(i)';
    });
    //Change Text on learn more click
    jQuery('.gunnars-addon-expand-description').on('click', (e) => {
        e.preventDefault();
        if (e.currentTarget.innerText == "(Learn More)") {
            e.currentTarget.innerText = "(Hide)";
        } else {
            e.currentTarget.innerText = "(Learn More)";
        }
    });

    jQuery('.wc-pao-addon-field.wc-pao-addon-checkbox[type=checkbox]').on('change', (e) => {
      if (!(this.currentVariation && this.currentVariation.display_price)) return;
      let checkbox = jQuery(e.target);
      this.onHighIndexChange(checkbox);
    });
    this.addToCartButton = this.cartForm.querySelector('button.single_add_to_cart_button');
    jQuery(this.addToCartButton).on('click.gunnars-single-product', function(e) {
      if ( jQuery( this ).is('.disabled') ) {
        var missingAttributes = [];
        jQuery('form.cart').find( '.variations select' ).each(function() {
          var attribute_id = jQuery( this ).attr( 'id' );
          var attribute_name = jQuery('label[for="' + attribute_id + '"]').text();
    			var value = jQuery( this ).val() || '';
          if ( value.length <= 0 ) {
            missingAttributes.push(attribute_name);
          }
        });
        if ( !!missingAttributes.length ) {
          e.preventDefault();
          var message = "You must select from the available options before adding to your cart. " + missingAttributes.join(', ') + (missingAttributes.length > 1 ? ' are ' : ' is ') + "missing, please make a selection.";
          window.alert(message);
          return false;
        }
      }
    });
    this.addonPopup = this.cartForm.querySelector('.gunnars-product-addon-popup');
    this.buyFromButton = this.cartForm.querySelector('button.open-addon-popup-button');
    // Open our addon popup when button is pressed
    if (this.buyFromButton) {
      jQuery(this.buyFromButton).on('click.gunnars-single-product', (e) => {
        this.addonPopup.classList.toggle('show');
        jQuery('body').toggleClass('no-scroll');
      });
      jQuery('.close-addon-popup-button').on('click.gunnars-single-product', (e) => {
        this.addonPopup.classList.toggle('show', false);
        jQuery('body').toggleClass('no-scroll', false);
      });
    }
    // Toggle the addon descriptions
    jQuery(this.cartForm).on('click.gunnars-single-product', '.gunnars-addon-expand-description', function(e) {
      e.preventDefault();
      jQuery(this).closest('.wc-pao-addon').find('.wc-pao-addon-description').toggleClass('show');
    });
    // Toggle the active class
    let arrow = document.querySelector('.filter-out-in-reviews');
    // arrow.addEventListener('click', function(e) {
    //   e.preventDefault();
    //   arrow.classList.toggle('closed');
    //   document.querySelector('.trustpilot-widget').classList.toggle('closed');
    // });
    // Update our visual styles by adding a class to the label of selected radio and checkboxes in our addons popup
    jQuery(this.cartForm).on('change.gunnars-single-product', '.gunnars-addon-type-radiobutton input[type="checkbox"], .gunnars-addon-type-radiobutton input[type="radio"], .gunnars-addon-type-select input[type="checkbox"]', function(e) {
      jQuery(this).closest('label').toggleClass('checked', jQuery(this).is(":checked"));
      jQuery('[name="' + jQuery(this).attr('name') + '"]:not(:checked)').closest('label').removeClass('checked');
    });
    // Update the labels of the selected swatches when appropriate
    jQuery(this.cartForm).on('click.gunnars-single-product', '.select-option', (e) => {
      setTimeout(() => {
        this.updateSwatchSelectionLabels();
      });
    });
    jQuery(this.cartForm).on('bind_calculator', () => {
      setTimeout(() => {
        this.updateSwatchSelectionLabels();
      });
    });
    // Select the default add on radio buttons (if they exist)
    jQuery(".wc-pao-addon").each(function() {
      jQuery(this).find("input[type='radio']:first").each(function() {
        jQuery(this).prop('checked', true);
      });
    });

    jQuery('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:eq(0) .wp-post-image').one('load', ()=> {
      var $image = jQuery( '.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:eq(0) .wp-post-image' );
      setTimeout(() => {
        this.setupThumbnailScrollArrows($image.closest( '.woocommerce-product-gallery__image' ).height());
      }, 100);
    });

  }

  setActiveSlideForSingleImage(){
    let thumbnav = jQuery(".flex-control-nav");
    if (!thumbnav || thumbnav.length == 0) {
      jQuery(".woocommerce-product-gallery__image").addClass("flex-active-slide");
    }
    // let slides = jQuery(".woocommerce-product-gallery__image");
    // if (slides.length == 1) {
    //   slides.addClass(".flex-active-slide");
    // }
  }

  onHighIndexChange(checkbox) {
    this.isHighIndexChecked = checkbox.is(':checked');
    let price = checkbox.is(':checked') ? checkbox.data('price') + this.currentVariation.display_price : this.currentVariation.display_price;
    this.updateAddToCartButton('<span class="price">$' + price.toFixed(2) + '</span>');
  }

  updateAddToCartButton(price_html) {
    if (this.buyFromButton) {
      let currentPrice = this.buyFromButton.querySelector('.price');
      if (currentPrice) {
        this.buyFromButton.removeChild(currentPrice);
      }
      this.buyFromButton.innerHTML += price_html;
    } else if (this.addToCartButton) {
      let currentPrice = this.addToCartButton.querySelector('.price');
      if (currentPrice) {
        this.addToCartButton.removeChild(currentPrice);
      }
      this.addToCartButton.innerHTML += price_html;
    }
  }

  updateSwatchSelectionLabels() {
    jQuery(this.cartForm).find('.select-option').each(function() {
      var $label = jQuery(this).closest('td').find('.swatch-label');
      if (!$label.length) { return; }
      var $newLabel = jQuery(this).closest('tr').find('td.label').find('.selected-value');
      if (!$newLabel.length) {
        $newLabel = jQuery('<span class="selected-value"></span>');
        jQuery(this).closest('tr').find('td.label').append($newLabel);
      }
      $newLabel.html($label.html());
    });
  }

  setupThumbnailScrollArrows(overrideHeight = 0) {
    // Add arrow down if thumbnails are off screen
    var thumbnails = document.querySelector('.flex-control-nav.flex-control-thumbs');
    if (thumbnails.scrollHeight > thumbnails.offsetHeight || (overrideHeight > 0 && thumbnails.scrollHeight > overrideHeight)) {
      var thumbnail_arrows = Array.from(document.querySelectorAll('.thumbnail-arrow'))
      if (thumbnail_arrows.length < 2) {

        var up_arrow_svg = document.createElement("div");
        up_arrow_svg.classList.add("thumbnail-arrow", "up-arrow");
        var down_arrow_svg = document.createElement("div");
        down_arrow_svg.classList.add("thumbnail-arrow", "down-arrow");
        var arrow_svg = '<svg viewBox="0 0 512 512" width="100" height="100"><path d="M368 505.6c-8 0-16-3.2-22.4-8l-240-225.6c-6.4-6.4-9.6-14.4-9.6-24 0-8 3.2-16 9.6-22.4l240-224c12.8-11.2 33.6-11.2 44.8 1.6 12.8 12.8 11.2 32-1.6 44.8l-214.4 201.6 216 203.2c12.8 11.2 12.8 32 0 44.8-4.8 4.8-14.4 8-22.4 8z"></path></svg>';
        up_arrow_svg.innerHTML = arrow_svg;
        down_arrow_svg.innerHTML = arrow_svg;

        thumbnails.parentNode.appendChild(up_arrow_svg);
        thumbnails.parentNode.appendChild(down_arrow_svg);
        thumbnail_arrows = [up_arrow_svg, down_arrow_svg];

        if (thumbnails.scrollTop <= 5) {
          up_arrow_svg.classList.add("hidden");
        }
        if (thumbnails.scrollTop + thumbnails.offsetHeight >= thumbnails.scrollHeight - 5) {
          down_arrow_svg.classList.add("hidden");
        }
        thumbnails.addEventListener("scroll", ()=> {
          if (thumbnails.scrollTop <= 5) {
            up_arrow_svg.classList.add("hidden");
          } else {
            up_arrow_svg.classList.remove("hidden");
          }
          if (thumbnails.scrollTop + thumbnails.offsetHeight >= thumbnails.scrollHeight - 5) {
            down_arrow_svg.classList.add("hidden");
          } else {
            down_arrow_svg.classList.remove("hidden");
          }
        });
      }
      thumbnail_arrows.forEach((item, i) => {
        item.addEventListener("click", (e)=> {
          let scroll = thumbnails.offsetHeight;
          if (e.currentTarget.classList.contains("up-arrow")) {

            scroll = scroll * -1;
          }
          thumbnails.scrollBy({
            top: scroll,
            behavior: 'smooth'
          });
        })
      });
    }
  }
}
