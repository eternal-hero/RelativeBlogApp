<?php
header("Access-Control-Allow-Origin: *");
header('Content-Security-Policy:frame-ancestors https://'. $_GET['shop'] .' https://admin.shopify.com',false);
include("../config.php");
require '../shopifyclient.php';
require '../vendor/autoload.php';

use sandeepshetty\shopify_api;

$shop = $_REQUEST['shop'];
$select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
   //print_r($select_sql);die;
   $res = mysqli_query($db_obj, $select_sql);
   if (mysqli_num_rows($res) > 0) {
      $res_arr = mysqli_fetch_assoc($res);
      $token = $res_arr['token'];
      //print_r($token);die;
   }

$setting_query = mysqli_query($db_obj, "SELECT top_text,html_before_list,html_after_list,html_before_post,html_after_post,no_char_post,custom_style,select_theme,read_more_theme_2 FROM html_settings WHERE shop='" . $shop . "'");
$setting_data = mysqli_fetch_assoc($setting_query);
//print_r($setting_data);die;
   $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

   $response = $sc->call("GET", "/admin/api/" . getYear() . "/themes.json");
   $themeId ='';
   foreach($response as $key =>  $value)
   {
      if ($value['role']=='main')
      {
         $themeId = $value;
      }
   }
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
                            <div>
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
                                   </a>
                                    <a href="instructions.php?shop=<?=$shop; ?>&host=<?=$_REQUEST['host']?>">
                                    <button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "instructions.php") != '') { ?>active<?php } ?>">
                                    Instructions 
                                    </button>
                                    </a>
                                </div>
                                <div id="setting" class="tabcontent active" style="width:100%">
                                    <div class="overlay"><img src="assets/loader.svg" alt="Loading..." class="loading" /></div>
                                        <div class="alert alert-warning" style="line-height: 27px;">
                                            
                                            <b>Instructions for 2.0 theme:</b>
                                            
                                            <ol>
                                              <li><span><i class="fa-solid fa-check"></i></span> Enable the app extension  <a href="https://<?php echo $shop ;?>/admin/themes/<?php echo $themeId['id'];?>/editor?context=apps" target="_blank">here</a>.
                                                </li>
                                              <li><span><i class="fa-solid fa-check"></i></span> On the same page for your theme customization, select a sample blog post that you want the block to be added.</li>

                                                <li><span><i class="fa-solid fa-check"></i></span> On the left menu, click 'Sections'.</li>
                                                <li><span><i class="fa-solid fa-check"></i></span> Click 'Add block' then the 'Related Blog Posts' block.</li>
                                                <li><span><i class="fa-solid fa-check"></i></span> Drag-and-drop the block in your desired position.</li>
                                                
                                                <li><span><i class="fa-solid fa-check"></i></span> Click 'Save'.</li>    
                                                <!-- <li><span><i class="fa-solid fa-check"></i></span> Select your 'R-Blog-Post' block.</li>  
                                                <li><span><i class="fa-solid fa-check"></i></span> Drag and drop the block in the position that you wish to have.</li>   
                                                <li><span><i class="fa-solid fa-check"></i></span> Click 'Save'.</li>  -->
                                                
                                            </ol>
                                            
                                            <b>Instructions for 1.0 theme:</b>
                                            <ol>    
                                                <li>To insert your related blog posts, go to your theme's in Sections folder and edit article-template.liquid file. Copy-and-paste<br><mark>{% include 'relatedblogs' %}</mark> after {{ article.content }} OR where you want to display related blog posts. See the official <a href="https://www.digitaldarts.com.au/related-blog-posts-shopify-app?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=support" target="_blank">support page</a> for more help.</li>
                                            </ol>    
            
                                            <b>What to do next:</b>
                                            <ol>
                                                <li>We appreciate you <a href="https://apps.shopify.com/related-blog-posts?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=review" target="_blank">leaving a review</a>.
                                                </li>
                                                <li>Learn how to grow your Shopify store with Digital Darts free marketing course. <a href="https://www.digitaldarts.com.au?utm_source=shopify&utm_medium=app&utm_campaign=relatedposts&utm_content=course" target="_blank">Sign up for free here</a>.</li>
                                                    
                                                
                                            </ol>
                                        </div>
                                    </div> 
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

      window.GenerateSessionToken = function() {
         var AppBridgeUtils = window['app-bridge-utils'];
         const sessionToken = AppBridgeUtils.getSessionToken(window.app);
         sessionToken.then(function(result) {
            $.ajaxSetup({
               headers: {
                  "Authorization": "Bearer " + result
               }
            });
            window.sessionToken = result;
         }, function(err) {
            // console.log(err); // Error: "It broke"
         });
      }

      window.ShowErrorToast = function(msg) {
         var Toast = window.ShopifyApp.Toast;
         const toastError = Toast.create(window.ShopifyApp.App, {
            message: msg,
            duration: 5000,
            isError: true
         });
         toastError.dispatch(Toast.Action.SHOW);
      }
      window.ShowSuccesToast = function(msg) {
         var Toast = window.ShopifyApp.Toast;
         const toastError = Toast.create(window.ShopifyApp.App, {
            message: msg,
            duration: 5000
         });
         toastError.dispatch(Toast.Action.SHOW);
      }
      window.LoadingOff = function() {
         var Loading = window.ShopifyApp.Loading
         const loading = Loading.create(window.ShopifyApp.App);
         loading.dispatch(Loading.Action.STOP);
      }


      head.ready("shopifyAppBridgeUtils", function() {
         var shopName = _configs.shop;
         var token = '';

         function initializeApp() {
            var shop_name = "<?php echo base64_decode($_REQUEST['host']);?>";
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
      head.load({
         shopifyAppBridge: "https://unpkg.com/@shopify/app-bridge"
      }, {
         shopifyAppBridgeUtils: "https:////unpkg.com/@shopify/app-bridge-utils"
      });
   </script>
        <script>
      $(document).ready(function() {
         var interval = setInterval(doStuff, 2000);
         $(".overlay").show();

         function doStuff() {
            if (window.sessionToken) {
               clearInterval(interval);
               //window.DoActions();
               window.LoadingOff();
            }
            $(".overlay").hide();
         }
         
      });
   </script>

    </body>
</html>                 