<?php
add_action('admin_head', 'copyscape_javascript');

function copyscape_javascript() {
?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
   $('.checkCopyscape').live('click', function(event) {

      event.preventDefault();

      var row = $(this).closest("tr");

      var data = {
         action: 'check_copyscape',
         rowId: row.attr('id'),
         tdCount: row.find('td').size() + 1
      };

      jQuery.post(ajaxurl, data, function(response) {
         if(response)
         {
            row.data('oldHtml', row.html());
            row.html(response);
            row.addClass('inline-edit-row');
         }
         else
         {

         }
      });
      return false;
   });

   $('.copyscapeOk').live('click', function() {
      var row = $(this).closest("tr");
      row.removeClass('inline-edit-row');
      row.html(row.data('oldHtml'));
   });
});
</script>
<?php
}

add_action('wp_ajax_check_copyscape', 'copyscape_ajax_check');

function copyscape_ajax_check()
{
   $id   = $_POST['rowId'];
   $id   = explode('-', $id);
   $id   = $id[1];
   $rowSpan = $_POST['tdCount'];
   
   $post = get_post($id);

   include(dirname(__FILE__) . '/copyscape_check.php');
   //echo "IN HERE";
   die();
}
?>