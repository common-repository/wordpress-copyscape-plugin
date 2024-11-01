<?php
if(current_user_can('check_copyscape'))
{
   $copyScapeResults = copyscape_api_text_search_internet($post->post_content, 'ISO-8859-1');
?>
   <td colspan="<?= $rowSpan ?>">
      <div class="inline-edit-col" style="margin-left: 40px;">
         <?php
         if($copyScapeResults['error'])
         {
            echo $copyScapeResults['error'];
         }
         else
         {
            ?><h4>Copyscape Results</h4><?php
            if($copyScapeResults['count'] == 0)
            {
               ?>No copyscape results found.<?php
            }
            else
            {
               foreach($copyScapeResults['result'] as $result)
               {
                  ?>
                  <div>
                     <a href="<?= $result['url'] ?>"><?= $result['title'] ?></a>
                     <?= $result['htmlsnippet'] ?>
                  </div>
                  <?
               }
               //print_r($copyScapeResults);
            }
         }
         ?>
      </div>
      <p class="submit inline-edit-save">
         <a accesskey="c" href="#inline-edit" title="Ok" class="button-secondary copyscapeOk alignleft">Ok</a>
         <br class="clear"/>
      </p>
   </td>
<?php } ?>