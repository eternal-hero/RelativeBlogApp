/////////////////////////////////////////Related blog setting/////////////////////////////////////////////

// var apidomain = 'https://8bdf-49-249-47-126.in.ngrok.io/related-blog-post-shopify-public-app';
// var pre_custom = null;

// $.ajax({
//     url: `${apidomain}/get-user-details/${Shopify.shop}`,
//     method: 'POST',
//     success: function(response) {
//     	console.log(response);
//     }
// });


$(document).ready(function () {


    var apidomain = 'https://www.digitaldarts.com.au/relatedposts';
    //console.log($(location).attr('hostname'));
    var domain_name = Shopify.shop;
    console.log(Shopify.shop);
    //////////////////// Get the article list same tag type ,random article and asc data article related dashboard setting //////////////////////
    
    $.ajax({
        
        url: `${apidomain}/admin/article.php`,
        method: 'POST',
        data: { domain: Shopify.shop, article_value: $('#html_value').val(),art_handle:$('#article_handle').val() },
        success: function (response) {
            var dataReturned = $.parseJSON(response);
            console.log(dataReturned);
            var handle = $.parseJSON(response);
            if(handle.length == 0){
                var html ="<p style='color:red'>Please select the Blogs to display on</p>";
                $('#select_blog').html(html);
                $('.article-template').hide();
            }else{
            var html = '';
            for (i = 0; i < dataReturned.length; i++) {
                
                if (dataReturned[i].a_author && dataReturned[i].content && dataReturned[i].a_tags) {
                    if (dataReturned[i].image) {
                        html = html + '<div class="artical-img-sec"><a href="https://' + domain_name + '/blogs/'+ dataReturned[i].b_handle.replace(/\s+/g, '-') +'/' + dataReturned[i].a_handle + '"><img hspace="10" src="' + dataReturned[i].image + '"></a>' + '<p style="font-weight: 600;margin-bottom: 0;">' + dataReturned[i].a_author + '</p>' + '<p style="margin: 0;">' + dataReturned[i].content + '</p>' + '<p style="margin: 0;">' + dataReturned[i].a_tags + '</p></div>';
                    } else {
                        html = html + '<div><p style="font-weight: 600;margin-bottom: 0;">' + dataReturned[i].a_author + '</p>' + '<p style="margin: 0;">' + dataReturned[i].content + '</p>' + '<p style="margin: 0;">' + dataReturned[i].a_tags + '</p></div>';
                    }

                } else if (dataReturned[i].content) {
                    if (dataReturned[i].image) {
                        html = html + '<div class="artical-img-sec"><a href="https://' + domain_name + '/blogs/'+ dataReturned[i].b_handle.replace(/\s+/g, '-') +'/' + dataReturned[i].a_handle + '"><img hspace="10" src="' + dataReturned[i].image + '" ></a>' + '<p style="margin: 0;">' + dataReturned[i].content + '</p></div>';
                    } else {
                        html = html + '<div class="artical-img-sec"><p style="margin: 0;">' + dataReturned[i].content + '</p></div>';
                    }

                } else if (dataReturned[i].a_tags) {
                    if (dataReturned[i].image) {
                        html = html + '<div class="artical-img-sec"><a href="https://' + domain_name + '/blogs/'+ dataReturned[i].b_handle.replace(/\s+/g, '-') +'/' + dataReturned[i].a_handle + '"><img hspace="10" src="' + dataReturned[i].image + '" ></a>' + '<p style="margin: 0;">' + dataReturned[i].a_tags + '</p></div>';
                    } else {
                        html = html + '<div class="artical-img-sec">' + '<p style="margin: 0;">' + dataReturned[i].a_tags + '</p></div>';
                    }

                } else if (dataReturned[i].a_author) {
                    if (dataReturned[i].image) {
                        html = html + '<div class="artical-img-sec"><a href="https://' + domain_name + '/blogs/'+ dataReturned[i].b_handle.replace(/\s+/g, '-') +'/' + dataReturned[i].a_handle + '"><img hspace="10" src="' + dataReturned[i].image + '" ></a>' + '<p style="font-weight: 600;margin-bottom: 0;">' + dataReturned[i].a_author + '</p></div>';
                    } else {
                        html = html + '<div class="artical-img-sec"><p style="margin: 0;">' + dataReturned[i].a_author + '</p></div>';
                    }

                }
            }
            $('#article_list').html(html);
            $('.article-template').hide();

        }
        }
    });
});




