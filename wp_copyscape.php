<?php
/*
Plugin Name: Wordpress Copyscape Plugin
Plugin URI: http://www.domainsuperstar.com
Description: Manage Copyscape API
Version: .2
Author: John Gadbois
Author URI: http://www.johngadbois.com
*/

include('classes/copyscape_premium_api.php');
include('inc/copyscape_ajax.php');

add_action('admin_menu', 'copyscapeoptions_add_page_fn');
// Add sub page to the Settings Menu
function copyscapeoptions_add_page_fn() {
	add_options_page('Copyscape', 'Copyscape', 'administrator', __FILE__, 'options_page_fn');
}

function options_page_fn() {
?>
   <div class="wrap">
      <div class="icon32" id="icon-options-general"><br></div>
      <h2>Copyscape Options</h2>
      Enter your copyscape username and api key to use the copyscape plugin.
      <form action="options.php" method="post">
         <?php settings_fields('copyscape_options'); ?>
         <?php do_settings_sections(__FILE__); ?>
         <p class="submit">
            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
         </p>
      </form>
      <?php

         $results = copyscape_api_check_balance();
         if (!$results['error'] && get_option('copyscape_options'))
         {
            ?>
            <h3>Copyscape Account</h3>
            <table class="form-table">
               <tr><th>Account Value</th><td>$<?= $results['value'] ?></td></tr>
               <tr><th>Credits Available</th><td><?= $results['total'] ?></td></tr>
            </table>
            <?php
         }
         else
         {
            ?><?php echo $results['error'] ?><?php
         }
      ?>
   </div>
<?php
}

add_action('admin_init', 'copyscapesoptions_init_fn' );
// Register our settings. Add the settings section, and settings fields
function copyscapesoptions_init_fn(){
   register_setting('copyscape_options', 'copyscape_options', 'copyscape_options_validate' );
   add_settings_section('main_section', 'Copyscape Settings', 'section_text_fn', __FILE__);
   add_settings_field('copyscape_username', 'Copyscape Username', 'setting_username_fn', __FILE__, 'main_section');
   add_settings_field('copyscape_password', 'Copyscape API Key', 'setting_api_key_fn', __FILE__, 'main_section');
   add_settings_field('min_user_level', 'Lowest Role that can check Copyscape', 'setting_role_fn', __FILE__, 'main_section');
   
   // add custom capability depending on settings
   $options = get_option('copyscape_options');
   $min_role = $options ? $options['role'] : 'administrator' ;
   $roles = array('Administrator'=>'administrator', 'Editor'=>'editor', 'Author'=>'author', 'Contributor'=>'contributor');

   foreach($roles as $role=>$val)
   {
      $role = get_role($val);
      $role->add_cap( 'check_copyscape' );

      if($val == $min_role)
         break;
   }
}

function section_text_fn()
{
   echo '<p>Enter your copyscape information.</p>';
}

function copyscape_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	return $input; // return validated input
}

function setting_role_fn() {
   $options = get_option('copyscape_options');
   $items = array('Administrator'=>'administrator', 'Editor'=>'editor', 'Author'=>'author', 'Contributor'=>'contributor');

   echo "<select id='copyscape_role' name='copyscape_options[role]'>";

   foreach($items as $item=>$value)
   {
      $selected = ($options['role']== $value ) ? 'selected="selected"' : '';
      echo "<option value='$value' $selected>$item</option>";
   }

   echo "</select>";
}

function setting_username_fn() {
   $options = get_option('copyscape_options');
   echo "<input id='plugin_text_string' name='copyscape_options[username]' size='40' type='text' value='{$options['username']}' />";
}

function setting_api_key_fn() {
   $options = get_option('copyscape_options');
   echo "<input id='plugin_text_string' name='copyscape_options[api_key]' size='40' type='text' value='{$options['api_key']}' />";
}

/* Set up meta box */
add_filter('page_row_actions', 'add_copyscape_link_to_row');
add_filter('post_row_actions', 'add_copyscape_link_to_row');

function add_copyscape_link_to_row($links)
{
   if(current_user_can('check_copyscape'))
   {
      $links['copyscape'] = "<a href='javascript:' class='checkCopyscape'>Check Copyscape</a>";
   }

   return $links;
}
?>