<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Optional, admin-triggered Bangla destination seeding (brief: "Use
 * professional Bengali names" for the 21 core destinations, plus a
 * featured Sylhet tea-garden collection). Never runs automatically —
 * only from the button on Settings → Tripdesh — and is idempotent: any
 * destination whose slug already exists is left untouched, so running it
 * twice (or on a site that already has real content) never overwrites or
 * duplicates anything.
 */
class Tripdesh_Demo_Content {

	const ACTION = 'tripdesh_import_demo_content';

	/**
	 * slug => [ Bengali title, short Bengali description, tea_garden flag ]
	 */
	public static function destinations(): array {
		return array(
			'coxs-bazar'  => array( 'কক্সবাজার', 'বিশ্বের দীর্ঘতম প্রাকৃতিক সমুদ্রসৈকত — বাংলাদেশের সবচেয়ে জনপ্রিয় সমুদ্র ভ্রমণ গন্তব্য।', false ),
			'sylhet'      => array( 'সিলেট', 'সবুজ চা-বাগান, পাহাড় আর ঝর্নার শহর — প্রকৃতিপ্রেমীদের প্রিয় গন্তব্য।', true ),
			'srimangal'   => array( 'শ্রীমঙ্গল', 'বাংলাদেশের চা-রাজধানী, বিস্তীর্ণ চা-বাগান ও লাউয়াছড়া জাতীয় উদ্যানের জন্য বিখ্যাত।', true ),
			'jaflong'     => array( 'জাফলং', 'পাহাড়, নদী আর পাথরের অপূর্ব মিলনস্থল, সিলেটের অন্যতম দর্শনীয় স্থান।', true ),
			'ratargul'    => array( 'রাতারগুল', 'বাংলাদেশের একমাত্র জলাবন — নৌকায় ঘুরে দেখার মতো এক অনন্য অভিজ্ঞতা।', true ),
			'sunamganj'   => array( 'সুনামগঞ্জ', 'হাওর অঞ্চলের প্রবেশদ্বার, টাঙ্গুয়ার হাওরে যাওয়ার প্রধান পথ।', false ),
			'tanguar-haor' => array( 'টাঙ্গুয়ার হাওর', 'বিশাল জলরাশি ও পরিযায়ী পাখির অভয়ারণ্য, নৌকা ভ্রমণের জন্য আদর্শ।', false ),
			'bandarban'   => array( 'বান্দরবান', 'উঁচু পাহাড়, মেঘের রাজ্য আর আদিবাসী সংস্কৃতির অপূর্ব সমন্বয়।', false ),
			'rangamati'   => array( 'রাঙামাটি', 'কাপ্তাই লেক ঘেরা পাহাড়ি শহর, নৌ ভ্রমণ ও ঝুলন্ত সেতুর জন্য বিখ্যাত।', false ),
			'khagrachari' => array( 'খাগড়াছড়ি', 'আলুটিলা গুহা ও রিছাং ঝর্নাসহ পার্বত্য চট্টগ্রামের মনোরম এক জেলা।', false ),
			'sajek'       => array( 'সাজেক ভ্যালি', '"বাংলাদেশের ছাদ" নামে পরিচিত — মেঘের সাগরে ঢাকা পাহাড়ি জনপদ।', false ),
			'chittagong'  => array( 'চট্টগ্রাম', 'পাহাড়, নদী ও সমুদ্র বন্দরের শহর — বাণিজ্যিক রাজধানী ও প্রবেশদ্বার।', false ),
			'dhaka'       => array( 'ঢাকা', 'রাজধানী শহর — ঐতিহাসিক স্থাপনা, পুরান ঢাকার ঐতিহ্য ও আধুনিক জীবনের মিশেল।', false ),
			'rajshahi'    => array( 'রাজশাহী', 'পদ্মা নদীর তীরে সবুজ ও পরিচ্ছন্ন শহর, আমের জন্য বিখ্যাত।', false ),
			'paharpur'    => array( 'পাহাড়পুর', 'ইউনেস্কো ঐতিহ্যবাহী সোমপুর মহাবিহার — উপমহাদেশের প্রাচীন বৌদ্ধ বিহার।', false ),
			'kuakata'     => array( 'কুয়াকাটা', '"সাগরকন্যা" নামে পরিচিত — যেখানে একসাথে সূর্যোদয় ও সূর্যাস্ত দেখা যায়।', false ),
			'sundarbans'  => array( 'সুন্দরবন', 'বিশ্বের বৃহত্তম ম্যানগ্রোভ বন ও রয়েল বেঙ্গল টাইগারের আবাসস্থল।', false ),
			'barisal'     => array( 'বরিশাল', 'নদী ও খালের শহর — লঞ্চ ভ্রমণ ও ভাসমান পেয়ারা বাজারের জন্য বিখ্যাত।', false ),
			'mymensingh'  => array( 'ময়মনসিংহ', 'ব্রহ্মপুত্র নদের তীরে ঐতিহ্যবাহী শহর, প্রকৃতি ও ইতিহাসের সমন্বয়।', false ),
			'gazipur'     => array( 'গাজীপুর', 'ঢাকার কাছে সবুজ প্রকৃতি, রিসোর্ট ও ভাওয়াল জাতীয় উদ্যানের জন্য পরিচিত।', false ),
			'tangail'     => array( 'টাঙ্গাইল', 'তাঁতশিল্প ও ঐতিহ্যবাহী শাড়ির জন্য বিখ্যাত, মধুপুর বনের কাছাকাছি এক জেলা।', false ),
		);
	}

	public function __construct() {
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle_import' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	public static function render_button(): void {
		?>
		<h2><?php esc_html_e( 'Bangla Demo Content', 'tripdesh' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Creates the 21 core Bangladesh destinations (Bengali titles, English URL slugs) if they do not already exist, and tags the Sylhet tea-garden group for the homepage featured section. Safe to run more than once — existing destinations (matched by slug) are never modified or duplicated.', 'tripdesh' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( self::ACTION ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
			<?php submit_button( __( 'Import Bangla Demo Destinations', 'tripdesh' ), 'secondary' ); ?>
		</form>
		<?php
	}

	public function handle_import(): void {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( self::ACTION ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'tripdesh' ) );
		}

		$created = 0;
		foreach ( self::destinations() as $slug => $data ) {
			list( $title, $description, $tea_garden ) = $data;

			if ( get_page_by_path( $slug, OBJECT, 'destination' ) ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'destination',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $description,
					'post_status'  => 'publish',
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			if ( $tea_garden ) {
				update_post_meta( $post_id, '_tripdesh_featured_collection', 'tea_garden' );
			}

			++$created;
		}

		Tripdesh_Taxonomies::seed_default_terms();

		set_transient( 'tripdesh_demo_import_result', $created, 60 );

		wp_safe_redirect( wp_get_referer() ?: admin_url( 'options-general.php?page=tripdesh-settings' ) );
		exit;
	}

	public function admin_notice(): void {
		$result = get_transient( 'tripdesh_demo_import_result' );
		if ( false === $result ) {
			return;
		}
		delete_transient( 'tripdesh_demo_import_result' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			/* translators: %d: number of destinations created */
			esc_html( sprintf( _n( '%d destination created.', '%d destinations created.', (int) $result, 'tripdesh' ), (int) $result ) )
		);
	}
}
