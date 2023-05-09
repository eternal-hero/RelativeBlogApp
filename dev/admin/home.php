<?php
   header("Access-Control-Allow-Origin: *");

   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");

   clearstatcache();

   include("../config.php");
   require '../shopifyclient.php';
   require '../vendor/autoload.php';

   use sandeepshetty\shopify_api;
   if(isset($_REQUEST['shop'])){
   $shop = $_GET['shop'];
   $select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
   $res = mysqli_query($db_obj, $select_sql);
   if (mysqli_num_rows($res) > 0) {
       $res_arr = mysqli_fetch_assoc($res);
       $token = $res_arr['token'];
   }

   $setting_query = mysqli_query($db_obj, "SELECT related_to,no_post_display,handle_rel_post,custom_msg,blogs,show_same_btype,ex_tags,ex_ids,ex_frmpool_id,ex_frmpool_tags,install_status,enable_bimage,image_size,display_desc FROM related_settings WHERE shop='".$shop."'");
   $setting_data = mysqli_fetch_assoc($setting_query);
   if(is_null($setting_data['blogs'])){
     $blogs_arr = [];
   }else{
     $blogs_arr = explode(",",$setting_data['blogs']);
   }
   if(is_null($setting_data['install_status'])){
     $install_status = "";
   }else{
     $install_status = $setting_data['install_status'];
   }




   $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

   if($install_status == '0'){
       $shop_json = $sc->call("GET","/admin/api/".getYear()."/shop.json");
       $shop_owner = explode(" ",$shop_json['shop_owner']);
       $first_name = $shop_owner[0];
       $website_url = $shop_json['domain'];
       $shop_email = $shop_json['email'];

       require 'Drip_API.class.php';

       $params = array(
           "account_id" => "4345124",
           "email" => $shop_email,
           "new_email" => $shop_email,
           "custom_fields" => array(
               "first_name" => $first_name,
               "website_url" => $website_url
           )
       );
       try {
           $drip = new Drip_Api();
           $resultsubscriber = $drip->create_or_update_subscriber($params);
       } catch (Exception $ex) {
       }

       $params = array(
           "account_id" => "4345124",
           "email" => $shop_email,
           "new_email" => $shop_email,
           "action" => "Install Related Blog Posts"
       );
       try {
           $drip = new Drip_Api();
           $resultevent = $drip->record_event($params);
       } catch (Exception $ex) {
       }
   }
   ?>
<html>
   <head>
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="-1">
      <meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
      <link rel="stylesheet" type="text/css" href="assets/css/seaff.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
      <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
      <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
      <script src="assets/bootstrap-tokenfield.js"></script>
      <script src="assets/related-blog-jquery.js"></script>
      <script src="assets/jquery.fancybox.js"></script>
      <link rel="stylesheet" type="text/css" href="assets/jquery.fancybox.css"/>
      <link rel="stylesheet" type="text/css" href="common.css"/>
      <link rel="stylesheet" type="text/css" href="assets/bootstrap-tokenfield.css"/>
      <link rel="stylesheet" type="text/css" href="polaris.css"/>
      <script type="text/javascript">
         ShopifyApp.init({
            apiKey: '<?= SHOPIFY_API_KEY ?>',
            shopOrigin: 'https://<?= $shop ?>'
         });
      </script>
      <script> $.ajaxSetup({cache: false}); </script>
      <title><?php echo APP_NAME; ?></title>
   </head>
   <body>
      <div class="section">
         <div class="section-content">
            <div class="section-row">
               <div class="section-listing">
                  <div class="section-options">
                     <div>
                        <div class="tab">
                           <a href="home.php?shop=<?= $shop; ?>" class="homesett"><button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "home.php") != '') { ?>active<?php } ?>">Settings</button></a>
                           <a href="settings.php?shop=<?= $shop; ?>"><button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "settings.php") != '') { ?>active<?php } ?>">Design</button></a>
                        </div>
                        <div id="setting" class="tabcontent active">
                           <div class="overlay"><img src="assets/loader.svg" alt="Loading..." class="loading"/></div>
                           <div class="alert alert-warning">
                              <ol>
                                 <li>To insert your related blog posts, go to your theme's article.liquid file. Copy-and-paste<br /><mark>{% include 'relatedblogs' %}</mark> after {{ article.content }} OR where you want to display related blog posts. See the official <a href="https://www.digitaldarts.com.au/related-blog-posts-shopify-app?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=support" target="_blank">support page</a> for more help.</li>
                                 <li>Help keep Related Blog Posts free by <a href="https://apps.shopify.com/related-blog-posts?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=review" target="_blank">leaving a review</a>.
                                 </li>
                                 <li>Learn how to grow your Shopify store with Digital Darts free marketing course. <a href="https://www.digitaldarts.com.au?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=course" target="_blank">Sign up for free here</a>.</li>
                              </ol>
                           </div>
                           <div class="alert alert-success hide">
                              <b>Success!</b> Your settings has been saved successfully.
                           </div>
                           <div class="alert alert-danger hide">
                              <b>Oops!</b> Some error occur. Please try later.. OR contact app developer.
                           </div>
                           <div class="Polaris-Card">
                              <div class="Polaris-Card__Section">
                                 <h3 style="color: rgb(0, 0, 0);">Related Settings</h3>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Related to:</label>
                                    <div class="heading-text">
                                       <span>
                                       The "Content, Tags and Author" option <br>often produces the best relevancy.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-Select">
                                       <select id="related_to" class="Polaris-Select__Input">
                                          <option value="0" <?php if($setting_data['related_to'] == 0){echo "selected='selected'";}?>>Content, Tags and Author</option>
                                          <option value="1" <?php if($setting_data['related_to'] == 1){echo "selected='selected'";}?>>Content</option>
                                          <option value="2" <?php if($setting_data['related_to'] == 2){echo "selected='selected'";}?>>Tags</option>
                                          <option value="3" <?php if($setting_data['related_to'] == 3){echo "selected='selected'";}?>>Author</option>
                                       </select>
                                       <div class="Polaris-Select__Icon">
                                          <span class="Polaris-Icon">
                                             <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                             </svg>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <?php
                              if($shop =='product-import-artur.myshopify.com'){
                                  echo $shop;
                              }
                              ?>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Number of related posts to display:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-Select">
                                       <select id="no_post_display" class="Polaris-Select__Input">
                                          <option value="1" <?php if($setting_data['no_post_display'] == 1){echo "selected='selected'";}?>>1</option>
                                          <option value="2" <?php if($setting_data['no_post_display'] == 2){echo "selected='selected'";}?>>2</option>
                                          <option value="3" <?php if($setting_data['no_post_display'] == 3){echo "selected='selected'";}?>>3</option>
                                          <option value="4" <?php if($setting_data['no_post_display'] == 4){echo "selected='selected'";}?>>4</option>
                                          <option value="5" <?php if($setting_data['no_post_display'] == 5){echo "selected='selected'";}?>>5</option>
                                          <option value="6" <?php if($setting_data['no_post_display'] == 6){echo "selected='selected'";}?>>6</option>
                                          <option value="7" <?php if($setting_data['no_post_display'] == 7){echo "selected='selected'";}?>>7</option>
                                          <option value="8" <?php if($setting_data['no_post_display'] == 8){echo "selected='selected'";}?>>8</option>
                                       </select>
                                       <div class="Polaris-Select__Icon">
                                          <span class="Polaris-Icon">
                                             <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                             </svg>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Display the related posts image:</label>
                                    <div class="heading-text">
                                       <span>
                                       Use a thumbnail of the <a class="fancybox" href="assets/example-featured-image.jpg">featured image</a> for<br />each blog post in the related posts.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="enable_bimage">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="enable_bimage" name="enable_bimage" class="Polaris-RadioButton__Input" value="1"
                                                   <?=((isset($setting_data['enable_bimage']) && $setting_data['enable_bimage'] == 1) ? "checked='checked'": "" )?>
                                                   >
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">Yes</span>
                                       </label>
                                    </div>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="disable_bimage">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="disable_bimage" name="enable_bimage" class="Polaris-RadioButton__Input" value="0"
                                                   <?=((isset($setting_data['enable_bimage']) && $setting_data['enable_bimage'] == 0) ? "checked='checked'": "" )?>
                                                   >
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">No</span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Selection of image sizes:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-Select">
                                       <select id="no_image_sizes" class="Polaris-Select__Input">
                                          <?php
                                             $setting_query_sizes = mysqli_query($db_obj, "SELECT * FROM image_sizes");
                                             while($setting_data_sizes = mysqli_fetch_assoc($setting_query_sizes)){
                                             	$sizes = htmlentities($setting_data_sizes['size']);
                                             	$values = htmlentities($setting_data_sizes['value']);
                                             ?>
                                          <option value="<?php echo $sizes;?>" <?php if($setting_data['image_size'] == $sizes){echo "selected='selected'";}?>><?php echo $values;?></option>
                                          <?php
                                             }
                                             ?>
                                       </select>
                                       <div class="Polaris-Select__Icon">
                                          <span class="Polaris-Icon">
                                             <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                             </svg>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Source of the excerpt:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-Select">
                                       <select id="disp_description" class="Polaris-Select__Input">
                                          <option value="Content" <?php if($setting_data['display_desc'] == 'Content'){echo "selected='selected'";}?>>Start of Blog</option>
                                          <option value="Excerpt" <?php if($setting_data['display_desc'] == 'Excerpt'){echo "selected='selected'";}?>>Excerpt</option>
                                       </select>
                                       <div class="Polaris-Select__Icon">
                                          <span class="Polaris-Icon">
                                             <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                             </svg>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>How to handle no related posts:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="blank">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="blank" name="handle_rel_post" class="Polaris-RadioButton__Input" aria-describedby="RadioButton40HelpText" value="0" <?php if($setting_data['handle_rel_post'] == 0){echo "checked='checked'";}?>>
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">Blank</span>
                                       </label>
                                    </div>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="random">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="random" name="handle_rel_post" class="Polaris-RadioButton__Input" aria-describedby="RadioButton40HelpText" value="1"
                                                   <?=((isset($setting_data['handle_rel_post']) && $setting_data['handle_rel_post'] == 1) ? "checked='checked'": "" )?>
                                                   >
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">Random post</span>
                                       </label>
                                    </div>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="custom">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="custom" name="handle_rel_post" class="Polaris-RadioButton__Input" aria-describedby="RadioButton40HelpText" value="2"
                                                   <?=((isset($setting_data['handle_rel_post']) && $setting_data['handle_rel_post'] == 2) ? "checked='checked'": "" )?>
                                                   >
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">Custom message</span>
                                       </label>
                                       <div class="Polaris-TextField">
                                          <input id="custom_msg" value="<?=(isset($setting_data['custom_msg'])  ? $setting_data['custom_msg']  : "" )?>" placeholder="Custom message" class="Polaris-TextField__Input
                                             <?php if($setting_data['handle_rel_post'] != 2){?>hide<?php } ?>"
                                             aria-labelledby="TextField1Label" aria-invalid="false">
                                          <div class="Polaris-TextField__Backdrop"></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Blogs to display on:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation bog_container">
                                    <?php
                                       $select_blog = mysqli_query($db_obj, "SELECT b_title,b_id,b_handle from blog_list where shop='".$shop."'");
                                       if (mysqli_num_rows($select_blog) > 0) {
                                       	while($blog_data = mysqli_fetch_assoc($select_blog)){
                                       	?>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="<?php echo $blog_data['b_handle']; ?>">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-Checkbox">
                                                <input type="checkbox" id="<?php echo $blog_data['b_handle']; ?>" name="blogs" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="<?php echo $blog_data['b_id']; ?>" <?php if(in_array($blog_data['b_id'],$blogs_arr)){echo "checked='checked'";}?> checked='checked'>
                                                <div class="Polaris-Checkbox__Backdrop"></div>
                                                <div class="Polaris-Checkbox__Icon">
                                                   <span class="Polaris-Icon">
                                                      <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20">
                                                         <g fill-rule="evenodd">
                                                            <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                                            <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                                         </g>
                                                      </svg>
                                                   </span>
                                                </div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label"><?php echo $blog_data['b_title']; ?></span>
                                       </label>
                                    </div>
                                    <?php 		}
                                       }  else { ?>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="Checkbox33">
                                       <span class="Polaris-Choice__Label">There is no blogs in your store.</span>
                                       </label>
                                    </div>
                                    <?php
                                       } ?>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div style="margin-top:10px;"class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Show only the same blog type:</label>
                                    <div class="heading-text">
                                       <span>
                                       Your Shopify store can have multiple blogs.<br />
                                       When turned on, related blog posts will only<br />
                                       appear for the same blog. This option is<br />
                                       available if you have multiple blogs setup.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="blog_type_yes">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="blog_type_yes" name="show_same_btype" class="Polaris-RadioButton__Input" aria-describedby="RadioButton40HelpText" value="0" <?php if($setting_data['show_same_btype'] == 0){echo "checked='checked'";}?>>
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">Yes</span>
                                       </label>
                                    </div>
                                    <div class="radio_opt">
                                       <label class="Polaris-Choice" for="blog_type_no">
                                          <span class="Polaris-Choice__Control">
                                             <div class="Polaris-RadioButton">
                                                <input type="radio" id="blog_type_no" name="show_same_btype" class="Polaris-RadioButton__Input" aria-describedby="RadioButton40HelpText" value="1" <?php if($setting_data['show_same_btype'] == 1){echo "checked='checked'";}?>>
                                                <div class="Polaris-RadioButton__Backdrop"></div>
                                                <div class="Polaris-RadioButton__Icon"></div>
                                             </div>
                                          </span>
                                          <span class="Polaris-Choice__Label">No</span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Exclude by tag:</label>
                                    <div class="heading-text">
                                       <span>
                                       Comma-separated list of tags to exclude<br />from having related posts.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation exclude_metas">
                                    <input id="ex_tags" placeholder="Enter tags" value="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <span class="err_div hide">Oops, you've already used this tag.</span>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Exclude by ID:</label>
                                    <div class="heading-text">
                                       <span>
                                       Comma-separated list of blog post IDs to<br />
                                       exclude from having related posts. The ID is the<br />
                                       number inserted at the end of the URL when editing a blog post.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation exclude_metas" >
                                    <input id="ex_ids" placeholder="Enter blog post IDs" value="" class="Polaris-TextField__Input tokenfield" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <span class="err_div hide">Oops, you've already used this id.</span>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Exclude from pool by tag:</label>
                                    <div class="heading-text">
                                       <span>
                                       Comma-separated list of tags to exclude<br />
                                       from appearing as a related post.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation exclude_metas">
                                    <input id="ex_frmpool_tags" placeholder="Enter Tags" value="" class="Polaris-TextField__Input tokenfield" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <span class="err_div hide">Oops, you've already used this id.</span>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Exclude from pool by ID:</label>
                                    <div class="heading-text">
                                       <span>
                                       Comma-separated list of blog post IDs to<br />
                                       exclude from appearing as a related post. <br />The
                                       ID is the number inserted at the end of<br />the URL when editing a blog post.
                                       </span>
                                    </div>
                                 </div>
                                 <div class="Polaris-Layout__Annotation exclude_metas">
                                    <input id="ex_frmpool_id" placeholder="Enter IDs" value="" class="Polaris-TextField__Input tokenfield" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <span class="err_div hide">Oops, you've already used this id.</span>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div style="float:right;" class="Polaris-Layout__AnnotationWrapper">
                                 <button type="button" class="Polaris-Button Polaris-Button--primary btnsave"><span class="Polaris-Button__Content"><span>Save</span></span></button>
                              </div>
                           </div>
                           <div style="border-top: 1px solid #ebeef0; float: left; width: 100%; margin-top: 20px;" class="Polaris-Card">
                              <div style="display: none;">
                                 <div class="Polaris-Card__Section">
                                    <h3 style="color: #000; font-weight: 600;">Cache</h3>
                                    <p style="color: #798c9c;font-size: 13px;margin: 20px 0px;">Cache speeds up the performance of the Related Blog Posts app by reducing database queries when a blog post is viewed. The cache is rebuilt every 24 hours and when you save your related posts settings. You may want to manually clear the cache if you edit a published blog post, edit your blog tags to affect their relatedness, or want blog suggestions to appear immediately for a new blog.</p>
                                 </div>
                                 <div class="Polaris-Layout__AnnotatedSection">
                                    <div class="Polaris-Layout__AnnotationWrapper">
                                       <button type="button" class="Polaris-Button Polaris-Button--primary clear_cache"><span class="Polaris-Button__Content"><span>Clear Cache</span></span></button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="" style="text-align:right">
                              Created by Shopify marketing expert&nbsp;<a href="https://www.digitaldarts.com.au?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=admin" target="_blank">Digital Darts</a>.
                           </div>
                        </div>
                        <script>
                           function openCity(evt, cityName) {
                           	var i, tabcontent, tablinks;
                           	tabcontent = document.getElementsByClassName("tabcontent");
                           	for (i = 0; i < tabcontent.length; i++) {
                           		tabcontent[i].style.display = "none";
                           	}
                           	tablinks = document.getElementsByClassName("tablinks");
                           	for (i = 0; i < tablinks.length; i++) {
                           		tablinks[i].className = tablinks[i].className.replace(" active", "");
                           	}
                           	document.getElementById(cityName).style.display = "block";
                           	evt.currentTarget.className += " active";
                           }
                        </script>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <style>
         .tokenfield{
         border: 1px solid #ccc;
         border-radius: 4px;
         padding: 5px;
         width: 100%;
         min-height: 29px;
         padding-bottom: 0 !important;
         }
         .tokenfield.focus{
         box-shadow: none;
         }
         .tokenfield .token {
         background: #cae9f7 none repeat scroll 0 0;color: #3e89b5;;border:0px;
         }
         .tokenfield .token .close{text-decoration: none;line-height: 25px;font-size: 13px;}
         .err_div{color:red;}
         .tokenfield .token{line-height: 25px;height: 25px;}
         .tokenfield .token .token-label{font-size: 15px;}
      </style>

      <script>
      var shop = "<?php echo $shop;?>";
      var install_status = "<?php echo $install_status;?>";
      var token_1 = "<?=(isset($setting_data['ex_tags'])  ? $setting_data['ex_tags']  : "" )?>";
      var token_2 = "<?=(isset($setting_data['ex_ids'])  ? $setting_data['ex_ids']  : "" )?>";
      var token_3 = "<?=(isset($setting_data['ex_frmpool_id'])  ? $setting_data['ex_frmpool_id']  : "" )?>";
      var token_4 = "<?=(isset($setting_data['ex_frmpool_tags'])  ? $setting_data['ex_frmpool_tags']  : "" )?>";
    </script>

      <script>
      $(document).ready(function() {
          ShopifyApp.Bar.loadingOff();
          generate_taginput($("#ex_tags"), token_1);
          generate_taginput($("#ex_ids"), token_2);
          generate_taginput($("#ex_frmpool_id"), token_3);
          generate_taginput($("#ex_frmpool_tags"), token_4);
          $("input[name='handle_rel_post']").click(function() {
              if ($(this).val() == 2) {
                  $("#custom_msg").removeClass("hide");
              } else {
                  $("#custom_msg").addClass("hide");
              }
          });
          if (install_status == "0") {
              $("#drip_form").submit();
              $(".overlay").show();
              $.ajax({
                  type: "POST",
                  url: "ajax.php",
                  data: {
                      page: 'create_snippest',
                      shop: shop
                  },
                  success: function(data) {
                      $(".overlay").hide();
                      $(".install_status").hide();
                  }
              });
          }
          if ($("input[name='blogs']:checked").length == 1) {
              $("input[name='show_same_btype'][value='1']").prop("checked", true);
              $("input[name='show_same_btype'][value='0']").attr("disabled", "disabled");
          } else if ($("input[name='blogs']:checked").length < 1) {
              $("input[name='show_same_btype']").removeAttr("checked");
              $("input[name='show_same_btype']").attr("disabled", true);
          } else {
              $("input[name='show_same_btype']").removeAttr("disabled");
          }
          $("input[name='blogs']").click(function() {
              if ($("input[name='blogs']:checked").length == 1) {
                  $("input[name='show_same_btype'][value='1']").prop("checked", true).removeAttr("disabled");
                  $("input[name='show_same_btype'][value='0']").attr("disabled", "disabled");
              } else if ($("input[name='blogs']:checked").length < 1) {
                  $("input[name='show_same_btype']").removeAttr("checked");
                  $("input[name='show_same_btype']").attr("disabled", true);
              } else {
                  $("input[name='show_same_btype']").removeAttr("disabled");
              }
          });
          $(".btnsave").click(function() {
              var related_to = $("#related_to option:selected").val();
              var no_post_display = $("#no_post_display option:selected").val();
              var handle_rel_post = $("input[name='handle_rel_post']:checked").val();
              var show_same_btype = $("input[name='show_same_btype']:checked").val();
              var no_image_sizes = $("#no_image_sizes option:selected").val();
              var disp_description = $("#disp_description option:selected").val();
              if (show_same_btype == undefined) {
                  show_same_btype = '1';
              }
              var ex_tags = $("#ex_tags").val();
              var ex_ids = $("#ex_ids").val();
              var ex_frmpool_id = $("#ex_frmpool_id").val();
              var ex_frmpool_tags = $("#ex_frmpool_tags").val();
              var custom_msg = $("#custom_msg").val();
              var blogs = "";
              $("input[name='blogs']:checked").each(function() {
                  blogs += $(this).val() + ",";
              });
              blogs = blogs.slice(0, -1);
              var enable_bimage = $("input[name='enable_bimage']:checked").val();
              $(".overlay").show();
              $("html, body").animate({
                  scrollTop: 0
              }, "slow");
              $.ajax({
                  type: "POST",
                  url: "ajax.php",
                  data: {
                      page: 'related_settings',
                      shop: shop,
                      related_to: related_to,
                      no_post_display: no_post_display,
                      handle_rel_post: handle_rel_post,
                      no_image_sizes: no_image_sizes,
                      show_same_btype: show_same_btype,
                      ex_tags: ex_tags,
                      ex_ids: ex_ids,
                      ex_frmpool_id: ex_frmpool_id,
                      ex_frmpool_tags: ex_frmpool_tags,
                      blogs: blogs,
                      custom_msg: custom_msg,
                      enable_bimage: enable_bimage,
                      disp_description: disp_description
                  },
                  success: function(data) {
                      $(".overlay").hide();
                      if ($.trim(data) == "true") {
                          $(".alert-success").html("Save successful. Your relatedblogs.liquid file is updated.").removeClass("hide");
                          $('.alert-success').show().delay(10000).slideUp();
                      } else {
                          $(".alert-danger").removeClass("hide");
                          $('.alert-danger').show().delay(10000).slideUp();
                      }
                  }
              });
          });
          $(".clear_cache").click(function() {
              $(".overlay").show();
              $("html, body").animate({
                  scrollTop: 0
              }, "slow");
              $.ajax({
                  type: "POST",
                  url: "ajax.php",
                  data: {
                      page: 'clear_cache',
                      shop: shop
                  },
                  dataType: "json",
                  success: function(data) {
                      $(".overlay").hide();
                      if (data['status'] == "true") {
                          $(".bog_container").html(data['data']);
                          $(".alert-success").html("<b>Success!</b> Your cache has been cleared and rebuilt.").removeClass("hide");
                          $('.alert-success').show().delay(5000).slideUp();
                      } else {
                          $(".alert-danger").removeClass("hide");
                          $('.alert-danger').show().delay(5000).slideUp();
                      }
                  }
              });
          });
          $.ajax({
              type: "POST",
              url: "ajax.php",
              data: {page: 'clear_cache',shop: shop},
              dataType: "json",
              success: function(data) {
                  $(".overlay").hide();
                  if (data['status'] == "true") {
                      $(".bog_container").html(data['data']);
                  }else{
                      $(".alert-danger").removeClass("hide");
                      $('.alert-danger').show().delay(5000).slideUp();
                  }
              }
          });
      });

      function generate_taginput(obj, value) {
          $(obj).on('tokenfield:createtoken', function(event) {
              var existingTokens = $(this).tokenfield('getTokens');
              var field = $(this);
              field.parents(".exclude_metas").find(".err_div").addClass("hide");
              $.each(existingTokens, function(index, token) {
                  if (token.value.toLowerCase() === event.attrs.value.toLowerCase()) {
                      field.parents(".exclude_metas").find(".err_div").removeClass("hide");
                      setTimeout(function() {
                          field.parents(".exclude_metas").find(".err_div").addClass("hide");
                      }, 4500);
                      event.preventDefault();
                  }
              });
          }).tokenfield({
              'createTokensOnBlur': true,
              tokens: value
          });
      }
      </script>
   </body>
</html>
<?php }else{
   echo "Empty Shop. Unauthorised Acccess";
   }?>
