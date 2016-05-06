/**
 * Created by yaronguez on 7/6/15.
 */
function recaptcha_callback()
{
    var buttons = [
        '#loginform #wp-submit',
        '#lostpasswordform #wp-submit',
        '#registerform #wp-submit',
        '#commentform #submit'
    ];

    for(var i=0; i<=buttons.length; i++)
    {
        if(jQuery(buttons[i]).length > 0)
        {
            jQuery(buttons[i]).show();
        }
    }
}