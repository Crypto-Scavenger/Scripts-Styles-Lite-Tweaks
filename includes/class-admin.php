<?php
/**
 * Admin interface for Scripts & Styles Lite Tweaks
 *
 * @package ScriptsStylesLiteTweaks
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin interface
 */
class SSLT_Admin {

	/**
	 * Database instance
	 *
	 * @var SSLT_Database
	 */
	private $database;

	/**
	 * Settings cache
	 *
	 * @var array|null
	 */
	private $settings = null;

	/**
	 * Constructor
	 *
	 * @param SSLT_Database $database Database instance
	 */
	public function __construct( $database ) {
		$this->database = $database;
		
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_post_sslt_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Get settings (lazy loading)
	 *
	 * @return array Settings
	 */
	private function get_settings() {
		if ( null === $this->settings ) {
			$this->settings = $this->database->get_all_settings();
		}
		return $this->settings;
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Scripts & Styles Lite Tweaks', 'scripts-styles-lite-tweaks' ),
			__( 'Scripts & Styles', 'scripts-styles-lite-tweaks' ),
			'manage_options',
			'scripts-styles-lite-tweaks',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'tools_page_scripts-styles-lite-tweaks' !== $hook ) {
			return;
		}
		
		wp_enqueue_style(
			'sslt-admin',
			SSLT_URL . 'assets/admin.css',
			array(),
			SSLT_VERSION
		);
		
		wp_enqueue_script(
			'sslt-admin',
			SSLT_URL . 'assets/admin.js',
			array( 'jquery' ),
			SSLT_VERSION,
			true
		);
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', 'scripts-styles-lite-tweaks' ) );
		}
		
		$settings = $this->get_settings();
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved successfully.', 'scripts-styles-lite-tweaks' ); ?></p>
				</div>
			<?php endif; ?>
			
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'sslt_save_settings', 'sslt_nonce' ); ?>
				<input type="hidden" name="action" value="sslt_save_settings">
				
				<table class="form-table sslt-settings-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="disable_jquery_migrate">
									<?php esc_html_e( 'Disable jQuery Migrate', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_jquery_migrate" 
										name="disable_jquery_migrate"
										value="1"
										<?php checked( '1', $settings['disable_jquery_migrate'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove jQuery Migrate compatibility script for older code. Only disable if your theme/plugins don\'t need it.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_emoji_scripts">
									<?php esc_html_e( 'Disable Emoji Scripts', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_emoji_scripts" 
										name="disable_emoji_scripts"
										value="1"
										<?php checked( '1', $settings['disable_emoji_scripts'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove WordPress emoji detection scripts. Modern browsers support emojis natively.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_embeds">
									<?php esc_html_e( 'Disable WordPress Embeds', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_embeds" 
										name="disable_embeds"
										value="1"
										<?php checked( '1', $settings['disable_embeds'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove automatic embedding of external content scripts.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_admin_bar_scripts">
									<?php esc_html_e( 'Disable Admin Bar Scripts (Frontend)', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_admin_bar_scripts" 
										name="disable_admin_bar_scripts"
										value="1"
										<?php checked( '1', $settings['disable_admin_bar_scripts'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove admin bar scripts from frontend for non-logged users.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_dashicons">
									<?php esc_html_e( 'Disable Dashicons', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_dashicons" 
										name="disable_dashicons"
										value="1"
										<?php checked( '1', $settings['disable_dashicons'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove WordPress admin icons from frontend for non-logged users.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="enable_selective_blocks">
									<?php esc_html_e( 'Enable Selective Block Loading', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="enable_selective_blocks" 
										name="enable_selective_blocks"
										value="1"
										<?php checked( '1', $settings['enable_selective_blocks'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Only load CSS styles for Gutenberg blocks that are actually present on each page. Analyzes page content and only includes necessary block styles.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_global_styles">
									<?php esc_html_e( 'Disable Global Styles', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_global_styles" 
										name="disable_global_styles"
										value="1"
										<?php checked( '1', $settings['disable_global_styles'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove WordPress\'s default CSS for block editor global styles.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_classic_theme_styles">
									<?php esc_html_e( 'Disable Classic Theme Styles', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_classic_theme_styles" 
										name="disable_classic_theme_styles"
										value="1"
										<?php checked( '1', $settings['disable_classic_theme_styles'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove backward compatibility CSS for classic themes.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="disable_recent_comments_style">
									<?php esc_html_e( 'Disable Recent Comments Style', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="disable_recent_comments_style" 
										name="disable_recent_comments_style"
										value="1"
										<?php checked( '1', $settings['disable_recent_comments_style'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove default styling for recent comments widget.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<label for="cleanup_on_uninstall">
									<?php esc_html_e( 'Cleanup on Uninstall', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="cleanup_on_uninstall" 
										name="cleanup_on_uninstall"
										value="1"
										<?php checked( '1', $settings['cleanup_on_uninstall'] ?? '0' ); ?>
									/>
									<?php esc_html_e( 'Remove all plugin data when uninstalling.', 'scripts-styles-lite-tweaks' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Save settings
	 */
	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', 'scripts-styles-lite-tweaks' ) );
		}
		
		if ( ! isset( $_POST['sslt_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sslt_nonce'] ) ), 'sslt_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed', 'scripts-styles-lite-tweaks' ) );
		}
		
		$settings = array(
			'disable_jquery_migrate' => isset( $_POST['disable_jquery_migrate'] ) ? '1' : '0',
			'disable_emoji_scripts' => isset( $_POST['disable_emoji_scripts'] ) ? '1' : '0',
			'disable_embeds' => isset( $_POST['disable_embeds'] ) ? '1' : '0',
			'disable_admin_bar_scripts' => isset( $_POST['disable_admin_bar_scripts'] ) ? '1' : '0',
			'disable_dashicons' => isset( $_POST['disable_dashicons'] ) ? '1' : '0',
			'enable_selective_blocks' => isset( $_POST['enable_selective_blocks'] ) ? '1' : '0',
			'disable_global_styles' => isset( $_POST['disable_global_styles'] ) ? '1' : '0',
			'disable_classic_theme_styles' => isset( $_POST['disable_classic_theme_styles'] ) ? '1' : '0',
			'disable_recent_comments_style' => isset( $_POST['disable_recent_comments_style'] ) ? '1' : '0',
			'cleanup_on_uninstall' => isset( $_POST['cleanup_on_uninstall'] ) ? '1' : '0',
		);
		
		foreach ( $settings as $key => $value ) {
			$result = $this->database->save_setting( $key, $value );
			if ( false === $result ) {
				wp_die( esc_html__( 'Failed to save settings', 'scripts-styles-lite-tweaks' ) );
			}
		}
		
		wp_safe_redirect( add_query_arg(
			array(
				'page' => 'scripts-styles-lite-tweaks',
				'settings-updated' => 'true',
			),
			admin_url( 'tools.php' )
		) );
		exit;
	}
}