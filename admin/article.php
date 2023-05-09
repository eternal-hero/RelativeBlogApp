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


$domain = "";
if(isset($_POST['domain']))
{
$domain = $_POST['domain'];
}
$articli_value="";
if(isset($_POST['art_handle']))
{
    $art_value = $_POST['art_handle'];
}

$app_name = 'Select * from app where shop="'.$domain.'"';
$app_data = mysqli_query($db_obj,$app_name);
$app_res = mysqli_fetch_assoc($app_data);

$blog_list = 'Select * from blog_list where shop="'.$domain.'"';
$blog_list_data = mysqli_query($db_obj,$blog_list);
if (mysqli_num_rows($blog_list_data) > 0) {
    $list_datas = array();
    while($blog_list_res = mysqli_fetch_assoc($blog_list_data)){
        $list_datas[] = $blog_list_res['b_id'];
    }
    $blogs_url = [];
    foreach($list_datas as $list_value){
        $sc = new ShopifyClient($domain, $app_res['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
        $blogs_url[] = $sc->call("GET", "admin/api/" . getYear() . "/blogs/{$list_value}/articles.json");
        
    }
    
 }

$url = json_decode(json_encode($blogs_url,true));

    foreach($url as $articles) 
    {
        
        foreach($articles as $article)
        {
            //$shop =  mysqli_real_escape_string($db_obj,$domain);
            $a_id = mysqli_real_escape_string($db_obj,$article->id);
            
            $blog_id = mysqli_real_escape_string($db_obj,$article->blog_id);
            
            $a_title = mysqli_real_escape_string($db_obj,$article->title);
            
            $a_handle = mysqli_real_escape_string($db_obj,$article->handle);
        
            $a_tags = mysqli_real_escape_string($db_obj,$article->tags);
        
            $a_content = mysqli_real_escape_string($db_obj,$article->body_html);
        
            $a_author = mysqli_real_escape_string($db_obj,$article->author);
            $published_at = mysqli_real_escape_string($db_obj,$article->published_at);
            if(isset($article->image->src)){
                $image = mysqli_real_escape_string($db_obj,$article->image->src);
            }else{
                $image ="";
            }
            
            //$a_json = json_encode($article);
            
            $select_sql_article = "SELECT `id`,`a_id` FROM `artical_list` WHERE a_id='" . $a_id . "' order by id desc";
            $res_article = mysqli_query($db_obj, $select_sql_article);
            if (mysqli_num_rows($res_article) > 0) {
                
            $update = mysqli_query($db_obj, "UPDATE artical_list SET shop_name = '".$domain."', blog_id= '".$blog_id."', a_title='" . $a_title . "',a_handle='" . $a_handle . "',a_tags='" . $a_tags . "',a_content='".$a_content."',a_author='".$a_author."',published_at='".$published_at."',image='".$image."' WHERE a_id='" . $a_id . "'");
            
                if($update)
                {
                   //echo "Updated article";
                }else{
                    //echo "Not updated article";
                   
                }
            }else{
                $query = "INSERT into artical_list(shop_name,blog_id,a_id, a_title, a_handle, a_tags, a_content, a_author,published_at,image) VALUES('".$domain."','".$blog_id."','" . $a_id . "','" . $a_title . "','" . $a_handle . "','" . $a_tags . "','".$a_content."','".$a_author."','".$published_at."','".$image."')";
                if (mysqli_query($db_obj, $query)) 
                {
                   //echo "Inserted article"; 
                }else{
                // echo "Not inserted article";
                }
            }
        }
    }

    $html_setting_query = 'Select * from html_settings where shop="'.$app_res['shop'].'"';
    $html_setting_data = mysqli_query($db_obj,$html_setting_query);
    if (mysqli_num_rows($html_setting_data) > 0) {
        $html_settings_list_data = array();
        while($html_setting_res = mysqli_fetch_assoc($html_setting_data)){
            
            $html_settings_list_data[]=$html_setting_res;
        }
     }

    $html_setting_no_of_char = $html_settings_list_data[0]['no_char_post'];
    
    $html_setting_select_theme = $html_settings_list_data[0]['select_theme'];

    $related_setting_query = 'Select * from related_settings where shop="'.$app_res['shop'].'"';
    $related_setting_data = mysqli_query($db_obj,$related_setting_query);
    $related_setting_res = mysqli_fetch_assoc($related_setting_data);

    $realeted_exclude_tags = explode(",",$related_setting_res['ex_tags']);
    $realeted_exclude_id = explode(",",$related_setting_res['ex_ids']);
    $realeted_exclude_pool_tags = explode(",",$related_setting_res['ex_frmpool_tags']);
    $realeted_exclude_pool_id = explode(",",$related_setting_res['ex_frmpool_id']);
    $blogs = explode(",",$related_setting_res['blogs']);
    $blog_article = array();
    foreach($blogs as $article_blogs)
    {
        $blog_article[] =  sprintf('"%s"', $article_blogs);

    }
    $blog_data = implode(',',$blog_article);

    $related_excludes_tag = [];
    foreach($realeted_exclude_tags as $relatetag)
    {
        $related_excludes_tag[] = sprintf('"%s"', trim($relatetag));
    }

    $blog_data_tags = implode(',',$related_excludes_tag);

    $related_excludes_id = [];
    foreach($realeted_exclude_id as $relateid)
    {
        $related_excludes_id[] = sprintf('"%s"', $relateid);
    }

    $blog_data_ids = implode(',',$related_excludes_id);

    $related_excludes_pool_id = [];
    foreach($realeted_exclude_pool_id as $relatepoolid)
    {
        $related_excludes_pool_id[] = sprintf('"%s"', $relatepoolid);
    }

    $blog_data_pool_ids = implode(',',$related_excludes_pool_id);

    $related_excludes_pool_tag = [];
    foreach($realeted_exclude_pool_tags as $relatepooltag)
    {
        $related_excludes_pool_tag[] = sprintf('"%s"', $relatepooltag);
    }

    $blog_data_pool_tags = implode(',',$related_excludes_pool_tag);
    $blog_data_pool_replace = str_replace(" ","",$blog_data_pool_tags);

    $concatinate_tag = $blog_data_pool_replace.",".$blog_data_tags;
    $concatinate_id = $blog_data_ids.",".$blog_data_pool_ids;
    
    if($related_setting_res['related_to'] == 0){
        if($related_setting_res['enable_bimage']== 1){
            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";  
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                           
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";  
                            
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }
                    }
                    
                }
            }elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`a_tags`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                            
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`a_tags`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`a_tags`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                            
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`a_tags`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }
                    }
                }
                
            }elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";

                        
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";

                        
                        }else{
                            $article_list = "SELECT `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle`,`image` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display']."";
                        }
                    }
                }
            }
            
        }else{
            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                        $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                            }else{
                                $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                            }
                    }
                }
            }elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                        $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                            }else{
                                $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display']."";
                            }
                    }
                }
            }elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                        $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){
                            $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                            }else{
                                $article_list = "Select `a_author`,`a_title`,SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_tags`,`a_handle`,`b_handle` from artical_list left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display']."";
                            } 
                    }    
                }
            }
        }
      
       if(isset($article_list))
       {
        $article_list_data = mysqli_query($db_obj,$article_list);
        if (mysqli_num_rows($article_list_data) > 0) {
            $article_data = array();
            while($article_list_res = mysqli_fetch_assoc($article_list_data)){
    
                array_push($article_data,$article_list_res);
               
            }
            $all_value['article_list'] = $article_data;
            $all_value['theme_select'] = $html_setting_select_theme;
            print_r(json_encode($all_value));die;
        }
       
       }else{
        print_r(json_encode(array()));die;
       }
        
    }
    elseif($related_setting_res['related_to'] == 1){
        if($related_setting_res['enable_bimage']== 1){
            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                           
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }  
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                           
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        } 
                    }  
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                      
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }

        }else{

            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    { 
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title`  FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_content_list = "SELECT SUBSTRING(`a_content`, 1, $html_setting_no_of_char) as `content`,`a_handle`,`b_handle`,`a_title`  FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            
        }
        if(isset($article_content_list))
        {
            $article_content_data = mysqli_query($db_obj,$article_content_list);
            if (mysqli_num_rows($article_content_data) > 0) {
                $article_data = array();
                while($article_content_res = mysqli_fetch_assoc($article_content_data)){
                    array_push($article_data,$article_content_res);
                    
                }
                $all_value['article_list'] = $article_data;
                $all_value['theme_select'] = $html_setting_select_theme;
                print_r(json_encode($all_value));die;
            }
        }else{
            print_r(json_encode(array()));die;
        }
        
    }
    elseif($related_setting_res['related_to'] == 2){

        if($related_setting_res['enable_bimage']== 1){
            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`image`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }

        }else{

            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".$blog_data.") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }   
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".$blog_data.") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        } 
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title`  FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title` FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_tags_list = "SELECT `a_tags`,`b_handle`,`a_handle`,`a_title`  FROM `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            
        }
        
        if(isset($article_tags_list))
        {
            $article_tags_data = mysqli_query($db_obj,$article_tags_list);
            if (mysqli_num_rows($article_tags_data) > 0) {
    
                $article_data = array();
                while($article_tags_res = mysqli_fetch_assoc($article_tags_data)){
                        array_push($article_data,$article_tags_res);
                    
                }
                $all_value['article_list'] = $article_data;
                $all_value['theme_select'] = $html_setting_select_theme;
                print_r(json_encode($all_value));die;
            }
        }else{
            print_r(json_encode(array()));die;
        }
        
    }
    elseif($related_setting_res['related_to'] == 3){

        if($related_setting_res['enable_bimage']== 1){
            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`b_handle`,`a_handle`,`image`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }

        }else{

            if($related_setting_res['handle_rel_post'] == 0)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand(),a_id DESC limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 1)
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".$blog_data.") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".$blog_data.") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") order by rand() limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") order by rand() limit ".$related_setting_res['no_post_display'].""; 
                        } 
                    }
                }
            }
            elseif($related_setting_res['handle_rel_post'] == 2 && $related_setting_res['custom_msg'] || $related_setting_res['handle_rel_post'] == 2 || $related_setting_res['custom_msg'] == '')
            {
                if($related_setting_res['blogs']){
                    if($related_setting_res['show_same_btype'] == 0)
                    {
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` like '%".$art_value."%' limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }else{
                        if($related_setting_res['ex_tags'] && $related_setting_res['ex_ids'] && $related_setting_res['ex_frmpool_tags'] && $related_setting_res['ex_frmpool_id'] || $related_setting_res['ex_tags'] || $related_setting_res['ex_ids'] || $related_setting_res['ex_frmpool_tags'] || $related_setting_res['ex_frmpool_id']){  
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") and `a_tags` NOT IN(".$concatinate_tag.") and `a_id` NOT IN(".str_replace(" ","",$concatinate_id).") limit ".$related_setting_res['no_post_display']."";
                        }else{
                            $article_author_list = "Select `a_author`,`a_handle`,`b_handle`,`a_title` from `artical_list` left join blog_list on artical_list.blog_id = blog_list.b_id WHERE `blog_id` IN (".trim($blog_data).") limit ".$related_setting_res['no_post_display'].""; 
                        }
                    }
                }
            }
            
        }
        if(isset($article_author_list))
        {
            $article_author_data = mysqli_query($db_obj,$article_author_list);
            if (mysqli_num_rows($article_author_data) > 0) {
                $article_data = array();
                while($article_author_res = mysqli_fetch_assoc($article_author_data)){
                    array_push($article_data,$article_author_res);
                    
                }
                $all_value['article_list'] = $article_data;
                $all_value['theme_select'] = $html_setting_select_theme;
                print_r(json_encode($all_value));die;
            }
        }else{
            print_r(json_encode(array()));die;
        }
        
    }

?>