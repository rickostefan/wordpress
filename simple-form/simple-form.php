<?php
/**
 * Plugin Name:       Form Simple
 * Description:       Form
 * Version:           3.0.0
 * Author:            Ricko
 * Author URI:        http://www.rickostefan.com
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if (!class_exists( 'ContactFormClass' )) 
{
	class ContactFormClass {
		protected static $instance = null;
        /**
			 * Returns a single instance of the object (singleton).
			 *
			 * @since 1.0.0
			 * 
			 * @access public
			 * 
			 * @return object
			 */
        public static function instance() 
        {
        	if (null == self::$instance) 
        		{
        			self::$instance = new self();
        		}
        		return self::$instance;
        	}
        /**
		 * Construct the admin object.
		 *
		 * @since 1.0.0
		 * 
		 * @access public
		 */
        public function __construct() 
        {
        	add_shortcode( 'contact_add_form', array($this, 'contact_form_shortcode' ));
        	add_action( 'admin_menu', array($this, 'contact_admin_menu' ));
        	register_activation_hook(__FILE__, array($this, 'contact_database_add'));
        }

        /*public function get_charset_collate() {
		    $charset_collate = '';
		 
		    if ( ! empty( $this->charset ) )
		        $charset_collate = "DEFAULT CHARACTER SET $this->charset";
		    if ( ! empty( $this->collate ) )
		        $charset_collate .= " COLLATE $this->collate";
		 
		    return $charset_collate;
		}*/

        /**
         * Create database
         * 
         * @return 	-
         *
         * @source https://developer.wordpress.org/reference/functions/dbdelta/
         */
        protected function contact_database_add() 
        {
        	global $wpdb;

        	$table_name = $wpdb->prefix . "contact";
        	$wpdb_collate = $wpdb->collate;
        	$sql = "CREATE TABLE $table_name (
        	`id` int(100) NOT NULL AUTO_INCREMENT,
        	`name` varchar(100) NOT NULL,
        	`email` varchar(50) DEFAULT NULL,
        	`contact` text NOT NULL,
        	PRIMARY KEY (`id`)
        )COLLATE $wpdb_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
        /**
         * Admin menu
         * 
         * @return 	array 	tab form data
         */
        public function contact_admin_menu() 
        {
        	add_menu_page('Contact Form', 'Contact Form', 'manage_options', 'contact_form_list',
        		array($this,'contact_form_list'));
        	add_submenu_page('contact_form_list', 'Add New Messages', 'Add New', 'manage_options', 'add_messages',
        		array($this,'add_messages'));
        }
        /**
         * Create form input
         * @return 	string email content
         */
        public function add_messages() {
        	if(!empty($_POST["input_name"])) $name = $_POST["input_name"];
        	if(!empty($_POST["input_email"])) $email = $_POST["input_email"];
        	if(!empty($_POST["input_messages"])) $messages = $_POST["input_messages"];
        	$message = '';
            // Add data contact form
        	if (isset($_POST['message_submit'])) 
        	{
        		if(empty($_POST["input_name"])) { $message = 'Please fill your name'; }
        		elseif(empty($_POST["input_email"])) { $message = 'Please fill your email'; }
        		elseif(empty($_POST["input_messages"])) { $message = 'Please fill your messages'; }
        		else{
        			global $wpdb;
        			$table_name = $wpdb->prefix . "messages";
        			$wpdb->insert(
        				$table_name,
                            array('name' => $name, 'email' => $email, 'messages' => $messages),
                            array('%s', '%s', '%s', '%s')
                        );
        			$message="Success add message";
                    //$to = get_option('contact_email');
                    $to = '<ricko@softwareseni.com>';
                    $subject = 'User Messages';

                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $headers[] = 'From: Ricko Stefan <ricko@softwareseni.com>';
                    $message_message .= 'Name: '. $name. "\r\n\r\n";
                    $message_message .= 'Message: '. $messages. "\r\n\r\n";
                    //sent email
                    wp_mail( $to, $subject, $message_message, $headers );

                    /*if (wp_mail ( $to, $subject, $message_message, $headers )) {
						echo "Email send. We can contacts you leter.";
					}else{
						echo "something went wrong to send email, please try again later.";
					}*/
                }
            }
            ?>
            <div class="wrap">
            	<h2>Contact Form</h2>
            	<?php if (isset($message)) echo '<p class="massages">' . $message . '</p>'; ?>
            	<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
            		<p>Name *  <br/>
            			<input type="text" name="input_name" value="<?php echo ( isset( $_POST["input_name"] ) ? esc_attr( $_POST["input_name"] ) : '' ); ?>" size="100" />
            		</p>
            		<p>Email * <br/>
            			<input type="email" name="input_email" value="<?php echo ( isset( $_POST["input_email"] ) ? esc_attr( $_POST["input_email"] ) : '' ); ?>" size="100" />
            		</p>
            		<p>Messages * <br/>
            			<textarea rows="50" cols="50" name="input_messages"><?php echo ( isset( $_POST["input_messages"] ) ? esc_attr( $_POST["input_messages"] ) : '' ); ?></textarea>
            		</p>
            		<p class="button"><input type="submit" name="message_submit" value="Send"></p>
            	</form>
            </div>
            <?php
        }
        /**
         * Create Wordpress Shortcode
         */
        public function contact_form_shortcode() 
        {
        	ob_start();
        	add_messages();
        	return ob_get_clean();
        }
        /**
         * Create table
         *
        * @since 1.0.0
     	* 
	 	* @access public
	 	* 
         */
	 	public function contact_form_list()
	 	{
	 		?>
	 		<div class="admin-form">
	 			<h2>Form Admin Page</h2>
	 			<div class="table-contact center">
	 				<div class="alignleft text">
	 					<a href="<?php echo admin_url('admin.php?page=add_messages'); ?>">Add New</a>
	 				</div>
	 				<br class="clear">
	 			</div>
	 			<?php
	 			global $wpdb;
	 			$table_name = $wpdb->prefix . "messages";
	 			$rows = $wpdb->get_results("SELECT id,name,email,messages from $table_name");
	 			?>
	 			<table class='list-table fixed striped'>
	 				<tr>
	 					<th class="name">Name</th>
	 					<th class="email">Email</th>
	 					<th class="messages">Messages</th>
	 				</tr>
	 				<?php foreach ((array)$rows as $rowss) { ?>
	 				<tr>
	 					<td class="name"><?php echo $rowss->name; ?></td>
	 					<td class="email"><?php echo $rowss->email; ?></td>
	 					<td class="messages"><?php echo $rowss->messages; ?></td>
	 				</tr>
	 				<?php } ?>
	 			</table>
	 		</div>
	 		<?php
	 	}
	 }
	}
	if (!function_exists('ContactFormClass')) 
	{
    /**
     * Call instance function
     *
     * @return instance value
     */
    function ricko_messages_class() 
    {
    	return ContactFormClass::instance();
    }
}
ricko_messages_class();


