<?php
/**
 * Plugin Name:       Form Simple
 * Description:       Form
 * Version:           3.0.0
 * Author:            Ricko
 * Author URI:        http://www.rickostefan.com
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function contact_form( $name, $email, $contact_message ) {
	//post email
	if(isset($_POST['submit']) && $_POST['submit'] == 'submit'){

		$name = $_POST['names'];
		$email = $_POST['email'];
		$contact_message = $_POST['contact_message'];
		$subject = "subject";
		$email_mail = '<ricko@softwareseni.com>';
		$message = "Name: " . $name . "\n\nEmail: " . $email . "\n\n" . "\n\nContact Message: " . $contact_message . "\n\n" . $_POST['message'];

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$headers[] = 'From: Ricko Stefan <ricko@softwareseni.com>';

		if (wp_mail ( $email_mail, $subject, $message, $headers )) {
			echo "Email send. We can contacts you leter.";
		}else{
			echo "something went wrong to send email, please try again later.";
		}

	}

	ob_start();

	?>

	<div class="contact_form">
		<div id="return" class="center"></div>
		<form method="post" action="">

			<div class="contact-outer">
				<div class="contact-box">

							<div class="contact-box">
								<div class="contact-inner half">
									<p class="small">* Mandatory fields. We respect your privacy.</p>
									<div class="contact-bio left">
										<input type="text" size="37" name="names" id="name" required placeholder="Name *"/>
										<input type="text" size="37" name="email" id="email" size="45" required placeholder="Email *"/>
										<input type="textarea" size="37" name="contact_message" id="contact_message" required placeholder="Contact Message *"/>
									</div>
									<label>&nbsp;</label><input name='submit' type="submit" class="btn" value="submit"><div class="clear"></div>
								</div><!-- end .contact-inner -->
							</div><!-- end .contact-box -->
				</form>
			</div>
			<script type="text/javascript">
				// for add js soon
				/*jQuery(document).ready(function(){
					$jQuery("#contactForm").validate();
				});*/
			</script>
			<?php
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		?>
		<?php add_shortcode( 'ricko_contact_form', 'contact_form' ); ?>