<?php
   header("Access-Control-Allow-Origin: *");
   header('Content-Security-Policy:frame-ancestors https://'. $_GET['shop'] .' https://admin.shopify.com',false);
   include("../config.php");
   require '../shopifyclient.php';
   require '../vendor/autoload.php';

   use sandeepshetty\shopify_api;

   $shop = $_REQUEST['shop'];
   $_SESSION['shop'] = $shop;

   $setting_query = mysqli_query($db_obj, "SELECT top_text,html_before_list,html_after_list,html_before_post,html_after_post,no_char_post,custom_style,select_theme,read_more_theme_2 FROM html_settings WHERE shop='".$shop."'");
   $setting_data = mysqli_fetch_assoc($setting_query);
   ?>
<html>
   <head>
      <link rel="stylesheet" type="text/css" href="assets/css/seaff.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
      <script src="assets/related-blog-jquery.js"></script>
      <script src="assets/jquery.fancybox.js"></script>
      <link rel="stylesheet" type="text/css" href="assets/jquery.fancybox.css"/>
      <link rel="stylesheet" type="text/css" href="common.css"/>
      <link rel="stylesheet" type="text/css" href="polaris.css"/>
      <script src="https:////cdnjs.cloudflare.com/ajax/libs/headjs/1.0.3/head.load.min.js" type="text/javascript"></script>

      <title><?php echo APP_NAME; ?></title>
   </head>
   <body>
      <div class="section">
         <div class="section-content">
            <div class="section-row">
               <div class="section-listing">
                  <div class="section-options">
                    <div class="tab">
                       <a href="home.php?shop=<?=$shop;?>&host=<?=$_REQUEST['host']?>" class="homesett">
                         <button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "home.php") != '') { ?>active<?php } ?>">
                           Settings
                         </button>
                       </a>
                       <a href="settings.php?shop=<?=$shop; ?>&host=<?=$_REQUEST['host']?>">
                         <button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "settings.php") != '') { ?>active<?php } ?>">
                           Design
                         </button>
                         <a href="instructions.php?shop=<?=$shop; ?>&host=<?=$_REQUEST['host']?>">
                          <button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "instructions.php") != '') { ?>active<?php } ?>">
                          Instructions 
                          </button>
                        </a>
                       </a>
                    </div>
                     <div id="design" class="tabcontent <?php if (strstr($_SERVER['PHP_SELF'], "settings.php") != '') { ?>active<?php } ?>">
                        <div class="overlay"><img src="assets/loader.svg" alt="Loading..." class="loading"/></div>
                        <div class="alert alert-success hide">
                           <b>Success!</b> Your settings has been saved successfully.
                        </div>
                        <div class="alert alert-danger hide">
                           <b>Oops!</b> Some error occur. Please try later.. OR contact app developer.
                        </div>
                        <div class="Polaris-Card">
                           <div class="Polaris-Card__Section">
                              <h3 style="color: rgb(0, 0, 0);">HTML Settings</h3>
                           </div>
                        </div>
                        <div class="Polaris-Layout__AnnotatedSection">
                           <div class="Polaris-Layout__AnnotationWrapper">
                              <div class="Polaris-Layout__Annotation align-right">
                                 <label>Top text:</label>
                                 <div class="heading-text">
                                    <span>
                                    HTML is allowed.
                                    </span>
                                 </div>
                              </div>
                              <div class="Polaris-Layout__Annotation">
                                 <div class="Polaris-TextField">
                                    <input id="top_text" value="<?php echo htmlentities($setting_data['top_text'], ENT_QUOTES); ?>" placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="Polaris-Layout__AnnotatedSection">
                           <div class="Polaris-Layout__AnnotationWrapper">
                              <div class="Polaris-Layout__Annotation align-right">
                                 <label>Selection of Blog Design Theme:</label>
                              </div>
                              <div class="Polaris-Layout__Annotation">
                                 <div class="radio_opt">
                                    <label class="Polaris-Choice" for="theme_sel_theme1">
                                       <span class="Polaris-Choice__Control">
                                          <div class="Polaris-RadioButton">
                                             <input type="radio" id="theme_sel_theme1" name="themesel" class="Polaris-RadioButton__Input" value="theme_1" <?php if($setting_data['select_theme'] == 'theme_1'){echo "checked='checked'";}?> checked='checked'>
                                             <div class="Polaris-RadioButton__Backdrop"></div>
                                             <div class="Polaris-RadioButton__Icon"></div>
                                          </div>
                                       </span>
                                       <span class="Polaris-Choice__Label"><a class="fancybox" href="assets/theme_1_pre.jpg" title="Theme 1 Preview">Theme 1</a></span>
                                    </label>
                                 </div>
                                 <div class="radio_opt">
                                    <label class="Polaris-Choice" for="theme_sel_theme2">
                                       <span class="Polaris-Choice__Control">
                                          <div class="Polaris-RadioButton">
                                             <input type="radio" id="theme_sel_theme2" name="themesel" class="Polaris-RadioButton__Input" value="theme_2" <?php if($setting_data['select_theme'] == 'theme_2'){echo "checked='checked'";}?>>
                                             <div class="Polaris-RadioButton__Backdrop"></div>
                                             <div class="Polaris-RadioButton__Icon"></div>
                                          </div>
                                       </span>
                                       <span class="Polaris-Choice__Label"><a class="fancybox" href="assets/theme_2_pre_fin.jpg" title="Theme 2 Preview">Theme 2</a></span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="theme_1_html" <?php if($setting_data['select_theme'] == 'theme_2'){echo 'style="display: none;"';}?>>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>HTML to use before the list:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-TextField">
                                       <input id="html_before_list" value='<?php echo htmlentities($setting_data['html_before_list'], ENT_QUOTES); ?>' placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>HTML to use before each related post:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-TextField">
                                       <input id="html_before_post" value="<?php echo htmlentities($setting_data['html_before_post'], ENT_QUOTES); ?>" placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>HTML to use after each related post:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-TextField">
                                       <input id="html_after_post" value="<?php echo htmlentities($setting_data['html_after_post'], ENT_QUOTES); ?>" placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>HTML to use after the list:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-TextField">
                                       <input id="html_after_list" value="<?php echo htmlentities($setting_data['html_after_list'], ENT_QUOTES); ?>" placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="theme_2_html" <?php if($setting_data['select_theme'] == 'theme_1'){echo 'style="display: none;"';} else if($setting_data['select_theme'] == ''){echo 'style="display: none;" tsttst';}?>>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div class="Polaris-Layout__AnnotationWrapper">
                                 <div class="Polaris-Layout__Annotation align-right">
                                    <label>Read more button text:</label>
                                 </div>
                                 <div class="Polaris-Layout__Annotation">
                                    <div class="Polaris-TextField">
                                       <input id="read_more_theme_2" value="<?php if($setting_data['read_more_theme_2'] == ''){echo 'Read More';} else{echo $setting_data['read_more_theme_2'];} ?>" placeholder="" class="Polaris-TextField__Input" aria-labelledby="TextField1Label" aria-invalid="false">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="Polaris-Layout__AnnotatedSection">
                           <div class="Polaris-Layout__AnnotationWrapper">
                              <div class="Polaris-Layout__Annotation align-right">
                                 <label>Number of characters displayed per related post:</label>
                                 <div class="heading-text">
                                    <span>
                                    Set to "0" to just display the title of the blog post.
                                    </span>
                                 </div>
                              </div>
                              <div class="Polaris-Layout__Annotation">
                                 <div class="Polaris-TextField">
                                    <input id="no_char_post" value="<?php echo $setting_data['no_char_post']; ?>" placeholder="" class="Polaris-TextField__Input integer" aria-labelledby="TextField1Label" aria-invalid="false">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="Polaris-Card">
                           <div class="Polaris-Card__Section">
                              <h3 style="color: rgb(0, 0, 0);">Custom Style</h3>
                              <p style="color: #798c9c;font-size: 14px;margin: 20px 0px;">The Related Blog Posts app by default uses the style of your Shopify theme. You can edit the design of related blog posts with CSS of your Shopify theme or apply custom CSS below. Work with your designer for assistance. This option is for advanced users only.</p>
                              <textarea rows="10" style="height:auto;" id="custom_style"><?php echo htmlentities($setting_data['custom_style'], ENT_QUOTES); ?></textarea>
                           </div>
                           <div class="Polaris-Layout__AnnotatedSection">
                              <div style="float:right;" class="Polaris-Layout__AnnotationWrapper">
                                 <button type="button" class="Polaris-Button Polaris-Button--primary btnsave" style="margin-bottom: 20px;"><span class="Polaris-Button__Content"><span>Save</span></span></button>
                              </div>
                           </div>
                        </div>
                        <div class="" style="text-align:right">
                           Created by Shopify marketing expert&nbsp;<a href="https://www.digitaldarts.com.au?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=admin" target="_blank">Digital Darts</a>.
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script type="text/javascript">
         var shop = "<?php echo $shop;?>";
         var _configs = {
             apiKey: '<?=SHOPIFY_API_KEY?>',
             shop: '<?=$shop?>',
         }
         <?php if(isset($_REQUEST['host'])){ ?>
            _configs['host'] = '<?=$_REQUEST['host']?>';
        <?php  } ?>

         window.GenerateSessionToken = function(){
           var AppBridgeUtils = window['app-bridge-utils'];
           const sessionToken = AppBridgeUtils.getSessionToken(window.app);
           sessionToken.then(function(result) {
             $.ajaxSetup({
               headers: { "Authorization": "Bearer " + result }
             });
             window.sessionToken = result;
           }, function(err) {
               // console.log(err); // Error: "It broke"
           });
         }

         window.ShowErrorToast = function(msg){
           var Toast = window.ShopifyApp.Toast;
           const toastError = Toast.create(window.ShopifyApp.App, {message: msg,duration: 5000,isError: true});
           toastError.dispatch(Toast.Action.SHOW);
         }
         window.ShowSuccesToast = function(msg){
           var Toast = window.ShopifyApp.Toast;
           const toastError = Toast.create(window.ShopifyApp.App, {message: msg,duration: 5000});
           toastError.dispatch(Toast.Action.SHOW);
         }
         window.LoadingOff = function(){
           var Loading = window.ShopifyApp.Loading
           const loading = Loading.create(window.ShopifyApp.App);
           loading.dispatch(Loading.Action.STOP);
         }


         head.ready("shopifyAppBridgeUtils", function(){
             var shopName = _configs.shop;
             var token = '';
             function initializeApp() {
               var shop_name = "<?php echo base64_decode($_REQUEST['host']);?>"
                  if(shop_name)
               {
                var app = createApp({
                  apiKey: _configs.apiKey,
                  shopOrigin: shop_name
                  
               });
               }else{
                  var app = createApp({
                  apiKey: _configs.apiKey,
                  shopOrigin: shopName
                  
               });
               }
               window.app = app;
               window.GenerateSessionToken();
               return app;
             }

             var AppBridge = window['app-bridge'];
             var AppBridgeUtils = window['app-bridge-utils'];
             var createApp = AppBridge.default;
             var actions = AppBridge.actions;
             window.ShopifyApp = {
               App: initializeApp(),
               ShopOrigin: _configs.shop,
               ResourcePicker: actions.ResourcePicker,
               Toast: actions.Toast,
               Button: actions.Button,
               TitleBar: actions.TitleBar,
               Modal: actions.Modal,
               Redirect: actions.Redirect,
               Loading: actions.Loading,
             };
             var ShopifyApp = window.ShopifyApp;
           });
         head.load({shopifyAppBridge: "https://unpkg.com/@shopify/app-bridge"},{shopifyAppBridgeUtils: "https:////unpkg.com/@shopify/app-bridge-utils"});
      </script>
      <script>
         $(document).ready(function() {
             var interval = setInterval(doStuff, 2000);
             $(".overlay").show();
             function doStuff() {
                 if (window.sessionToken) {
                     clearInterval(interval);
                    window.DoActions();
                    window.LoadingOff();
                 }
             }

             window.ClearCache = function(){
               $.ajax({
                   type: "POST",
                   url: "ajax.php",
                   data: {page: 'clear_cache',shop: shop},
                   dataType: "json",
                   success: function(data) {
                       $(".overlay").hide();
                       if (data['status'] == "true") {
                           $(".bog_container").html(data['data']);
                       } else {
                           if(data.code == 201){
                             window.ShowErrorToast(data.msg);
                             window.GenerateSessionToken();
                           }
                           $(".alert-danger").removeClass("hide");
                           $('.alert-danger').show().delay(5000).slideUp();
                       }
                   }
               });
             }
             window.DoActions = function() {
                 $(".btnsave").click(function() {
                     var top_text = $("#top_text").val();
                     var html_before_list = $("#html_before_list").val();
                     var html_before_post = $("#html_before_post").val();
                     var html_after_post = $("#html_after_post").val();
                     var html_after_list = $("#html_after_list").val();
                     var no_char_post = $("#no_char_post").val();
                     var custom_style = $("#custom_style").val();
                     var select_theme = $("input[name='themesel']:checked").val();
                     var read_more_theme_2 = $("#read_more_theme_2").val();
                     $(".overlay").show();
                     $("html, body").animate({
                         scrollTop: 0
                     }, "slow");
                     $.ajax({
                         type: "POST",
                         url: "ajax.php",
                         data: {page: 'html_settings',shop: shop,top_text: top_text,html_before_list: html_before_list,html_before_post: html_before_post,html_after_post: html_after_post,html_after_list: html_after_list,no_char_post: no_char_post,custom_style: custom_style,select_theme: select_theme,read_more_theme_2: read_more_theme_2},
                         dataType: "json",
                         success: function(data) {
                           $(".overlay").hide();
                           if(data.code == 201){
                             window.ShowErrorToast(data.msg);
                             window.GenerateSessionToken();
                           }else{
                             if(data.code == 100){
                               $(".alert-danger").removeClass("hide");
                               $('.alert-danger').show().delay(10000).slideUp();
                             }else{
                               $(".alert-success").html("Save successful. Your relatedblogs.liquid file is updated.").removeClass("hide");
                               $('.alert-success').show().delay(10000).slideUp();
                             }
                           }
                         }
                     });
                 });
                 $(".integer").keypress(function(e) {
                     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                         return false;
                     }
                 });

                 window.ClearCache();
                 $("input[name='themesel']").bind('change', function() {
                     var theme_selected = $("input[name='themesel']:checked").val();
                     if (theme_selected == 'theme_2') {
                         $('.theme_1_html').hide();
                         $('.theme_2_html').show();
                     } else {
                         $('.theme_1_html').show();
                         $('.theme_2_html').hide();
                     }
                 });
             }
           });
      </script>
   </body>
</html>
