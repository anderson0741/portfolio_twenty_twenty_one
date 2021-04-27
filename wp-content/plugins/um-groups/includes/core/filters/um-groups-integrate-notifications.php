<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add UM: Notifications logs
 * @param array $logs
 *
 * @return array
 */
function um_groups_notifications_core_log_types( $logs ) {

	$logs['groups_approve_member'] = array(
		'title'         => __( 'Groups - Approve Member', 'um-groups' ),
		'account_desc'  => __( 'When my group requests have been approved', 'um-groups' ),
	);

	$logs['groups_join_request'] = array(
		'title'         => __( 'Groups - Join Request', 'um-groups' ),
		'account_desc'  => __( 'When a user requested to join their group', 'um-groups' ),
	);

	$logs['groups_invite_member'] = array(
		'title'         => __( 'Groups - Invite Member', 'um-groups' ),
		'account_desc'  => __( 'When a member has invited to join a group', 'um-groups' ),
	);

	$logs['groups_change_role'] = array(
		'title'         => __( 'Groups - Change Group Role', 'um-groups' ),
		'account_desc'  => __( 'When my group roles have been changed', 'um-groups' ),
	);

	return $logs;
}
add_filter( 'um_notifications_core_log_types', 'um_groups_notifications_core_log_types', 200, 1 );


/**
 * Add notification icon
 *
 * @param $output
 * @param $type
 *
 * @return string
 */
function um_groups_add_notification_icon( $output, $type ) {
	if ( in_array( $type, array( 'groups_approve_member' , 'groups_join_request', 'groups_invite_member', 'groups_change_role' ) ) ) {
		$output = '<i class="um-faicon-users" style="color: #3ba1da"></i>';
	}

	return $output;
}
add_filter('um_notifications_get_icon', 'um_groups_add_notification_icon', 10, 2 );