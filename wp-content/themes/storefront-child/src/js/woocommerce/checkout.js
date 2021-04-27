import * as FloatLabels from 'float-labels.js/src/float-labels';

export class WooCommerceCheckout {
  checkoutForm = null;
  checkoutFloatLabels = null;
  billingAutocomplete = null;
  billingAddressField = null;
  billingCityField = null;
  billingPostCodeField = null;
  billingCountryField = null;
  billingStateField = null;
  shippingAutocomplete = null;
  shippingAddressField = null;
  shippingCityField = null;
  shippingPostCodeField = null;
  shippingCountryField = null;
  shippingStateField = null;

  constructor() {
    if (!document.querySelector('body').classList.contains('woocommerce-checkout')) { return; } // PRESCRIPTION UPLOAD CHANGE: CHANGED CONDISITON BY BILL
    this.setupListeners();
    this.displayBillingAddressFields();
    this.displayShippingAddressFields();
  }

  setupListeners() {
    this.checkoutForm = document.querySelector('form.checkout, form.prescription-collection-form'); // PRESCRIPTION UPLOAD CHANGE: SECOND SELECTOR ADDED BY BILL

    //Dirty Hack to prevent multiple optionals on the billing_address_2 line.  Added by Colton, approved by Bill.
    jQuery(document.body).on('country_to_state_changing', function(){
      let billing_address_2 = jQuery('#billing_address_2');
      if ( !billing_address_2.data('gunnarchanged') || billing_address_2.data('gunnarchanged') < 2 ) {
        let i = billing_address_2.data('gunnarchanged') || 0;
        billing_address_2.data('gunnarchanged', (i+1) );
        return;
      }
      let placeholder = billing_address_2.data('placeholder').replace(/\(optional\)/gi, '');
      jQuery('label[for=billing_address_2]').html( placeholder );
      billing_address_2.attr('placeholder', placeholder );
    });
    //End

    if (!jQuery('label[for=cc-expire-year]').length) {
      jQuery('#cc-expire-year').before('<label for="cc-expire-year">Year</label>');
    }
    this.checkoutFloatLabels = new FloatLabels( this.checkoutForm, {
        style: 2,
        customLabel: ( labelEl, el ) => {
          if (el.id == 'paypal_pro_payflow-card-cvc') {
            return 'CVC';
          }
          return labelEl.textContent;
        },
    });

    // Terrible hack to enable apple pay on checkout screen
    var watch_for_apple_pay = () => {
      setTimeout(() => {
        if (!window.wc_braintree_credit_card_apple_pay_handler || window.wc_braintree_credit_card_apple_pay_handler.buttons != '.sv-wc-apply-pay-checkout') {
          watch_for_apple_pay();
          return;
        }
        window.wc_braintree_credit_card_apple_pay_handler.reset_payment_request();
        //jQuery('body').trigger('updated_checkout');
      }, 500);
    };
    jQuery(() => {
      watch_for_apple_pay();
    });
    // END: Terrible hack to enable apple pay on checkout screen

    jQuery('body').on( 'updated_checkout', () => {
      if (!jQuery('label[for=cc-expire-year]').length) {
        jQuery('#cc-expire-year').before('<label for="cc-expire-year">Year</label>');
      }
      this.checkoutFloatLabels.init_();
    });

    window.addEventListener('message', function finalizeCallback(event) {
      if (event.origin.indexOf('fitmetrix') > -1 && event.data === 'finalized') {
        var pdMeasureCustomerValue = jQuery('.measure-pd-link').data('gunnar-pd-measure');
        var parent = jQuery('#gunnars-prescription-pupillary-distance-instructions').parent();
        parent.empty();
        parent.append(jQuery('<input type="hidden" value="' + pdMeasureCustomerValue + '" name="gunnars-pd-fitmetrix-customer-value">'))
        parent.append(jQuery('<p id="gunnars-prescription-pupillary-distance-instructions">Your PD has been measured. Click <a target="_blank" href="#" class="measure-pd-link" data-gunnar-pd-measure="' + pdMeasureCustomerValue + '">here to remeasure your PD</a></p>'))
      }
    });

    this.billingAddressField = this.checkoutForm.querySelector('#billing_address_1');
    this.billingCityField = this.checkoutForm.querySelector('#billing_city');
    this.billingPostCodeField = this.checkoutForm.querySelector('#billing_postcode');
    this.billingCountryField = this.checkoutForm.querySelector('#billing_country');
    this.billingStateField = this.checkoutForm.querySelector('#billing_state');
    this.shippingAddressField = this.checkoutForm.querySelector('#shipping_address_1');
    this.shippingCityField = this.checkoutForm.querySelector('#shipping_city');
    this.shippingPostCodeField = this.checkoutForm.querySelector('#shipping_postcode');
    this.shippingCountryField = this.checkoutForm.querySelector('#shipping_country');
    this.shippingStateField = this.checkoutForm.querySelector('#shipping_state');
    window.initCheckoutAutocomplete = () => {
      this.setupBillingForm();
      this.setupShippingForm();
    };

    this.checkoutForm.addEventListener('change', (e) => {
      if (e.target.id == 'gunnars-prescription-method-upload' || e.target.id == 'gunnars-prescription-method-email') {
        let visible = this.checkoutForm.querySelector('.prescription-option.visible');
        if (visible) {
          visible.classList.remove('visible');
        }
        if (e.target.checked) {
          let newVisible = this.checkoutForm.querySelector('.prescription-option[data-option=' + e.target.value + ']');
          if (newVisible) {
            newVisible.classList.add('visible');
          }
        }
      } else if (e.target.id == 'gunnars-prescription-upload') {
        let file = e.target.files[0];
        if (!file) {
          e.target.parentElement.querySelector('span').innerHTML = "Click Here To Select File";
          this.checkoutForm.querySelector('#gunnars-prescription-upload-file-contents').value = '';
          this.checkoutForm.querySelector('#gunnars-prescription-upload-file-name').value = '';
        } else {
          if (file.size > 10000000) {
            window.alert('File is too large, must be less than 10MB, please select a smaller file!');
            e.target.value = '';
            e.target.parentElement.querySelector('span').innerHTML = "Click Here To Select File";
            this.checkoutForm.querySelector('#gunnars-prescription-upload-file-contents').value = '';
            this.checkoutForm.querySelector('#gunnars-prescription-upload-file-name').value = '';
            return;
          } else if (!/\.(jpe?g|png|gif|tiff|pdf)$/i.test(file.name)) {
            window.alert('File must be an image or PDF!');
            e.target.value = '';
            e.target.parentElement.querySelector('span').innerHTML = "Click Here To Select File";
            this.checkoutForm.querySelector('#gunnars-prescription-upload-file-contents').value = '';
            this.checkoutForm.querySelector('#gunnars-prescription-upload-file-name').value = '';
            return;
          }
          e.target.parentElement.querySelector('span').innerHTML = file.name;
          this.checkoutForm.querySelector('#gunnars-prescription-upload-file-name').value = file.name;
          this.checkoutForm.querySelector('#gunnars-prescription-upload-file-contents').value = '';
          var reader = new FileReader();
          reader.addEventListener('load', () => {
            this.checkoutForm.querySelector('#gunnars-prescription-upload-file-contents').value = reader.result;
          });
          reader.readAsDataURL(file);
        }
      } else if (e.target.id == 'billing_country') {
        setTimeout(() => {
          this.checkoutFloatLabels.rebuild();
          this.displayAddressFields();
        });
      }
    });
    this.checkoutForm.addEventListener('click', (e) => {
      if (e.target.id == "gunnars-prescription-upload-trigger" || e.target.parentElement.id == 'gunnars-prescription-upload-trigger') {
        let uploadInput = this.checkoutForm.querySelector('#gunnars-prescription-upload');
        if (uploadInput) {
          uploadInput.click();
        }
      }
    });
    if (this.billingAddressField) { // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
      this.billingAddressField.addEventListener('blur', () => {
        this.displayBillingAddressFields();
      });
    }
    if (this.shippingAddressField) { // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
      this.shippingAddressField.addEventListener('blur', () => {
        this.displayShippingAddressFields();
      });
    }
  }

