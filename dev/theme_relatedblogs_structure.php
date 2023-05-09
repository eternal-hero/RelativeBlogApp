<?php
$customRowClass="row";
if(isset($html_data['select_theme']) && $html_data['select_theme'] == 'theme_2') {
  $customRowClass="rb_row";
}
?>
{%- assign ex_ids = "<?php echo $ex_ids; ?>"-%}
<?php if ($ex_frmpool_id != "") { ?>
    {%- assign ex_frmpool_id = "<?php echo $ex_frmpool_id; ?>" | append: "," | append: article.id -%}
<?php } else { ?>
    {%- assign ex_frmpool_id = article.id | append: '"' | prepend: '"' -%}
<?php } ?>

{%- assign ex_tags = "<?php echo $ex_tags; ?>" | split: "," -%}
{%- assign no_post_display = <?php echo $rel_data['no_post_display']; ?> -%}

{%- assign b_id = blog.id  -%}

<?php if ($blog_ids != "") { ?>
    {%- assign blog_ids = "<?php echo $blog_ids; ?>"  -%}
<?php } else { ?>
    {%- assign blog_ids = blog.id | append: '"' | prepend: '"' -%}
<?php } ?>

<?php if ($blog_handle_arr != "") { ?>
    <?php  if ($rel_data['show_same_btype'] == 0) { ?>
    {%- assign blog_handle_arr = blog.handle -%}
    <?php }else{ ?>
    {%- assign blog_handle_arr = "<?php echo $blog_handle_arr; ?>" | split: "," -%}
    <?php } ?>
<?php } else { ?>
    {%- assign blog_handle_arr = blog.handle | append: '"' | prepend: '"' -%}
<?php } ?>

{%- assign blog_flag = "false" -%}

{%- unless blog_ids contains b_id -%}
{%- assign blog_flag = "true" -%}
{%- endunless -%}

{%- unless ex_ids contains article.id -%}

{%- if blog_flag == "false" -%}

{%- assign article_temp = article.id -%}

{%- assign related_posts = "" -%}
{%- assign curr_blog = blog.handle -%}

{%- for article in blogs[curr_blog].articles -%}
{%- if article.id == article_temp -%}
{%- assign current_post_tags = article.tags -%}
{%- assign article_author = article.author -%}
{%- assign article_title = article.title -%}
{%- assign article_title_arr = article_title | remove : " " | split: "" -%}
{%- endif -%}
{%- endfor -%}

{%- assign main_flag = "true" -%}
{%- for tag in ex_tags -%}
{%- assign ex_tags_temp = tag | strip -%}
{%- if current_post_tags contains ex_tags_temp -%}
{%- assign main_flag = "false" -%}
{%- endif -%}
{%- endfor -%}

{%- if main_flag == "true" -%}

