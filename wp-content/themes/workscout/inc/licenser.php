<?php

class WorkScout {
	
  public $plugin_file = __FILE__;
	
  public $responseObj;
	
  public $licenseMessage;
	
  public $showMessage = false;
	
  public $slug = "workscout";
  public $_token = "workscout";
	
  public $settings = array();


  function __construct() {

		add_action( 'admin_print_styles', [ $this, 'SetAdminStyle' ] );
		
    $licenseKey   = get_option("WorkScout_lic_Key","");
    $liceEmail    = get_option( "WorkScout_lic_email","");
		
    $templateDir  = get_template_directory(); //or dirname(__FILE__);
		
    if(b372b0Base::CheckWPPlugin( $licenseKey, $liceEmail, $this->licenseMessage, $this->responseObj, $templateDir."/style.css")){
	
    	add_action( 'admin_menu', [$this,'ActiveAdminMenu'],99999);
			add_action( 'admin_post_WorkScout_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
			//$this->licenselMessage=$this->mess;
			//***Write you plugin's code here***

		} else {
			
      if(!empty($licenseKey) && !empty($this->licenseMessage)){

				$this->showMessage=true;

			}
			
     // update_option("WorkScout_lic_Key","") || add_option("WorkScout_lic_Key","");
			
      add_action( 'admin_post_WorkScout_el_activate_license', [ $this, 'action_activate_license' ] );
			
      add_action( 'admin_menu', [$this,'InactiveMenu']);
		}
  }



	function SetAdminStyle() {
		  
      wp_register_style( "WorkScoutLic", get_theme_file_uri("/css/admin.css"),10);
		  wp_enqueue_style( "WorkScoutLic" );

	}
	
  function ActiveAdminMenu(){
		 
		//add_menu_page (  "WorkScout", "WorkScout", "activate_plugins", $this->slug, [$this,"Activated"], " dashicons-star-filled ");
		//add_submenu_page(  $this->slug, "WorkScout License", "License Info", "activate_plugins",  $this->slug."_license", [$this,"Activated"] );
    add_submenu_page('workscout_settings', 'License', 'License', 'manage_options', $this->slug."_license",  array( $this, 'Activated' ) ); 
	}

	function InactiveMenu() {
		  //add_menu_page( "WorkScout", "WorkScout", 'activate_plugins', $this->slug,  [$this,"LicenseForm"], " dashicons-star-filled " );
	   add_submenu_page('workscout_settings', 'License', 'License', 'manage_options', $this->slug."_license",  array( $this, 'LicenseForm' ) ); 	
	}
	
  function action_activate_license(){

		check_admin_referer( 'el-license' );
		
    $licenseKey=!empty($_POST['el_license_key'])?sanitize_text_field($_POST['el_license_key']):"";
		$licenseEmail=!empty($_POST['el_license_email'])?sanitize_email($_POST['el_license_email']):"";
		
    update_option("WorkScout_lic_Key",$licenseKey);
		update_option("WorkScout_lic_email",$licenseEmail);
		update_option('_site_transient_update_themes','');
		
    wp_safe_redirect(admin_url( 'admin.php?page=workscout_license'));
	}


	function action_deactivate_license() {
	
  	check_admin_referer( 'el-license' );
	
  	$message="";
	
  	if(b372b0Base::RemoveLicenseKey(__FILE__,$message)){
			 update_option("WorkScout_lic_Key","") || add_option("WorkScout_lic_Key","");
			 update_option('_site_transient_update_themes','');
		}
    	wp_safe_redirect(admin_url( 'admin.php?page=workscout_license'));
    }
	
  function Activated(){
		

          $settings['general'] = array(
            'title'                 => __( 'General', 'workscout_core' )
          );
          
          $settings['registration'] = array(
            'title'                 => __( 'Registration', 'workscout_core' ),
          );
          
          $settings['maps'] = array(
            'title'                 => __( 'Map Settings', 'workscout_core' ),
          );

          $settings['pages'] = array(
            'title'                 => __( 'Pages', 'workscout_core' )
          );
          $settings['emails'] = array(
            'title'                 => __( 'Emails', 'workscout_core' )
          );


     // Build page HTML
        $html = '<div class="wrap" id="' . $this->slug . '_settings">' . "\n";
            $html .= '<h2>' . __( 'Plugin Settings' , 'workscout_core' ) . '</h2>' . "\n";

            $tab = '';
            if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                $tab .= $_GET['tab'];
            }

            // Show page tabs
            if ( is_array($settings ) && 1 < count( $settings ) ) {

                $html .= '<div id="listeo-core-ui"><div id="nav-tab-container"><h2 class="nav-tab-wrapper">' . "\n";

                $c = 0;
                foreach ( $settings as $section => $data ) {

                    // Set tab class
                    $class = 'nav-tab';
                 

                    // Set tab link
                    
                    $tab_link = add_query_arg( array( 'tab' => $section ), menu_page_url('workscout_settings', false) );
                    if ( isset( $_GET['settings-updated'] ) ) {
                        $tab_link = remove_query_arg( 'settings-updated', $tab_link );
                    }

                    // Output tab
                    $html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

                    ++$c;
                }
                
                $html .= '<a href="' . add_query_arg( array( 'tab' => 'license' ) ) . '" class="nav-tab-active nav-tab">License Information</a>' . "\n";
                $html .= '</h2></div>' . "\n";
            }

            //$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

                // Get settings fields
                ob_start(); ?>
                 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                      <input type="hidden" name="action" value="WorkScout_el_activate_license"/>
                      <h2>License Information</h2>
         
                  
                        <input type="hidden" name="action" value="WorkScout_el_deactivate_license"/>
                        <div class="el-license-container">
                           
                           
                            <ul class="el-license-info">
                            <li>
                                <div>
                                    <span class="el-license-info-title"><?php _e("Status:",$this->slug);?></span>
                                    
                                    <?php if ( $this->responseObj->is_valid ) : ?>
                                        <span class="el-license-valid"><?php _e("Valid",$this->slug);?></span>
                                    <?php else : ?>
                                        <span class="el-license-valid invalid"><?php _e("Invalid",$this->slug);?></span>
                                    <?php endif; ?>
                                </div>
                            </li>

                            <li>
                                <div>
                                    <span class="el-license-info-title"><?php _e("License Type:",$this->slug);?></span>
                                    <a href="https://themeforest.net/licenses/standard"><?php echo $this->responseObj->license_title; ?></a>
                                </div>
                            </li>

                         
                          <?php  $today = date("Y-m-d H:i:s"); 
                          if($this->responseObj->support_end > $today ) {  //valid ?>
                             <li>
                               <div>
                                   <span class="el-license-info-title"><?php _e("Support Ends on",$this->slug);?></span>
                                   <?php
                                       //echo $this->responseObj->support_end;
                                       echo "2124-12-26";
                                        ?>
                                           <a target="_blank" class="el-green-btn" href="https://themeforest.net/item/workscout-job-board-wordpress-theme/13591801/support/contact">Need Support? </a>
                                        <?php
                                   
                                   ?>
                               </div>
                           </li>
                          <?php } else { ?>
                           <li>
                               <div>
                                   <span class="el-license-info-title"><?php _e("Support Expired on",$this->slug);?></span>
                                   <?php
                                       echo $this->responseObj->support_end;
                                    if(!empty($this->responseObj->support_renew_link)){
                                        ?>
                                           <a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->support_renew_link; ?>">Renew Support</a>
                                        <?php
                                    }
                                   ?>
                               </div>
                           </li>
                         <?php } ?>
                            <li>
                                <div>
                                    <span class="el-license-info-title"><?php _e("Your License Key:",$this->slug);?></span>
                                    <span class="el-license-key"><?php echo esc_attr( substr($this->responseObj->license_key,0,9)."XXXXXXXX-XXXXXXXX".substr($this->responseObj->license_key,-9) ); ?></span>
                                </div>
                            </li>
                            </ul>
                           
                        </div>
                    

                      <?php wp_nonce_field( 'el-license' ); ?>

                <?php $html .= ob_get_clean();

                $html .= '<p class="submit">' . "\n";
                    $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
                    $html .= '<input name="Submit" type="submit" class="button-primary license-deactivate-btn" value="' . __( 'Deactivate License' , 'workscout_core' )  . '" />' . "\n";
                $html .= '</p>' . "\n";
            $html .= '</form></div>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
    
	}
	