  displayBillingAddressFields() {
    if (!this.billingAddressField) return; // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
    if (!!this.billingAddressField.value && !!this.billingAddressField.value.trim()) {
      this.checkoutForm.querySelector('#billing_city_field').classList.remove('hide');
      this.checkoutForm.querySelector('#billing_state_field').classList.remove('hide');
      this.checkoutForm.querySelector('#billing_postcode_field').classList.remove('hide');
    } else {
      this.checkoutForm.querySelector('#billing_city_field').classList.add('hide');
      this.checkoutForm.querySelector('#billing_state_field').classList.add('hide');
      this.checkoutForm.querySelector('#billing_postcode_field').classList.add('hide');
    }
  }

  setupBillingForm() {
    if (!this.billingAddressField) return; // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
    this.billingAutocomplete = new google.maps.places.Autocomplete(
      this.billingAddressField,
      {
        types: ['geocode']
      }
    );
    this.billingAutocomplete.addListener('place_changed', () => {
      let place = this.billingAutocomplete.getPlace();
      this.billingAddressField.value = '';
      this.billingCityField.value = '';
      this.billingPostCodeField.value = '';
      this.billingStateField.value = '';
      place.address_components.forEach((c) => {
        var addressType = c.types[0];
        if (addressType == "street_number") {
          this.billingAddressField.value = this.billingAddressField.value ? (c.short_name + ' ' + this.billingAddressField.value) : c.short_name;
        } else if (addressType == "route") {
          this.billingAddressField.value = this.billingAddressField.value ? (this.billingAddressField.value + ' ' + c.long_name) : c.long_name;
        } else if (addressType == "locality") {
          this.billingCityField.value = c.long_name;
        } else if (addressType == "postal_code") {
          this.billingPostCodeField.value = c.short_name;
        } else if (addressType == "administrative_area_level_1") {
          this.billingStateField.value = c.short_name;
        } else if (addressType == "country") {
          this.billingCountryField.value = c.short_name;
        }
      });
      this.checkoutFloatLabels.rebuild();
      jQuery(this.checkoutForm).trigger( 'update' );
    });
    this.billingAddressField.addEventListener('focus', () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          var circle = new google.maps.Circle({
            center: geolocation,
            radius: position.coords.accuracy
          });
          this.billingAutocomplete.setBounds(circle.getBounds());
        });
      }
    });
  }

  displayShippingAddressFields() {
    if (!this.shippingAddressField) return; // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
    if (!!this.shippingAddressField.value && !!this.shippingAddressField.value.trim()) {
      this.checkoutForm.querySelector('#shipping_city_field').classList.remove('hide');
      this.checkoutForm.querySelector('#shipping_state_field').classList.remove('hide');
      this.checkoutForm.querySelector('#shipping_postcode_field').classList.remove('hide');
    } else {
      this.checkoutForm.querySelector('#shipping_city_field').classList.add('hide');
      this.checkoutForm.querySelector('#shipping_state_field').classList.add('hide');
      this.checkoutForm.querySelector('#shipping_postcode_field').classList.add('hide');
    }
  }

  setupShippingForm() {
    if (!this.shippingAddressField) return; // PRESCRIPTION UPLOAD CHANGE: IF ADDED BY BILL
    this.shippingAutocomplete = new google.maps.places.Autocomplete(
      this.shippingAddressField,
      {
        types: ['geocode']
      }
    );
    this.shippingAutocomplete.addListener('place_changed', () => {
      let place = this.shippingAutocomplete.getPlace();
      this.shippingAddressField.value = '';
      this.shippingCityField.value = '';
      place.address_components.forEach((c) => {
        var addressType = c.types[0];
        if (addressType == "street_number") {
          this.shippingAddressField.value = this.shippingAddressField.value ? (c.short_name + ' ' + this.shippingAddressField.value) : c.short_name;
        } else if (addressType == "route") {
          this.shippingAddressField.value = this.shippingAddressField.value ? (this.shippingAddressField.value + ' ' + c.long_name) : c.long_name;
        } else if (addressType == "locality") {
          this.shippingCityField.value = c.long_name;
        } else if (addressType == "postal_code") {
          this.shippingPostCodeField.value = c.short_name;
        } else if (addressType == "administrative_area_level_1") {
          this.shippingStateField.value = c.short_name;
        } else if (addressType == "country") {
          this.shippingCountryField.value = c.short_name;
        }
      });
      this.checkoutFloatLabels.rebuild();
      jQuery(this.checkoutForm).trigger( 'update' );
    });
    this.shippingAddressField.addEventListener('focus', () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          var circle = new google.maps.Circle({
            center: geolocation,
            radius: position.coords.accuracy
          });
          this.shippingAutocomplete.setBounds(circle.getBounds());
        });
      }
    });
  }
}
