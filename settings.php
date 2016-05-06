<div class="wrap">
    <h2>Crypteron reCAPTCHA Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'crypteron-recaptcha-group' ); ?>
        <?php do_settings_sections( 'crypteron-recaptcha-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Site Key</th>
                <td><input type="text" name="crypteron_recaptcha_client_key" value="<?php echo esc_attr( get_option('crypteron_recaptcha_client_key') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Secret Key</th>
                <td><input type="text" name="crypteron_recaptcha_server_key" value="<?php echo esc_attr( get_option('crypteron_recaptcha_server_key') ); ?>" /></td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>