if (!class_exists( 'ContactFormWidget' )) 
{
	class ContactFormWidget extends WP_Widget 
	{
            /**
             * Register widget with WordPress.
             */
            function __construct() 
            {
            	parent::__construct(
            		'ContactFormWidget',
            		esc_html__( 'Messages', 'text_domain' ),
            		array( 'description' => esc_html__( 'Contact Form Widget', 'text_domain' ), )
            	);
            }
            /**
             * Show the form.
             *
             * @since 1.0.0
		     * 
			 * @access public
			 * 
		     * @return 	array 	items data
             */
            public function widget( $args, $instance ) 
            {
            	echo $args['before_widget'];
            	if ( ! empty( $instance['title'] ) ) 
            	{
            		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            	}
            	global $wpdb;
            	$table_name = $wpdb->prefix . "messages";
            	$form = $wpdb->get_results("SELECT name,email,messages from $table_name"); ?>
            	<table class='list-table fixed striped'>
            		<tr>
            			<th class="name">Name</th>
            			<th class="email">Email</th>
            			<th class="messages">Messages</th>
            		</tr>
            		<?php foreach ((array)$form as $forms) 
            		{ 
            			?>
            			<tr>";
            				<td class="name"><?php echo $forms->name; ?></td>
            				<td class="email"><?php echo $forms->email; ?></td>
            				<td class="messages"><?php echo $forms->messages; ?></td>
            			</tr>
            			<?php
            		}
            		?>
            	</table>
            	<?php echo $args['after_widget'];
            }
            /**
             * Back-end widget form.
             *
             * @since 1.0.0
		     *
			 * @access public
			 *
             */
            public function form( $instance ) 
            {
            	$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New Title', 'text_domain' );
            	?>
            	<p>
            		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title :', 'text_domain' ); ?></label>
            		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            	</p>
            	<?php 
            }
            /**
             * Sanitize widget form values as they are saved.
             * @since 1.0.0
		     *
			 * @access public
             *
             * @return array update title.
             */
            public function update( $new_instance, $old_instance )
            {
            	$instance = array();
            	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            	return $instance;
            }
        }
    }
    if (!function_exists( 'register_widget' ))
    {

    	function register_widget()
    	{
    		register_widget( 'ContactFormWidget' );
    	}
    	add_action( 'widgets_init', 'register_widget' );
    }

    ?>