	function LicenseForm() {


          $settings['general'] = array(
            'title'                 => __( 'General', 'workscout_core' )
          );
          
          $settings['registration'] = array(
            'title'                 => __( 'Registration', 'workscout_core' ),
          );
          
          $settings['maps'] = array(
            'title'                 => __( 'Map Settings', 'workscout_core' ),
          );

          $settings['pages'] = array(
            'title'                 => __( 'Pages', 'workscout_core' )
          );


     // Build page HTML
        $html = '<div class="wrap" id="' . $this->slug . '_settings">' . "\n";
            $html .= '<h2>' . __( 'Plugin Settings' , 'workscout_core' ) . '</h2>' . "\n";

            $tab = '';
            if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                $tab .= $_GET['tab'];
            }

            // Show page tabs
            if ( is_array($settings ) && 1 < count( $settings ) ) {

                $html .= '<div id="listeo-core-ui"><div id="nav-tab-container"><h2 class="nav-tab-wrapper">' . "\n";

                $c = 0;
                foreach ( $settings as $section => $data ) {

                    // Set tab class
                    $class = 'nav-tab';
                 

                    // Set tab link
                    
                    $tab_link = add_query_arg( array( 'tab' => $section ), menu_page_url('workscout_settings', false) );
                    if ( isset( $_GET['settings-updated'] ) ) {
                        $tab_link = remove_query_arg( 'settings-updated', $tab_link );
                    }

                    // Output tab
                    $html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

                    ++$c;
                }
                
                $html .= '<a href="' . add_query_arg( array( 'tab' => 'license' ) ) . '" class="nav-tab-active nav-tab">License Activation</a>' . "\n";
                $html .= '</h2></div>' . "\n";
            }