<?php
if($html_data['select_theme'] == 'theme_1') {?>
<div id="relatedblogs">

    {%- for blog in blog_handle_arr -%}

    {%- assign blog_temp = blog -%}

    {%- for article in blogs[blog_temp].articles -%}
    {%- unless ex_frmpool_id contains article.id -%}

    <?php if ($rel_data['related_to'] == 1) { ?>

        {%- assign atitle_temp = article.title | remove : " " | split: "" -%}
        {%- assign flag = "false" -%}
        {%- for word in atitle_temp -%}
        {%- if article_title_arr contains word -%}
        {%- assign flag = "true" -%}
        {%- break -%}
        {%- endif -%}
        {%- endfor -%}

        {%- if flag == "true" -%}
        {%- assign related_posts = related_posts | append:article.id | append : "," -%}
        {%- endif -%}

    <?php } elseif ($rel_data['related_to'] == 2) { ?>

        {%- for tag in article.tags -%}
        {%- if current_post_tags contains tag -%}
        {%- assign related_posts = related_posts | append:article.id | append : "," -%}
        {%- endif -%}
        {%- endfor -%}

    <?php } elseif ($rel_data['related_to'] == 3) { ?>

        {%- if article_author ==  article.author -%}
        {%- assign related_posts = related_posts | append:article.id | append : ","-%}
        {%- endif -%}

    <?php } elseif ($rel_data['related_to'] == 0) { ?>

        {%- assign atitle_temp = article.title | remove : " " | split: "" -%}
        {%- assign flag = "false" -%}
        {%- for word in atitle_temp -%}
        {%- if article_title_arr contains word -%}
        {%- assign flag = "true" -%}
        {%- break -%}
        {%- endif -%}
        {%- endfor -%}

        {%- assign t_flag = "false" -%}
        {%- for tag in current_post_tags -%}
        {%- if article.tags contains tag -%}
        {%- assign t_flag = "true" -%}
        {%- break -%}
        {%- endif -%}
        {%- endfor -%}

        {%- if t_flag == "true"  or article_author ==  article.author or flag == "true" -%}
        {%- assign related_posts = related_posts | append:article.id | append : ","-%}
        {%- endif -%}
    <?php } ?>

    {%- endunless -%}
    {%- endfor -%}


    {%- endfor -%}

    {%- if related_posts.size > 0 -%}

    {%- assign tsl = timestamp | slice : -1,1 | times:1 -%}

    {%- assign rids = related_posts | split: ',' -%}


    {%- assign rindex = tsl -%}

    {%- if tsl > rids.size or tsl < 0 -%}
            {%- assign time_arr = timestamp | split: '' -%}
            {%- for t in time_arr reversed -%}
            {%- assign ti = t | times:1 -%}
            {%- if ti < rids.size -%}
                    {%- assign rindex = forloop.index -%}
                {%- break -%}
            {%- endif -%}
            {%- endfor -%}
    {%- endif -%}

    {%- if rindex == rids.size -%}
            {%- assign rindex = rindex | minus : 1-%}
    {%- endif -%}

    {%- assign articleids = '' -%}
    {%- for rid in rids offset:rindex -%}
            {%- assign articleids = articleids | append:rid | append : "," -%}
    {%- endfor -%}

{%- assign rsize = articleids | split:","  -%}

    {%- if rsize.size < no_post_display -%}
        {%- for rid in rids -%}
        {%- unless articleids contains rid -%}
        {%- assign articleids = articleids | append:rid | append : "," -%}
        {%- endunless -%}
        {%- endfor -%}
    {%- endif -%}

    {%- assign rsize = articleids | size | minus:1 -%}
    {%- assign articleids = articleids | slice: 0, rsize | split:"," -%}

{%- assign article_ids = articleids | uniq -%}

    <?php echo $html_data['top_text']; ?>
    <?php echo $html_data['html_before_list']; ?>
    {%- assign count = 0 -%}
    {%- for aid in article_ids -%}
        {%- assign rflag = "false" -%}
        {%- for blog in blog_handle_arr -%}
            {%- assign blog_temp = blog -%}

            {%- for article in blogs[blog_temp].articles -%}
                {%- if count == no_post_display -%}
                {%- break -%}
                {%- endif -%}
                {%- if aid contains article.id -%}

      {%- assign ex_frmpool_tags = "<?php echo $ex_frmpool_tags; ?>" | split: "," -%}
      {%- assign ex_frmpool_main_flag = "true" -%}
      {%- for tag in ex_frmpool_tags -%}
      {%- assign ex_frmpool_ex_tags_temp = tag | strip -%}
      {%- if article.tags contains ex_frmpool_ex_tags_temp -%}
      {%- assign ex_frmpool_main_flag = "false" -%}
      {%- endif -%}
      {%- endfor -%}

      {%- if ex_frmpool_main_flag == "true" -%}
        <?php echo $html_data['html_before_post']; ?>

          <?php if($rel_data['enable_bimage'] == '1'){ ?>
            {%- if article.image -%}
              <div class="rb_image_wrap">
                <a href="{{ article.url }}"><img src="{{ article.image.src | img_url: '<?php echo $rel_data['image_size'];?>' }}" alt="{{ article.title }}" class="rb_image"/></a>
              </div>
            {%- else -%}
              <div class="rb_image_wrap"><div class="rb_no_image"></div></div>
            {%- endif -%}
          <?php } ?>
          <div class="rb_contents">
            <h5 class="rb_title"><a href='{{ article.url }}'>{{ article.title }}</a></h5>

            <?php if ($rel_data['display_desc'] == 'Excerpt') { ?>
              {%- if article.excerpt != "" -%}
                <p>{{ article.excerpt | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?>, "" }}</p>
              {%- endif -%}
            <?php } else{ ?>
              {%- if article.content != "" -%}
                <p>{{ article.content | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?> }}</p>
              {%- endif -%}
            <?php } ?>

          </div>

        <?php echo $html_data['html_after_post']; ?>
      {%- endif -%}

                    {%- assign rflag = "true" -%}
                    {%- assign count = count | plus:1 -%}
                    {%- break -%}
                {%- endif -%}
            {%- endfor -%}
            {%- if rflag == "true" -%}
                {%- break -%}
            {%- endif -%}
        {%- endfor -%}
    {%- endfor -%}
    <?php echo $html_data['html_after_list']; ?>
    {%- else -%}

    <?php if ($rel_data['handle_rel_post'] == 1) { ?>

        {%- assign all_articleids = "" -%}

        {%- for blog in blog_handle_arr -%}
          {%- assign blog_temp = blog -%}
          {%- for article in blogs[blog_temp].articles -%}
                            {%- unless article.id == article_temp -%}
                            {%- assign all_articleids = all_articleids | append:article.id | append : "," -%}
                            {%- endunless -%}
          {%- endfor -%}
        {%- endfor -%}

        {%- assign tsl = timestamp | slice : -1,1 | times:1 -%}

        {%- assign rids = all_articleids | split: ',' -%}


        {%- assign rindex = tsl -%}

        {%- if tsl > rids.size or tsl < 0 -%}
                {%- assign time_arr = timestamp | split: '' -%}
                {%- for t in time_arr reversed -%}
                {%- assign ti = t | times:1 -%}
                {%- if ti < rids.size -%}
                        {%- assign rindex = forloop.index -%}
                                    {%- break -%}
                {%- endif -%}
                {%- endfor -%}
        {%- endif -%}

        {%- if rindex == rids.size -%}
                {%- assign rindex = rindex | minus : 1-%}
        {%- endif -%}

        {%- assign articleids = '' -%}
        {%- for rid in rids offset:rindex -%}
                {%- assign articleids = articleids | append:rid | append : "," -%}
        {%- endfor -%}

                    {%- assign rsize = articleids | split:","  -%}

        {%- if rsize.size < no_post_display -%}
            {%- for rid in rids -%}
            {%- unless articleids contains rid -%}
            {%- assign articleids = articleids | append:rid | append : "," -%}
            {%- endunless -%}
            {%- endfor -%}
        {%- endif -%}

        {%- assign rsize = articleids | size | minus:1 -%}
        {%- assign articleids = articleids | slice: 0, rsize | split:"," -%}

        <?php echo $html_data['top_text']; ?>
        <?php echo $html_data['html_before_list']; ?>
        {%- assign count = 0 -%}
        {%- for aid in articleids -%}
            {%- assign rflag = "false" -%}
            {%- for blog in blog_handle_arr -%}
                {%- assign blog_temp = blog -%}

                {%- for article in blogs[blog_temp].articles -%}
                    {%- if count == no_post_display -%}
                    {%- break -%}
                    {%- endif -%}
                    {%- if aid contains article.id -%}

        {%- assign ex_frmpool_tags = "<?php echo $ex_frmpool_tags; ?>" | split: "," -%}
        {%- assign ex_frmpool_main_flag = "true" -%}
        {%- for tag in ex_frmpool_tags -%}
        {%- assign ex_frmpool_ex_tags_temp = tag | strip -%}
        {%- if article.tags contains ex_frmpool_ex_tags_temp -%}
        {%- assign ex_frmpool_main_flag = "false" -%}
        {%- endif -%}
        {%- endfor -%}

          {%- if ex_frmpool_main_flag == "true" -%}

            <?php echo $html_data['html_before_post']; ?>
            <?php if($rel_data['enable_bimage'] == '1'){ ?>
              {%- if article.image -%}
              <div class="rb_image_wrap">
                <img class="rb_image" src="{{ article.image.src | img_url: '<?php echo $rel_data['image_size'];?>' }}" alt="{{ article.title }}" />
              </div>
              {%- else -%}
              <div class="rb_no_image"></div>
              {%- endif -%}
            <?php } ?>

              <div class="rb_contents">
                <h5 class="rb_title"><a href='{{ article.url }}'>{{ article.title }}</a></h5>

                <?php if ($rel_data['display_desc'] == 'Excerpt') { ?>
                  {%- if article.excerpt != "" -%}
                    <p>{{ article.excerpt | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?>, "" }}</p>
                  {%- endif -%}
                <?php } else{ ?>
                  {%- if article.content != "" -%}
                    <p>{{ article.content | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?> }}</p>
                  {%- endif -%}
                <?php } ?>
              </div>
            <?php echo $html_data['html_after_post']; ?>
          {%- endif -%}

                        {%- assign rflag = "true" -%}
                        {%- assign count = count | plus:1 -%}
                        {%- break -%}
                    {%- endif -%}
                {%- endfor -%}
                {%- if rflag == "true" -%}
                    {%- break -%}
                {%- endif -%}
            {%- endfor -%}
        {%- endfor -%}
        <?php echo $html_data['html_after_list']; ?>

    <?php } elseif ($rel_data['handle_rel_post'] == 2) { ?>
        <?php echo $html_data['top_text']; ?>
        <span><?php echo $rel_data['custom_msg']; ?></span>

        <?php
    } else {

    }
    ?>

    {%- endif -%}


        <style type="text/css">
            .rb_contents{display:inline-block}
            <?php if($rel_data['enable_bimage'] == '1'){ ?>
            .rb_no_image{height:75px; width:100px; background-color:rgb(128, 128, 128); display:inline-block}
            img.rb_image{max-height:75px; max-width:100px; height:auto; width:auto}
            .rb_image_wrap{float:left; width:105px}
            .rb_contents {margin:0 0 0 20px}
            #relatedblogs li {margin:0 0 15px; clear:both}
            <?php } ?>
            #relatedblogs <?php $tag = str_replace("<","",$html_data['html_before_post']); $tag = str_replace(">","",$tag); echo $tag; ?>{display:block;}
            <?php if ($html_data['custom_style'] != "") { ?>
            <?php echo $html_data['custom_style']; ?>
            <?php } ?>
        </style>


</div>
<?php } else {?>

<div id="relatedblogs">


{%- for blog in blog_handle_arr -%}

{%- assign blog_temp = blog -%}

{%- for article in blogs[blog_temp].articles -%}
{%- unless ex_frmpool_id contains article.id -%}

<?php if ($rel_data['related_to'] == 1) { ?>

  {%- assign atitle_temp = article.title | remove : " " | split: "" -%}
  {%- assign flag = "false" -%}
  {%- for word in atitle_temp -%}
  {%- if article_title_arr contains word -%}
  {%- assign flag = "true" -%}
  {%- break -%}
  {%- endif -%}
  {%- endfor -%}

  {%- if flag == "true" -%}
  {%- assign related_posts = related_posts | append:article.id | append : "," -%}
  {%- endif -%}

<?php } elseif ($rel_data['related_to'] == 2) { ?>

  {%- for tag in article.tags -%}
  {%- if current_post_tags contains tag -%}
  {%- assign related_posts = related_posts | append:article.id | append : "," -%}
  {%- endif -%}
  {%- endfor -%}

<?php } elseif ($rel_data['related_to'] == 3) { ?>

  {%- if article_author ==  article.author -%}
  {%- assign related_posts = related_posts | append:article.id | append : ","-%}
  {%- endif -%}

<?php } elseif ($rel_data['related_to'] == 0) { ?>

  {%- assign atitle_temp = article.title | remove : " " | split: "" -%}
  {%- assign flag = "false" -%}
  {%- for word in atitle_temp -%}
  {%- if article_title_arr contains word -%}
  {%- assign flag = "true" -%}
  {%- break -%}
  {%- endif -%}
  {%- endfor -%}

  {%- assign t_flag = "false" -%}
  {%- for tag in current_post_tags -%}
  {%- if article.tags contains tag -%}
  {%- assign t_flag = "true" -%}
  {%- break -%}
  {%- endif -%}
  {%- endfor -%}

  {%- if t_flag == "true"  or article_author ==  article.author or flag == "true" -%}
  {%- assign related_posts = related_posts | append:article.id | append : ","-%}
  {%- endif -%}
<?php } ?>

{%- endunless -%}
{%- endfor -%}


{%- endfor -%}

{%- if related_posts.size > 0 -%}

{%- assign tsl = timestamp | slice : -1,1 | times:1 -%}

{%- assign rids = related_posts | split: ',' -%}


{%- assign rindex = tsl -%}

{%- if tsl > rids.size or tsl < 0 -%}
    {%- assign time_arr = timestamp | split: '' -%}
    {%- for t in time_arr reversed -%}
    {%- assign ti = t | times:1 -%}
    {%- if ti < rids.size -%}
        {%- assign rindex = forloop.index -%}
        {%- break -%}
    {%- endif -%}
    {%- endfor -%}
{%- endif -%}

{%- if rindex == rids.size -%}
    {%- assign rindex = rindex | minus : 1-%}
{%- endif -%}

{%- assign articleids = '' -%}
{%- for rid in rids offset:rindex -%}
    {%- assign articleids = articleids | append:rid | append : "," -%}
{%- endfor -%}

{%- assign rsize = articleids | split:","  -%}

{%- if rsize.size < no_post_display -%}
  {%- for rid in rids -%}
  {%- unless articleids contains rid -%}
  {%- assign articleids = articleids | append:rid | append : "," -%}
  {%- endunless -%}
  {%- endfor -%}
{%- endif -%}

{%- assign rsize = articleids | size | minus:1 -%}
{%- assign articleids = articleids | slice: 0, rsize | split:"," -%}
{%- assign article_ids = articleids | uniq -%}

<div class="<?=$customRowClass?>">
  <?php echo $html_data['top_text']; ?>
{%- assign count = 0 -%}
{%- for aid in article_ids -%}
  {%- assign rflag = "false" -%}
  {%- for blog in blog_handle_arr -%}
    {%- assign blog_temp = blog -%}

    {%- for article in blogs[blog_temp].articles -%}

      {%- if aid contains article.id -%}

        {%- assign ex_frmpool_tags = "<?php echo $ex_frmpool_tags; ?>" | split: "," -%}
        {%- assign ex_frmpool_main_flag = "true" -%}
        {%- for tag in ex_frmpool_tags -%}
        {%- assign ex_frmpool_ex_tags_temp = tag | strip -%}
        {%- if article.tags contains ex_frmpool_ex_tags_temp -%}
        {%- assign ex_frmpool_main_flag = "false" -%}
        {%- endif -%}
        {%- endfor -%}

          {%- if ex_frmpool_main_flag == "true" -%}
            {%- if count == no_post_display -%}
                    {%- break -%}
                    {%- endif -%}
            <div class="rb_blog-grid">
              <?php if($rel_data['enable_bimage'] == '1'){ ?>
                {%- if article.image -%}
                  <a href="{{ article.url }}"><img src="{{ article.image.src | img_url: '<?php echo $rel_data['image_size'];?>' }}" alt="{{ article.title }}"/></a>
                {%- else -%}
                  <div class="rb_no_image"></div>
                {%- endif -%}
              <?php } ?>

              <h5 class="rb_title">
                <a href='{{ article.url }}'>{{ article.title }}</a>
              </h5>

              <?php if ($rel_data['display_desc'] == 'Excerpt') { ?>
                {%- if article.excerpt != "" -%}
                  <div class="rb_contents">
                    <span>{{ article.excerpt | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?>, "" }}</span>
                  </div>
                  <a href="{{ article.url }}" class="rb_continue_button"><?php if($html_data['read_more_theme_2'] == ''){echo 'Read More';} else{echo $html_data['read_more_theme_2'];}?></a>
                {%- endif -%}
              <?php } else{ ?>
                {%- if article.content != "" -%}
                  <div class="rb_contents">
                    <span>{{ article.content | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?> }}</span>
                  </div>
                  <a href="{{ article.url }}" class="rb_continue_button"><?php if($html_data['read_more_theme_2'] == ''){echo 'Read More';} else{echo $html_data['read_more_theme_2'];}?></a>
                {%- endif -%}
              <?php } ?>

            </div>

                        {%- assign rflag = "true" -%}
                        {%- assign count = count | plus:1 -%}
                        {%- break -%}

          {%- endif -%}


      {%- endif -%}
    {%- endfor -%}
    {%- if rflag == "true" -%}
      {%- break -%}
    {%- endif -%}
  {%- endfor -%}
{%- endfor -%}
</div>
{%- else -%}

<?php if ($rel_data['handle_rel_post'] == 1) { ?>

  {%- assign all_articleids = "" -%}

  {%- for blog in blog_handle_arr -%}
    {%- assign blog_temp = blog -%}
    {%- for article in blogs[blog_temp].articles -%}
            {%- unless article.id == article_temp -%}
            {%- assign all_articleids = all_articleids | append:article.id | append : "," -%}
            {%- endunless -%}
    {%- endfor -%}
  {%- endfor -%}

  {%- assign tsl = timestamp | slice : -1,1 | times:1 -%}

  {%- assign rids = all_articleids | split: ',' -%}


  {%- assign rindex = tsl -%}

  {%- if tsl > rids.size or tsl < 0 -%}
      {%- assign time_arr = timestamp | split: '' -%}
      {%- for t in time_arr reversed -%}
      {%- assign ti = t | times:1 -%}
      {%- if ti < rids.size -%}
          {%- assign rindex = forloop.index -%}
                {%- break -%}
      {%- endif -%}
      {%- endfor -%}
  {%- endif -%}

  {%- if rindex == rids.size -%}
      {%- assign rindex = rindex | minus : 1-%}
  {%- endif -%}

  {%- assign articleids = '' -%}
  {%- for rid in rids offset:rindex -%}
      {%- assign articleids = articleids | append:rid | append : "," -%}
  {%- endfor -%}

        {%- assign rsize = articleids | split:","  -%}

  {%- if rsize.size < no_post_display -%}
    {%- for rid in rids -%}
    {%- unless articleids contains rid -%}
    {%- assign articleids = articleids | append:rid | append : "," -%}
    {%- endunless -%}
    {%- endfor -%}
  {%- endif -%}

  {%- assign rsize = articleids | size | minus:1 -%}
  {%- assign articleids = articleids | slice: 0, rsize | split:"," -%}

  <div class="<?=$customRowClass?>">
    <?php echo $html_data['top_text']; ?>

  {%- assign count = 0 -%}
  {%- for aid in articleids -%}
    {%- assign rflag = "false" -%}
    {%- for blog in blog_handle_arr -%}
      {%- assign blog_temp = blog -%}

      {%- for article in blogs[blog_temp].articles -%}


        {%- if aid contains article.id -%}

          {%- assign ex_frmpool_tags = "<?php echo $ex_frmpool_tags; ?>" | split: "," -%}
          {%- assign ex_frmpool_main_flag = "true" -%}
          {%- for tag in ex_frmpool_tags -%}
          {%- assign ex_frmpool_ex_tags_temp = tag | strip -%}
          {%- if article.tags contains ex_frmpool_ex_tags_temp -%}
          {%- assign ex_frmpool_main_flag = "false" -%}
          {%- endif -%}
          {%- endfor -%}

            {%- if ex_frmpool_main_flag == "true" -%}

            {%- if count == no_post_display -%}
            {%- break -%}
            {%- endif -%}

              <div class="rb_blog-grid">

                <?php if($rel_data['enable_bimage'] == '1'){ ?>
                  {%- if article.image -%}

                    <a href="{{ article.url }}"><img src="{{ article.image.src | img_url: '<?php echo $rel_data['image_size'];?>' }}" alt="{{ article.title }}" /></a>

                  {%- else -%}
                    <div class="rb_no_image"></div>
                  {%- endif -%}
                <?php } ?>

                <h5 class="rb_title">
                  <a href='{{ article.url }}'>{{ article.title }}</a>
                </h5>

                <?php if ($rel_data['display_desc'] == 'Excerpt') { ?>
                {%- if article.excerpt != "" -%}
                  <div class="rb_contents">
                    <span>{{ article.excerpt | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?>, "" }}</span>
                  </div>
                  <a href="{{ article.url }}" class="rb_continue_button"><?php if($html_data['read_more_theme_2'] == ''){echo 'Read More';} else{echo $html_data['read_more_theme_2'];}?></a>
                {%- endif -%}
                <?php } else{ ?>
                  {%- if article.content != "" -%}
                    <div class="rb_contents">
                      <span>{{ article.content | strip_html | strip | truncate: <?php echo $html_data['no_char_post']; ?> }}</span>
                    </div>
                    <a href="{{ article.url }}" class="rb_continue_button"><?php if($html_data['read_more_theme_2'] == ''){echo 'Read More';} else{echo $html_data['read_more_theme_2'];}?></a>
                  {%- endif -%}
                <?php } ?>

              </div>
            {%- assign rflag = "true" -%}
            {%- assign count = count | plus:1 -%}
            {%- break -%}

            {%- endif -%}


        {%- endif -%}

      {%- endfor -%}
      {%- if rflag == "true" -%}
        {%- break -%}
      {%- endif -%}
    {%- endfor -%}
  {%- endfor -%}
  </div>

<?php } elseif ($rel_data['handle_rel_post'] == 2) { ?>
  <?php echo $html_data['top_text']; ?>
  <span><?php echo $rel_data['custom_msg']; ?></span>

  <?php
} else {

}
?>

{%- endif -%}

  <style>
  <?php if ($html_data['custom_style'] != "") { ?>
            <?php echo $html_data['custom_style']; ?>
            <?php } ?>
  .rb_blog-grid{
    float: left;
    width: 32%;
    margin-right: 2%;
  }
  #relatedblogs .rb_blog-grid:nth-of-type(3), #relatedblogs .rb_blog-grid:nth-of-type(6){margin-right:0px;}
  #relatedblogs .rb_blog-grid:nth-of-type(4), #relatedblogs .rb_blog-grid:nth-of-type(7) { clear:left }
    .rb_title a {
    color: #333333;
  }
  .rb_title a:hover {
    color: #333333;opacity: 1;
  }
  .rb_title{margin-top: 10px;}
  .blog_meta {
    color: #8c8b8b;
    line-height: 1.6em;
    font-size: 15px;
  }.blog_meta span {
    position: relative;
    display: inline-block;
    margin-right: 15px;
    font-size: smaller;
    color: #8c8b8b;
  }.blog_meta span a {
    color: #8c8b8b;
  }.rb_contents {
    line-height: 1.5;
    margin: 1em 0;
  }.rb_contents span {
    font-size: 15px;
    float: left;
    text-align: left;
  }
  .rb_continue_button {
    color: #333333;
    border-color: #333333;
    background-color: transparent;
    border: 1px solid #333333;
    transition: background-color 0.2s linear, color 0.2s linear;
    margin: 25px 0;
    padding: 0 20px;
    text-align: center;
    cursor: pointer;
    min-height: 42px;
    height: 40px;
    line-height: 1.2;
    vertical-align: top;
    font-weight: bold;
    font-size: 15px;
    display: inline-flex;
    -webkit-box-align: center;
    align-items: center;
    -webkit-box-pack: center;text-transform: uppercase;
    letter-spacing: 1px;
  }.rb_continue_button:hover {
    border-color: #333333;
    background-color: #333333;
    color: #fff;
    opacity: 1;
  }
  @media(max-width: 800px) {
    .rb_blog-grid{width:100%;
    marign-right:0px;}
  }

  /* .rb_row {
	display: -ms-flexbox;
	display: flex;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	margin-right: -15px;
	margin-left: -15px;
} */
</style>

</div>
<?php } ?>
{%- endif -%}
{%- endif -%}
{%- endunless -%}
