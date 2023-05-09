<?php
// include 'CommonIncludes.php';
include '../common_includes.php';
include 'generate_theme_snippet.php';

$date = date("Y-m-d H:i:s");

if (isset($_POST['page'])) {
$isValid = IsValidRequest();
if($isValid['code'] == 200){

    $shop = $_POST['shop'];
    $select_sql = "SELECT `id`,`token`,`store_metafield_id` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
    $res = mysqli_query($db_obj, $select_sql);
    if (mysqli_num_rows($res) > 0) {
        $res_arr = mysqli_fetch_assoc($res);
        $token = $res_arr['token'];
    }
    $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

    if ($_POST['page'] == "html_settings") {
        try {
            $shop = $_POST['shop'];
            $top_text = mysqli_real_escape_string($db_obj, $_POST['top_text']);
            $html_before_list = mysqli_real_escape_string($db_obj, $_POST['html_before_list']);
            $html_before_post = mysqli_real_escape_string($db_obj, $_POST['html_before_post']);
            $html_after_post = mysqli_real_escape_string($db_obj, $_POST['html_after_post']);
            $html_after_list = mysqli_real_escape_string($db_obj, $_POST['html_after_list']);
            $no_char_post = mysqli_real_escape_string($db_obj, $_POST['no_char_post']);
            $custom_style = mysqli_real_escape_string($db_obj, $_POST['custom_style']);
            $select_theme = mysqli_real_escape_string($db_obj, $_POST['select_theme']);
            $read_more_theme_2 = mysqli_real_escape_string($db_obj, $_POST['read_more_theme_2']);
            $select_sql = "SELECT `shop` FROM `html_settings` WHERE `shop` = '" . $shop . "'";
            $res = mysqli_query($db_obj, $select_sql);
            if (mysqli_num_rows($res) > 0) {
                mysqli_query($db_obj, "update html_settings set top_text='" . $top_text . "',html_before_list='" . $html_before_list . "',html_before_post='" . $html_before_post . "',html_after_post='" . $html_after_post . "',html_after_list='" . $html_after_list . "',no_char_post='" . $no_char_post . "',custom_style='" . $custom_style . "',select_theme='" . $select_theme . "',updated_date='" . $date . "',read_more_theme_2='" . $read_more_theme_2 . "' where shop='" . $shop . "'");
            } else {
                mysqli_query($db_obj, "insert into html_settings(`shop`,`top_text`,`html_before_list`,`html_before_post`,`html_after_post`,`html_after_list`,`no_char_post`,`custom_style`,`select_theme`,`created_date`,`updated_date`,`read_more_theme_2`)"
                        . "VALUES('" . $shop . "','" . $top_text . "','" . $html_before_list . "','" . $html_before_post . "','" . $html_after_post . "','" . $html_after_list . "','" . $no_char_post . "','" . $custom_style . "','" . $select_theme . "','" . $date . "','" . $date . "','" . $read_more_theme_2 . "')");
            }
            genereate_article_html($db_obj,$sc,$shop);
            echo json_encode([
              "code"=>200,
              "msg"=>"Settings Updated",
            ]);

        } catch (Exception $ex) {
            echo json_encode([
              "code"=>100,
              "msg"=>$ex->getMessage(),
            ]);
        }
    }

    if ($_POST['page'] == "related_settings") {
        try {
              $shop = $_POST['shop'];
              $related_to = $_POST['related_to'];
              $no_post_display = $_POST['no_post_display'];
              $handle_rel_post = $_POST['handle_rel_post'];
              $show_same_btype = $_POST['show_same_btype'];
              $no_image_sizes = $_POST['no_image_sizes'];
              $disp_description = $_POST['disp_description'];
              $ex_tags = mysqli_real_escape_string($db_obj, $_POST['ex_tags']);
              $ex_ids = mysqli_real_escape_string($db_obj, $_POST['ex_ids']);
              $ex_frmpool_id = mysqli_real_escape_string($db_obj, $_POST['ex_frmpool_id']);
              $ex_frmpool_tags = mysqli_real_escape_string($db_obj, $_POST['ex_frmpool_tags']);
              $blogs = mysqli_real_escape_string($db_obj, $_POST['blogs']);
              $custom_msg = mysqli_real_escape_string($db_obj, $_POST['custom_msg']);
              $enable_bimage = $_POST['enable_bimage'];
              $select_sql = "SELECT `shop` FROM `related_settings` WHERE `shop` = '" . $shop . "'";
              $res = mysqli_query($db_obj, $select_sql);
              if (mysqli_num_rows($res) > 0) {
            	$MyQuery = "update related_settings set related_to='" . $related_to . "',no_post_display='" . $no_post_display . "',handle_rel_post='" . $handle_rel_post . "',show_same_btype='" . $show_same_btype . "',ex_tags='" . $ex_tags . "',ex_ids='" . $ex_ids . "',ex_frmpool_id='" . $ex_frmpool_id . "',ex_frmpool_tags='" . $ex_frmpool_tags . "',blogs='" . $blogs . "',custom_msg='" . $custom_msg . "',updated_date='" . $date . "',enable_bimage='".$enable_bimage."',image_size='".$no_image_sizes."',display_desc='".$disp_description."' where shop='" . $shop . "'";
            	} else {
            	$MyQuery = "insert into related_settings(`shop`,`related_to`,`no_post_display`,`handle_rel_post`,`show_same_btype`,`ex_tags`,`ex_ids`,`ex_frmpool_id`,`ex_frmpool_tags`,`blogs`,`custom_msg`,`created_date`,`updated_date`,`enable_bimage`,`image_size`,`display_desc`)"
            	. "VALUES('" . $shop . "','" . $related_to . "','" . $no_post_display . "','" . $handle_rel_post . "','" . $show_same_btype . "','" . $ex_tags . "','" . $ex_ids . "','" . $ex_frmpool_id . "','" . $ex_frmpool_tags . "','" . $blogs . "','" . $custom_msg . "','" . $date . "','" . $date . "','0','" . $no_image_sizes . "','" . $disp_description . "')";
            	}
              $uPD = mysqli_query($db_obj,$MyQuery);

            $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
            genereate_article_html($db_obj,$sc,$shop);
            $total_blogs = $sc->call("GET", "/admin/api/".getYear()."/blogs/count.json");
            $flag = "false";
            if ($total_blogs > 0) {
                $get_new_blogs = $sc->call("GET", "admin/api/".getYear()."/blogs.json");
                $new_blog_ids = array();
                foreach ($get_new_blogs as $b) {
                    $new_blog_ids[] = $b['id'];
                }

                $old_blog_ids = array();
                $get_old_blogs = mysqli_query($db_obj, "select b_id from blog_list where shop='".$shop."'");
                if (mysqli_num_rows($get_old_blogs) > 0) {
                    while ($res = mysqli_fetch_assoc($get_old_blogs)) {
                        $old_blog_ids[] = $res['b_id'];
                    }
                }
                $delete_bid = array_diff($old_blog_ids,$new_blog_ids);
                $delete_bcount = count($delete_bid);
                $d_count = 0;
                if($delete_bid != null){
                    foreach ($delete_bid as $id){
                        $d_count++;
                        mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $id . "'");
                    }
                }

                $loop_count = 0;
                if($delete_bcount == $d_count){
                    foreach ($get_new_blogs as $blog) {
                        $loop_count++;
                        $b_id = $blog['id'];
                        $b_title = mysqli_real_escape_string($db_obj, $blog['title']);
                        $b_handle = mysqli_real_escape_string($db_obj, $blog['handle']);
                        $b_tags = mysqli_real_escape_string($db_obj, $blog['tags']);
                        $b_json = json_encode($blog);
                        $select_sql_1 = "SELECT `id`,`b_id` FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'";
                        $res1 = mysqli_query($db_obj, $select_sql_1);
                        if (mysqli_num_rows($res1) > 0) {
                            mysqli_query($db_obj, "UPDATE blog_list SET b_title='" . $b_title . "',b_handle='" . $b_handle . "',b_tags='" . $b_tags . "',b_json='" . mysqli_real_escape_string($db_obj, $b_json) . "' WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'");
                        } else {
                            mysqli_query($db_obj, "INSERT into blog_list(`b_id`,`shop`,`b_title`,`b_handle`,`b_tags`,`b_json`) "
                                    . "VALUES('" . $b_id . "','" . $shop . "','" . $b_title . "','" . $b_handle . "','" . $b_tags . "','" . mysqli_real_escape_string($db_obj, $b_json) . "')");
                        }

                        if ($loop_count == $total_blogs) {
                             $flag = "true";
                        }
                    }
                }
            }else{
                mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "'");
                $flag = "true";
            }

            if($flag = "true"){
                $setting_query = mysqli_query($db_obj,"SELECT blogs FROM related_settings WHERE shop='".$shop."'");
                $setting_data = mysqli_fetch_assoc($setting_query);
                $blogs_arr = explode(",",$setting_data['blogs']);

                $select_blog = mysqli_query($db_obj,"SELECT b_title,b_id,b_handle from blog_list where shop='".$shop."'");
                $html = "";
                if (mysqli_num_rows($select_blog) > 0) {
                    while($blog_data = mysqli_fetch_assoc($select_blog)){
                        $html .= '<input id="'.$blog_data['b_handle'].'" name="blogs" value="'.$blog_data['b_id'].'" type="checkbox"';
                        if(in_array($blog_data['b_id'],$blogs_arr)){
                            $html .= "checked='checked'";
                        }
                        $html .= '><label for="'.$blog_data['b_handle'].'" style="font-weight: normal;">'.$blog_data['b_title'].'</label>';
                    }
                 }  else {
                    $html .= '<label>There is no blogs in your store.</label>';
                 }
            }
            echo json_encode([
              "code"=>200,
              "msg"=>"Settings Updated",
            ]);
        } catch (Exception $ex) {
          echo json_encode([
            "code"=>100,
            "msg"=>$ex->getMessage(),
          ]);
        }
    }

    if ($_POST['page'] == "clear_cache") {
        try {
            $shop = $_POST['shop'];
            $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
            genereate_article_html($db_obj,$sc,$shop);
            $total_blogs = $sc->call("GET", "/admin/api/".getYear()."/blogs/count.json");
            $flag = "false";
            if ($total_blogs > 0) {
                $get_new_blogs = $sc->call("GET", "admin/api/".getYear()."/blogs.json");
                $new_blog_ids = array();
                foreach ($get_new_blogs as $b) {
                    $new_blog_ids[] = $b['id'];
                }

                $old_blog_ids = array();
                $get_old_blogs = mysqli_query($db_obj, "select b_id from blog_list where shop='".$shop."'");
                if (mysqli_num_rows($get_old_blogs) > 0) {
                    while ($res = mysqli_fetch_assoc($get_old_blogs)) {
                        $old_blog_ids[] = $res['b_id'];
                    }
                }
                $delete_bid = array_diff($old_blog_ids,$new_blog_ids);
                $delete_bcount = count($delete_bid);
                $d_count = 0;
                if($delete_bid != null){
                    foreach ($delete_bid as $id){
                        $d_count++;
                        mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $id . "'");
                    }
                }
                $loop_count = 0;
                if($delete_bcount == $d_count){
                    foreach ($get_new_blogs as $blog) {
                        $loop_count++;
                        $b_id = $blog['id'];
                        $b_title = mysqli_real_escape_string($db_obj, $blog['title']);
                        $b_handle = mysqli_real_escape_string($db_obj, $blog['handle']);
                        $b_tags = mysqli_real_escape_string($db_obj, $blog['tags']);
                        $b_json = json_encode($blog);
                        $select_sql_1 = "SELECT `id`,`b_id` FROM `blog_list` WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'";
                        $res1 = mysqli_query($db_obj, $select_sql_1);
                        if (mysqli_num_rows($res1) > 0) {
                            mysqli_query($db_obj, "UPDATE blog_list SET b_title='" . $b_title . "',b_handle='" . $b_handle . "',b_tags='" . $b_tags . "',b_json='" . mysqli_real_escape_string($db_obj, $b_json) . "' WHERE `shop` = '" . $shop . "' AND b_id='" . $b_id . "'");
                        } else {
                            mysqli_query($db_obj, "INSERT into blog_list(`b_id`,`shop`,`b_title`,`b_handle`,`b_tags`,`b_json`) "
                                    . "VALUES('" . $b_id . "','" . $shop . "','" . $b_title . "','" . $b_handle . "','" . $b_tags . "','" . mysqli_real_escape_string($db_obj, $b_json) . "')");
                        }
                        if ($loop_count == $total_blogs) {
                             $flag = "true";
                        }
                    }
                }
            }else{
                mysqli_query($db_obj, "DELETE FROM `blog_list` WHERE `shop` = '" . $shop . "'");
                $flag = "true";
            }

            if($flag = "true"){
                $setting_query = mysqli_query($db_obj,"SELECT blogs FROM related_settings WHERE shop='".$shop."'");
                $setting_data = mysqli_fetch_assoc($setting_query);
                $blogs_arr = explode(",",$setting_data['blogs']);

                $select_blog = mysqli_query($db_obj,"SELECT b_title,b_id,b_handle from blog_list where shop='".$shop."'");
                $html = "";
                if (mysqli_num_rows($select_blog) > 0) {
                    while($blog_data = mysqli_fetch_assoc($select_blog)){
                        $html .= '<input id="'.$blog_data['b_handle'].'" name="blogs" value="'.$blog_data['b_id'].'" type="checkbox"';
                        if(in_array($blog_data['b_id'],$blogs_arr)){
                            $html .= "checked='checked'";
                        }
                        $html .= '><label for="'.$blog_data['b_handle'].'" style="font-weight: normal;">'.$blog_data['b_title'].'</label>';
                    }
                 }  else {
                    $html .= '<label>There is no blogs in your store.</label>';
                 }
            }
            $json = array();
            $json['data'] = $html;
            $json['status'] = "true";
            echo json_encode($json);
        } catch (Exception $ex) {
            $json = array();
            $json['data'] = "";
            $json['status'] = $ex;
            echo json_encode($json);
        }
    }

    if ($_POST['page'] == "create_snippest") {
        try {
            $shop = $_POST['shop'];
            $sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
            genereate_article_html($db_obj,$sc,$shop);
            mysqli_query($db_obj, "UPDATE `related_settings` SET `install_status` = '1' where `shop` = '".$shop."'");
           echo json_encode([
             "code"=>200,
             "msg"=>"Widget Created SuccessFully",
           ]);
        } catch (Exception $ex) {
            echo json_encode([
              "code"=>100,
              "msg"=>$ex->getMessage(),
            ]);
        }
    }
  }else{
      echo json_encode($isValid);
  }
}


?>
