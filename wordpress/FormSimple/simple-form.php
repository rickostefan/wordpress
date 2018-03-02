<?php
/*
Plugins Name : Form simple
Plugins URI : https://www.rickostefan.com/
Description: -
Version: 1.0
Author: Ricko
*/

function wordpress_form_custom() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>';
	echo 'Name (required) <br/>';
	echo '<input type="text" name="contact-name" value="' . ( isset( $_POST["contact-name"] ) ? esc_attr( $_POST["contact-name"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Email (required) <br/>';
	echo '<input type="email" name="contact-email" value="' . ( isset( $_POST["contact-email"] ) ? esc_attr( $_POST["contact-email"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Message (required) <br/>';
	echo '<textarea rows="10" cols="35" name="contact-message">' . ( isset( $_POST["contact-message"] ) ? esc_attr( $_POST["contact-message"] ) : '' ) . '</textarea>';
	echo '</p>';
	echo '<p><input type="submit" name="contact-submitted" value="Send"></p>';
	echo '</form>';
}

function deliver_mail() {

	// if the submit button is clicked, send the email
	if ( isset( $_POST['contact-submitted'] ) ) {

		// sanitize form values
		$name    = sanitize_text_field( $_POST["contact-name"] );
		$email   = sanitize_email( $_POST["contact-email"] );
		$subject = "subject";
		$message = esc_textarea( $_POST["contact-message"] );

		// get the blog administrator's email address
		$to = get_option( 'admin_email' );

		$headers = "From: $name <$email>" . "\r\n";

		// If email has been process for sending, display a success message
		if ( wp_mail( $to, $subject, $message, $headers ) ) {
			echo '<div>';
			echo '<p>Makasih udah menghubungi kami.</p>';
			echo '</div>';
		} else {
			echo 'Etss ada error!!!';
		}
	}
}

function form_shortcode() {
	ob_start();
	deliver_mail();
	wordpress_form_custom();

	return ob_get_clean();
}

add_shortcode( 'ricko_contact_form', 'form_shortcode' );

?>