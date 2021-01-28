<?php
ini_set('display_errors', '1'); 
/*
 Plugin Name: MF mail poet users
Description: Add users to a list with the camponay name 
Version: Version 1
Author: Mario Flores
Author URI: http://mario-flores.com
License: Comercial
*/
function mf_mailpoet_menu(){
add_menu_page ( 'Mail poet user', 'Mail poet user', 'update_core', 'mail_poet_user', 'mail_poet_users'); 
}
add_action( 'admin_menu', 'mf_mailpoet_menu' );

function mail_poet_users(){ 
    global $wpdb;
    echo '<div class="alert alert-info">Add users in the imported list to the lists with the corresponding company name</div>'; 
    $empty_list = $wpdb->get_results("SELECT wp_wysija_list.name as 'list_name',  wp_wysija_user.cf_1 as 'company' FROM wp_wysija_user"
        . " LEFT JOIN wp_wysija_list ON  wp_wysija_list.name = wp_wysija_user.cf_1 "    
            . " where wp_wysija_list.name IS NULL "
            . " GROUP by wp_wysija_user.cf_1 "); 
    if(!empty($empty_list)){
         
        foreach($empty_list as $list){
            if(!is_null($list->company)){
                $dados = array(
                    'name' => $list->company, 
                    'namekey' => preg_replace('/^[A-Za-z?!]/', '', $list->company), 
                    'is_enabled' => 1, 
                    'created_at' => time()
                );  
                $wpdb->insert('wp_wysija_list', $dados); 
            }

        }
    }
    
    $user_list = $wpdb->get_results("SELECT wp_wysija_list.list_id,  wp_wysija_user.user_id FROM wp_wysija_user"
        . " LEFT JOIN wp_wysija_list ON  wp_wysija_list.name = wp_wysija_user.cf_1 " ); 

    foreach($user_list as $list){
    echo "user ".$list->user_id."<br />"; 
        $check = $wpdb->get_results("SELECT * FROM wp_wysija_user_list where list_id = ".$list->list_id." and user_id = ".$list->user_id); 
        if(!empty($check)){
            $dados = array(
                'list_id' => $list->list_id, 
                'user_id' => $list->user_id
            ); 
        } 
    }
    $group = $wpdb->get_results("SELECT * FROM wp_wysija_list where wp_wysija_list.name = 'imported'"); 
$list = $group[0]; 
    if(!empty($group)){
                $import_list = $wpdb->get_results("SELECT wp_wysija_list.list_id,  wp_wysija_user.user_id, wp_wysija_user_list.unsub_date 
FROM wp_wysija_user
JOIN wp_wysija_list ON  wp_wysija_list.name = wp_wysija_user.cf_1 
JOIN wp_wysija_user_list on wp_wysija_user_list.user_id = wp_wysija_user.user_id
WHERE wp_wysija_user_list.list_id = '".$list->list_id."'"); 
        
                foreach ($import_list as $list){
                    $dados = array(
                        'list_id' => $list->list_id, 
                        'user_id' => $list->user_id
                    );
                    $test = $wpdb->get_results("SELECT user_id FROM wp_wysija_user_list WHERE wp_wysija_user_list.list_id = ".$list->list_id." and wp_wysija_user_list.user_id = ".$list->user_id); 
                    if(empty($teste)){
                        $dados['sub_date'] = time(); 
                        $wpdb->insert('wp_wysija_user_list', $dados); 
                        
                    }

                }
    }

}