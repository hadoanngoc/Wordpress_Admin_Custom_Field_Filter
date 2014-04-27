<?php

if(!class_exists('Admin_Custom_Field_Filter')){
	class Admin_Custom_Field_Filter{
		/**
         * __construct 
         * @author Ha Doan Ngoc <hadoanngoc@gmail.com>
         * @param array $cpt [description]
         */
        function __construct($cpt = array()){
            $this->cpt = $cpt;
            // Adding a Taxonomy Filter to Admin List for a Custom Post Type
            add_action( 'restrict_manage_posts', array($this,'admin_restrict_manage_posts' ));
			add_filter( 'parse_query',array($this,'admin_filter_parse_query' ));
        }
		
		/*
		 * Get all custom field values of all posts
		 */
		private function get_all_custom_fields_values($key = '', $type = 'post', $status = ''){
			global $wpdb;

			if( empty( $key ) )
				return;
			
			if($status != ''){
				$r = $wpdb->get_col( $wpdb->prepare( "
					SELECT distinct pm.meta_value FROM {$wpdb->postmeta} pm
					LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = '%s' 
					AND p.post_status = '%s' 
					AND p.post_type = '%s'
				", $key, $status, $type ) );
			} else {
				$r = $wpdb->get_col( $wpdb->prepare( "
					SELECT distinct pm.meta_value FROM {$wpdb->postmeta} pm
					LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = '%s'
					AND p.post_type = '%s'
				", $key, $type ) );
			}
				
			return $r;
		}
  
        /**
         * admin_restrict_manage_posts add the slelect dropdown per taxonomy
         * @author Ha Doan Ngoc <hadoanngoc@gmail.com>
         * @since 0.1
         * @return void
         */
        public function admin_restrict_manage_posts() {
            // only display these taxonomy filters on desired custom post_type listings
            global $typenow;
            $types = array_keys($this->cpt);
            if (in_array($typenow, $types)) {
                // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
                $filters = $this->cpt[$typenow];
                foreach ($filters as $custom_field) {
                    // retrieve the taxonomy object
					$values = $this->get_all_custom_fields_values($custom_field, $typenow);
					
                    // output html for taxonomy dropdown filter
                    echo "<select name='field_".strtolower($custom_field)."' id='field-".strtolower($custom_field)."' class='postform'>";
                    echo "<option value=''>" . __('Show All ' . $custom_field,'affiliatez') ."</option>";
                    foreach($values as $val){
						$selected = '';
						if(isset($_GET['field_' . strtolower($custom_field)]) && $_GET['field_' . strtolower($custom_field)] == $val){
							$selected = "selected='selected'";
						}
						
						echo "<option value='$val' $selected>".apply_filters('admin_custom_field_filter_value',$val,$typenow,$custom_field)."</option>";
					}
                    echo "</select>";
                }
            }
        }
         
        function admin_filter_parse_query( $query ) {
			// only display these taxonomy filters on desired custom post_type listings
            $types = array_keys($this->cpt);
			
			if( is_admin() AND in_array($query->query['post_type'], $types)) {
				
				$qv = &$query->query_vars;
				if(!$qv['meta_query']){
					$qv['meta_query'] = array();
				}
				foreach($_GET as $key => $val){
				
					if(strpos($key,'field_') !== false){
						
						$custom_field = substr($key, 6);
						$qv['meta_query'][] = array(
							'field' => 'store_id',
							'value' => $val,
							'compare' => '='
						);
					}
				}
			}
		}
	}
}


// Usage:
// new Tax_CTP_Filter(array('CUSTOM-POST-TYPE' => array('CUSTOM-FIELD-1','CUSTOM-FIELD-2')));