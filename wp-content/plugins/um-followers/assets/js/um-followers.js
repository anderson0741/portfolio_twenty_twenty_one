var um_followers_ajax = false;

jQuery(document).ready(function() {

	var m_users = jQuery('.um-followers-m');

	if ( m_users.length ) {

		m_users.each( function(){
			var max = jQuery(this).attr('data-max');
			if ( max > 0 && jQuery(this).find('.um-followers-m-user').length > max ) {
				var n = max - 1;
				jQuery(this).find('.um-followers-m-user:gt('+n+')').hide();
				var more = jQuery(this).find('.um-followers-m-user').length - jQuery(this).find('.um-followers-m-user:visible').length;
				jQuery('<div class="um-followers-m-user show-all">+'+ more + '</div>').insertAfter( jQuery(this).find('.um-followers-m-user:visible:last') );
			}
		});

		jQuery( document.body ).on('click', '.um-followers-m-user.show-all',function(e){
			e.preventDefault();
			jQuery(this).parents('.um-followers-m').find('.um-followers-m-user').show();
			jQuery(this).hide();
			return false;
		});
	}

	/* Mouse over of following button */
	jQuery( document.body ).on( 'mouseenter', '.um-unfollow-btn', function() {
		if ( ! jQuery(this).hasClass('um_followers_ajax') ) {
			jQuery(this).addClass('um-unfollow-btn2');
			jQuery(this).html( jQuery(this).attr('data-unfollow') );
		}
	});

	/* Mouse out of following button */
	jQuery(document.body).on('mouseleave', '.um-unfollow-btn2', function() {
		if ( ! jQuery(this).hasClass('um_followers_ajax') ) {
			jQuery(this).removeClass('um-unfollow-btn2');
			jQuery(this).html( jQuery(this).attr('data-following') );
		}
	});

	/* Following user */
	jQuery(document.body).on('click', '.um-follow-btn', function(e) {
		e.preventDefault();
		if ( um_followers_ajax === true ) {
			return false;
		}
		um_followers_ajax = true;

		var btn = jQuery(this);
		var member_directory = btn.parents( '.um-members-wrapper' ).length > 0;

		btn.addClass('um_followers_ajax');
		var user_id = btn.data('user_id');

		wp.ajax.send( 'um_followers_follow', {
			data: {
				user_id: user_id,
				nonce: um_scripts.nonce
			},
			success: function( response ) {
				if ( typeof ( response.count ) !== 'undefined' ) {
					if ( member_directory ) {
						btn.parents( '.um-member' ).find('span.um-ajax-count-followers').text( response.count );
					} else {
						jQuery('span.um-ajax-count-followers').text( response.count );
					}
				}

				btn.replaceWith( response.btn );
				um_followers_ajax = false;
			},
			error: function( e ) {
				console.log( e );
				um_followers_ajax = false;
			}
		});
		return false;
	});

	/* Unfollowing user */
	jQuery(document.body).on('click', '.um-unfollow-btn', function(e) {
		e.preventDefault();
		if ( um_followers_ajax === true ) {
			return false;
		}
		um_followers_ajax = true;
		var btn = jQuery(this);
		var member_directory = btn.parents( '.um-members-wrapper' ).length > 0;

		btn.addClass('um_followers_ajax');
		var user_id = btn.data('user_id');

		wp.ajax.send( 'um_followers_unfollow', {
			data: {
				user_id: user_id,
				nonce: um_scripts.nonce
			},
			success: function( response ) {
				if ( typeof ( response.count ) !== 'undefined' ) {
					if ( member_directory ) {
						btn.parents( '.um-member' ).find('span.um-ajax-count-followers').text( response.count );
					} else {
						jQuery('span.um-ajax-count-followers').text( response.count );
					}
				}

				btn.replaceWith( response.btn );
				um_followers_ajax = false;
			},
			error: function( e ) {
				console.log( e );
				um_followers_ajax = false;
			}
		});
		return false;
	});

});