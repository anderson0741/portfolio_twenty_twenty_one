import style from '../sass/style.scss';
import { WooCommerceSingleProduct } from './woocommerce/single-product';
import { WooCommerceMyAccount } from './woocommerce/my-account';
import { UMReferral } from './ultimate-member/referral';

var singleProduct  = new WooCommerceSingleProduct();
var myAccount 		 = new WooCommerceMyAccount();
var memberReferral = new UMReferral();

if (!Array.prototype.forEach) {
	Array.prototype.forEach = function (callback, thisArg) {
		thisArg = thisArg || window;
		for (var i = 0; i < this.length; i++) {
			callback.call(thisArg, this[i], i, this);
		}
	};
}
