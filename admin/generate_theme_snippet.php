<?php
function genereate_article_html($db_obj, $sc, $shop)
{
    try {

        $select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
        $res = mysqli_query($db_obj, $select_sql);
        if (mysqli_num_rows($res) > 0) {
            $res_arr = mysqli_fetch_assoc($res);
            $token = $res_arr['token'];
        }
        $themes = $sc->call('GET', '/admin/api/' . getYear() . '/themes.json');
        $active_theme_arr = loopAndFind($themes, 'role', 'main');
        $active_theme_id = $active_theme_arr[0]['id'];
        $related_sql = mysqli_query($db_obj, "SELECT * FROM `related_settings` WHERE `shop` = '" . $shop . "'");
        if (mysqli_num_rows($related_sql) > 0) {
            $rel_data = mysqli_fetch_assoc($related_sql);
        }

        $html_sql = mysqli_query($db_obj, "SELECT * FROM `html_settings` WHERE `shop` = '" . $shop . "'");
        if (mysqli_num_rows($html_sql) > 0) {
            $html_data = mysqli_fetch_assoc($html_sql);
        }

        $ex_ids = "";
        if ($rel_data['blogs'] != "") {
            $ex_ids = $rel_data['ex_ids'];
        }

        $ex_frmpool_id = "";
        if ($rel_data['ex_frmpool_id'] != "") {
            $ex_frmpool_id = $rel_data['ex_frmpool_id'];
        }

        $ex_frmpool_tags = "";
        if ($rel_data['ex_frmpool_tags'] != "") {
            $ex_frmpool_tags = $rel_data['ex_frmpool_tags'];
        }

        $ex_tags = $rel_data['ex_tags'];
        $blog_ids = "";
        $blog_handle_arr = "";
        $blog_ids = "";
        $blog_handle_arr = "";
        $blog_ids = $rel_data['blogs'];
        $bid_array = explode(",", $rel_data['blogs']);
        foreach ($bid_array as $b_id) {
            $blog_qry = mysqli_query($db_obj, "SELECT b_id,b_handle FROM blog_list where shop='" . $shop . "' and b_id = '" . $b_id . "'");
            if (mysqli_num_rows($blog_qry) > 0) {
                $blog_data = mysqli_fetch_assoc($blog_qry);
                $blog_handle_arr .= $blog_data['b_handle'] . ",";
            }
        }
        $blog_handle_arr = trim($blog_handle_arr, ",");
        $blog_ids = trim($blog_ids, ",");

        $rootDir = $_SERVER["DOCUMENT_ROOT"];
        ob_start();
?>
        {%- assign timestamp = 'now' | date : "%s" -%}
        <?php include "../theme_relatedblogs_structure.php"; ?>
<?php
        $html = ob_get_clean();
        $count = 0;
        $file_name = explode(".", $shop);

        $assets_arr1 = array("asset" =>
            array(
                "key" => "snippets/relatedblogs.liquid",
                "value" => $html//"https://admin.drawyourcard.com/hello.txt"//SITE_URL ."/article_html_files/".$file_name[0]."_relatedblogs.html"
            ));
        $resp1 = $sc->call('PUT',"/admin/api/".getYear()."/themes/{$active_theme_id}/assets.json",$assets_arr1);
        

        try {
            if (file_exists("../article_html_files/" . $file_name[0] . "_relatedblogs.html")) {
                @unlink("../article_html_files/" . $file_name[0] . "_relatedblogs.html");
            }
            file_put_contents("../article_html_files/" . $file_name[0] . "_relatedblogs.html", $html);
            $count = 1;
        } catch (Exception $ex) {
        }

        // try {
        //     $sc->call('DELETE', "/admin/api/" . getYear() . "/themes/{$active_theme_id}/assets.json?asset[key]=snippets/relatedblogs.liquid");
        // } catch (Exception $ex) {
        // }

        
            // $assets_arr1 = array("asset" =>
            // array(
            //     "key" => "snippets/relatedblogs.liquid",
            //     "value" => $html
            //     // "src" => SITE_URL ."/article_html_files/".$file_name[0]."_relatedblogs.html"
            // ));

            // $resp1 = $sc->call('PUT',"/admin/api/".getYear()."/themes/{$active_theme_id}/assets.json",$html);
        
    } catch (Exception $ex) {
    }
}
?>