<?php
include '../db_login_detail.php';
$rootDir = $_SERVER["DOCUMENT_ROOT"];
include  $rootDir."/relatedposts/shopifyclient.php";
include  $rootDir."/relatedposts/vendor/autoload.php";
use sandeepshetty\shopify_api;


if(isset($_POST['data'])){
  $data = json_decode($_POST['data']);
  // print_r($data);
  // exit;
  $shop = $data->shop;
  $token = $data->token;
  $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
   try {
      $themes = $sc->call('GET', '/admin/api/2020-01/themes.json');
      $active_theme_arr = loopAndFind($themes, 'role', 'main');
      $active_theme_id = $active_theme_arr[0]['id'];
      $relatedBlog = $sc->call('GET', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json?asset[key]=snippets/relatedblogs.liquid");
      $checkString = '<script src="//www.digitaldarts.com.au/relatedposts/timestamp.php?shop='.$shop.'"></script>';
      if (strpos($relatedBlog['value'], $checkString) !== false) {
        $newString = str_replace($checkString, '', $relatedBlog['value']);
        $assets_arr1 = ["asset" => ["key" => "snippets/relatedblogs.liquid","value" => $newString]];
        $sc->call('DELETE', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json?asset[key]=snippets/relatedblogs.liquid");
        $resp1 = $sc->call('PUT', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json", $assets_arr1);
      }
      $checkString2 = '<script src="//www.digitaldarts.com.au/relatedposts/timestamp.php?shop={{shop.domain}}"></script>';
      if (strpos($relatedBlog['value'], $checkString2) !== false) {
        $newString = str_replace($checkString2, '', $relatedBlog['value']);
        $assets_arr1 = ["asset" => ["key" => "snippets/relatedblogs.liquid","value" => $newString]];
        $sc->call('DELETE', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json?asset[key]=snippets/relatedblogs.liquid");
        $resp1 = $sc->call('PUT', "/admin/api/2020-01/themes/{$active_theme_id}/assets.json", $assets_arr1);
      }

  } catch (\Exception $e) {
  }
  $select_sql = "UPDATE `app`  set latest_update=1 where shop = '".$shop."'";
  $res = mysqli_query($db_obj, $select_sql);
  echo json_encode(['code'=>200,'msg'=>"Updated Successfully"]);
}


?>
