Wordpress_Admin_Custom_Field_Filter
===================================

A class to add a selectbox filter by custom field value in Custom Post Type listing page in admin

Usage
===================================
To add a select-box filter to any Post Type/Custom Post Type listing page, just create new object

  new Tax_CTP_Filter(array('CUSTOM-POST-TYPE' => array('CUSTOM-FIELD-1','CUSTOM-FIELD-2')));

in which, 'CUSTOM-POST-TYPE' is the name of the post type/custom post type you want to add
          'CUSTOM-FIELD-1', 'CUSTOM-FIELD-2' are the names of the custom fields you want to filter by. Add many custom fields as you want, each item will add new select-box
          
By default, items in the select-box filter will have Text value the same with it's Value. To modify this value, use filter

  add_filter('admin_custom_field_filter_value','custom_admin_custom_field_filter_value',10,3);
  
For examle

  function custom_admin_custom_field_filter_value($value,$custom_post_type,$custom_field){
		if($custom_post_type == 'custom-post-type' && $custom_field == 'custom-field'){
			$post = get_post($value);
			if($post){
				return $post->post_title;
			}
		}
		return $value;
	}
