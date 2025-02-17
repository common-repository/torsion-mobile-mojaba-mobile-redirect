<?php
/**
 * Plugin Name: Mojaba for WordPress
 * Version: 1.2
 * Plugin URI: http://www.torsionmobile.com
 * Description: Detect if your site is visited by a mobile device user, and redirect to an appropriate location to serve content catering to mobile devices.
 * Author: Torsion Mobile <team@torsionmobile.com>
 * Author URI: http://www.torsionmobile.com
 * @package TorsionMobile
 */
// create custom plugin settings menu
add_action('admin_menu', 'mojaba_create_menu');
add_action('template_redirect', 'mojaba_mobile_detect');
add_action('wp_head', 'mojaba_clientside_detect');

function mojaba_post_slug() {
    $title = get_the_title();
    $separator = '-';

	// Remove all characters that are not the separator, letters, numbers, or whitespace
	$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));

	// Replace all separator characters and whitespace by a single separator
	$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

	// Trim separators from the beginning and end
	return trim($title, $separator);
}

function mojaba_mobile_detect() {
	$panel = '#feed-'.mojaba_post_slug();

	$mobile_site = get_option('mobile_site_url');
	$redirect = true;
	if(empty($_COOKIE['mojaba-redirect'])) {
		if(!empty($_GET['mojaba-redirect'])) { 
            $redirect = false; 
            setcookie('mojaba-redirect', $_GET['mojaba-redirect'], time() + 3600); 
		}
	} else { 
		$redirect = false; 
    }

	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(!empty($mobile_site) && $redirect && preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
		if(strpos($mobile_site, 'http://') === false) {
			$mobile_site = 'http://'.$mobile_site;
		}
		header('Location: '.$mobile_site."?pk_campaign=redirect-wp".$panel); die;
	}
}

function mojaba_clientside_detect() {
	$mobile_site = get_option('mobile_site_url');
	if(!empty($mobile_site)) {
		$code = <<<CODE
	<script type="text/javascript">
	var mojaba = {
		domain: '{$mobile_site}',
		fullSiteDelayMinutes: 60
	}
	</script>
	<script type="text/javascript" src="https://c776917.ssl.cf2.rackcdn.com/includes/mojaba-mobile-redirect.js"></script>
CODE;

	    echo $code;
	}
}
 
function mojaba_create_menu() {
    add_menu_page('Mojaba Plugin Settings', 'Mojaba Settings', 'administrator', __FILE__, 'mojaba_settings_page');

	add_action( 'admin_init', 'register_mojaba_settings' );
}


function register_mojaba_settings() {
	register_setting( 'mojaba-settings-group', 'mobile_site_url' );
}

function mojaba_settings_page() {
?>
<div class="wrap">
<h2>Torsion Mobile - Mojaba Mobile Redirect</h2>

<?php
    if(isset($_GET['settings-updated']) && $_GET['settings-updated']) {
?>
<div id="settings_updated" class="updated"> 
    <p><strong>Settings saved.</strong></p>
</div>
<?php
    }
?>

<form method="post" action="options.php">
    <?php settings_fields( 'mojaba-settings-group' ); ?>
    <?php do_settings_sections( 'mojaba-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Mojaba Mobile Site URL</th>
		<?php
		$url = get_option('mobile_site_url');
		if(empty($url)) {
			$url = 'http://';
		}
		?>
        <td><input type="text" name="mobile_site_url" value="<?php echo $url; ?>" size="60"/></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>
