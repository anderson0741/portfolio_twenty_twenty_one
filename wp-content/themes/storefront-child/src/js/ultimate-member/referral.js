
export class UMReferral {
  myAccountLabels = null;

  constructor() {
    if (!document.querySelector('body').classList.contains('um-page-user')) { return; }
    this.setupListeners();
  }
  setupListeners() {

    jQuery('#dc-referral-copy-button').on('click', () => {
      var dcReferralLink = document.getElementById("dc-referral-link");
      var dcReferralLink = dcReferralLink.innerText;
      let textToCopy      = dcReferralLink,
          urlInput = document.createElement( "input" );
      document.body.appendChild( urlInput );
      urlInput.setAttribute( "value", textToCopy );
      if ( navigator.userAgent.match( /ipad|ipod|iphone/i ) ) {
          let contentEditable      = urlInput.contentEditable,
              readOnly             = urlInput.readOnly,
              range                = document.createRange(),
              windowSelection      = window.getSelection();
          urlInput.contentEditable = !0;
          urlInput.readOnly        = !1;
          range.selectNodeContents( urlInput );
          windowSelection.removeAllRanges();
          windowSelection.addRange( range );
          urlInput.setSelectionRange( 0, 999999 );
          urlInput.contentEditable = contentEditable;
          urlInput.readOnly        = readOnly
      } else urlInput.select();
      document.execCommand( "copy" );
      document.body.removeChild( urlInput );

      jQuery('.referral-section').css({'transition': 'background-color 0.5s ease-in', 'animation-name': 'temporary', 'animation-duration': '1s'});
      jQuery('#dc-referral-copy-button').css('display', 'none');
    });
  }
}