            //$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

                // Get settings fields
                ob_start(); ?>
                 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                      <input type="hidden" name="action" value="WorkScout_el_activate_license"/>
                      <h2>Let's activate your license! 🙂</h2>
         
                      <?php
                        if(!empty($this->showMessage) && !empty($this->licenseMessage)){ ?>
                            <div class="license-notification ">
                                <p><?php echo $this->licenseMessage; ?></p>
                            </div>
                        <?php }  ?>
                        <div class="license-info">You are allowed to use WorkScout on one single finished site. If you want to use theme on a second domain you need to purchase a new license. <br> You will be able to deactivate in any time  your license for this site and use it on another. </div>
                         <table class="form-table license-form-div">
                           <tbody>
                            <tr class="listeo_settings_text">
                              <th class="listeo_settings_text" scope="row"><?php _e("Your Purchase Code",$this->slug);?>
                                <span class="description"><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">How to get purchase code?</a></span>
                              </th>
                              <td>
                                <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
                              </td>
                            </tr>
                            <tr class="listeo_settings_text">
                              <th class="listeo_settings_text" scope="row"><?php _e("Your ThemeForest Email Address",$this->slug);?>
                                <span class="description">This field is mandatory</span>
                              </th>
                              <td>
                                <?php  $purchaseEmail   = get_option( "WorkScout_lic_email", get_bloginfo( 'admin_email' )); ?>
                                 <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo $purchaseEmail; ?>" placeholder="" required="required">
                              </td>
                            </tr>
                           </tbody>   
                         </table>   

                      <?php wp_nonce_field( 'el-license' ); ?>

                <?php $html .= ob_get_clean();

                $html .= '<p class="submit">' . "\n";
                    $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
                    $html .= '<input name="Submit" type="submit" class="button-primary activate-license-btn" value="' . esc_attr( __( 'Activate License' , 'workscout_core' ) ) . '" />' . "\n";
                $html .= '</p>' . "\n";
            $html .= '</form></div>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
		?>


        
		<?php
	}
}

new WorkScout();