export class WooCommerceCart {

  constructor() {
    if (!document.querySelector('body').classList.contains('woocommerce-cart')) { return; }
    this.setupListeners();

  }

  setupListeners() {
    jQuery('.coupon-drop-down a').click((e) => {
      e.preventDefault();
      jQuery('.coupon-drop-down a svg').toggleClass('flip')
      jQuery('.woocommerce-coupon-form .coupon').toggleClass('active')
    });
  }

}
