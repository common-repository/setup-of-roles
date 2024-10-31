<?php
/*
Plugin Name: Setup of roles
Plugin URI:
Description: "Setup of roles" very simple plug-in for setup of roles and capabilities of users.
						 Allows to add and delete a role, and also to change capabilities of roles.
Version: 0.0.1
Author: Eugene Yudin
Author URI:
*/
global $wp_roles;

function default_list_caps() {
	global $wp_roles, $wp_version;
	$back_caps = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5',
										 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
	$list_caps = array();
	if ($wp_version[0] >= 2) {
		foreach ( $wp_roles->role_objects as $role_obj ) {
			$list_caps = array_merge($list_caps, $role_obj->capabilities);
		}
		return array_diff(array_keys($list_caps), $back_caps);
	} else {
		return $back_caps;
	}
}

function set_caps( $all_caps ) {
	global $wp_roles;
	$caps = array();
	foreach ( $all_caps as $cap ) {
		foreach ( $wp_roles->role_objects as $role_obj ) {
			$caps[$cap][$role_obj->name] = ( isset($role_obj->capabilities[$cap]) 
																				AND $role_obj->capabilities[$cap] ) ? '1' : '0';
		}
	}
	return $caps;
}

$all_caps = default_list_caps();
$capabilities = set_caps( $all_caps );

if ( isset($_POST['set_role_caps']) ) {
	foreach ( $all_caps as $cap ) {
		foreach ( $wp_roles->role_objects as $role_obj ) {
			if ( isset($_POST['capout'][$cap][$role_obj->name]) ) {
				if ( $capabilities[$cap][$role_obj->name] == '0' ) {
					if ( $role_obj->name != 'administrator' ) {
						$wp_roles->add_cap( $role_obj->name, $cap );
						$capabilities[$cap][$role_obj->name] = '1';
					}
				}
			} else {
				if ( $capabilities[$cap][$role_obj->name] == '1' ) {
					if ( $role_obj->name != 'administrator' ) {
						$wp_roles->remove_cap( $role_obj->name, $cap );
						$capabilities[$cap][$role_obj->name] = '0';
					}
				}
			}
		}
	}
	echo '<div class="updated"><p>' . __('Success! Your changes were successfully saved!', 'setup-of-roles').'</p></div>';
}

if ( isset($_POST['add_role']) AND $_POST['new_role'] ) {
	if ( array_key_exists($_POST['new_role'], $wp_roles->roles) ) {
		echo '<div class="error"><p>'.sprintf(__('The role "%s" already exists.', 'setup-of-roles'), $_POST['new_role']).'</p></div>';
	} else { 
		if ( add_role( $_POST['new_role'], $_POST['new_role_display'] ? $_POST['new_role_display'] : $_POST['new_role'] ) ) {
			$capabilities = set_caps( $all_caps );
			echo '<div class="updated"><p>'.__('Success! The new role was successfully added!', 'setup-of-roles') . '</p></div>';
		}	
	}
}

if ( isset($_POST['del_role']) ) {
	if ( $_POST['del_role'] == 'administrator' ) {
		echo '<div class="error"><p>'.__('The role the role "administrator" can\'t be deleted.', 'setup-of-roles') . '</p></div>';
	} else { 
		remove_role( $_POST['del_role'] );
		$capabilities = set_caps( $all_caps );
		echo '<div class="updated"><p>'.__('Success! The role was successfully deleted!', 'setup-of-roles').'</p></div>';
	}
} ?>

<h2><?php _e( 'Setup of roles', 'setup-of-roles' ); ?></h2>
<form method="post" >
	<label id="new-role"><?php _e( 'New role: ', 'setup-of-roles' ); ?>
		<input type="text" name="new_role" pattern="^[a-z][a-z0-9]{4,}"
			title=<?php echo '"'.__( 'the name of a role shall consist of 5 or more lowercase Latin letters and digits', 'setup-of-roles' ).'"'; ?> />
	</label>
	<label id="new-role-display"><?php _e( 'Display name: ', 'setup-of-roles' ); ?>
		<input type="text" name="new_role_display" pattern="^[a-zA-Z][a-zA-Z0-9]{4,}"
			title=<?php echo '"'.__( 'the displayed name shall consist of 5 or more Latin letters and digits', 'setup-of-roles' ).'"'; ?> />
	</label>
	<input id="add-role" type="submit" name="add_role"
				 value=<?php echo '"'.__('Add role', 'setup-of-roles').'"'; ?> class="button-primary" />
</form>
<hr class="up-caps"/>
<table class="table-caps">
	<form method="post" >
	<tr>
		<th class="th-roles"></th>
		<?php foreach ( $wp_roles->role_names as $role_name => $role_name_display ) { ?>
			<th class="th-roles"><?php echo $role_name_display; ?>
				<button type="submit" name="del_role" value=<?php echo '"'.$role_name.'"'; ?>
					onClick=<?php echo '"return confirm(\''.__( 'Are you sure you want to delete this role?', 'setup-of-roles' ).'\');"'; ?>>X</button>
			</th>
		<?php } ?>
	</tr>
	</form>
	<form id="caps" method="post" >
	<?php foreach ( $capabilities as $cap => $roles ) { ?>
		<tr>
			<td class="cap"><?php echo $cap; ?></td>
			<?php foreach ( array_keys($roles) as $role_name ) { ?>
			<td class="cap-check">
				<input type="checkbox" <?php echo "name=capout[{$cap}][{$role_name}] value='1' ".
					($capabilities[$cap][$role_name] ? 'checked' : ''); ?> />
			</td>
			<?php } ?>
		</tr>
	<?php }	?>
	<tr>
		<td class="tr-cap-end"></td>
		<?php foreach ( array_keys($roles) as $role_name ) { ?><td class="tr-cap-end"></td><?php } ?>
	</tr>
	</form>
</table>
<input id="submit-caps" form="caps" type="submit" name="set_role_caps"
			 value=<?php echo '"'.__('Submit', 'setup-of-roles').'"'; ?> class="button-primary" />