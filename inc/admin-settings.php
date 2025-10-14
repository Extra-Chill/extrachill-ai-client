<?php
/**
 * Network Admin Settings Page
 *
 * Centralized API key management for AI providers across the ExtraChill Platform.
 * Keys are stored network-wide and accessible to all plugins via the ai-http-client library.
 *
 * @package ExtraChillAIClient
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register network admin menu page
 */
function extrachill_ai_client_add_network_admin_menu() {
	add_submenu_page(
		'extrachill-multisite',
		__( 'AI Client Settings', 'extrachill-ai-client' ),
		__( 'AI Client', 'extrachill-ai-client' ),
		'manage_network_options',
		'extrachill-ai-client',
		'extrachill_ai_client_render_settings_page'
	);
}
add_action( 'network_admin_menu', 'extrachill_ai_client_add_network_admin_menu' );

/**
 * Render network admin settings page
 */
function extrachill_ai_client_render_settings_page() {
	// Check user capabilities
	if ( ! current_user_can( 'manage_network_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'extrachill-ai-client' ) );
	}

	// Handle form submission
	if ( isset( $_POST['extrachill_ai_client_save'] ) ) {
		check_admin_referer( 'extrachill_ai_client_settings' );

		// Get all API keys from form
		$api_keys = array(
			'openai'     => isset( $_POST['openai_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['openai_api_key'] ) ) : '',
			'anthropic'  => isset( $_POST['anthropic_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['anthropic_api_key'] ) ) : '',
			'gemini'     => isset( $_POST['gemini_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['gemini_api_key'] ) ) : '',
			'grok'       => isset( $_POST['grok_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['grok_api_key'] ) ) : '',
			'openrouter' => isset( $_POST['openrouter_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['openrouter_api_key'] ) ) : '',
		);

		// Save using library's filter
		apply_filters( 'ai_provider_api_keys', $api_keys );

		echo '<div class="notice notice-success"><p>' . esc_html__( 'API keys saved successfully.', 'extrachill-ai-client' ) . '</p></div>';
	}

	// Get current API keys
	$current_keys = apply_filters( 'ai_provider_api_keys', null );
	if ( ! is_array( $current_keys ) ) {
		$current_keys = array();
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<p><?php esc_html_e( 'Configure API keys for AI providers. These keys will be available network-wide to all plugins that use the AI HTTP Client library.', 'extrachill-ai-client' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'extrachill_ai_client_settings' ); ?>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="openai_api_key"><?php esc_html_e( 'OpenAI API Key', 'extrachill-ai-client' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="openai_api_key"
								name="openai_api_key"
								value="<?php echo esc_attr( $current_keys['openai'] ?? '' ); ?>"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'sk-...', 'extrachill-ai-client' ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Get your API key from https://platform.openai.com/api-keys', 'extrachill-ai-client' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="anthropic_api_key"><?php esc_html_e( 'Anthropic API Key', 'extrachill-ai-client' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="anthropic_api_key"
								name="anthropic_api_key"
								value="<?php echo esc_attr( $current_keys['anthropic'] ?? '' ); ?>"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'sk-ant-...', 'extrachill-ai-client' ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Get your API key from https://console.anthropic.com/settings/keys', 'extrachill-ai-client' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="gemini_api_key"><?php esc_html_e( 'Google Gemini API Key', 'extrachill-ai-client' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="gemini_api_key"
								name="gemini_api_key"
								value="<?php echo esc_attr( $current_keys['gemini'] ?? '' ); ?>"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'AIza...', 'extrachill-ai-client' ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Get your API key from https://aistudio.google.com/app/apikey', 'extrachill-ai-client' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="grok_api_key"><?php esc_html_e( 'Grok API Key', 'extrachill-ai-client' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="grok_api_key"
								name="grok_api_key"
								value="<?php echo esc_attr( $current_keys['grok'] ?? '' ); ?>"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'xai-...', 'extrachill-ai-client' ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Get your API key from https://console.x.ai/', 'extrachill-ai-client' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="openrouter_api_key"><?php esc_html_e( 'OpenRouter API Key', 'extrachill-ai-client' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="openrouter_api_key"
								name="openrouter_api_key"
								value="<?php echo esc_attr( $current_keys['openrouter'] ?? '' ); ?>"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'sk-or-...', 'extrachill-ai-client' ); ?>"
							/>
							<p class="description"><?php esc_html_e( 'Get your API key from https://openrouter.ai/keys', 'extrachill-ai-client' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button( __( 'Save API Keys', 'extrachill-ai-client' ), 'primary', 'extrachill_ai_client_save' ); ?>
		</form>

		<hr>

		<h2><?php esc_html_e( 'Usage', 'extrachill-ai-client' ); ?></h2>
		<p><?php esc_html_e( 'API keys configured here are available to all plugins across the network. Individual plugins can choose which provider and model to use in their own settings.', 'extrachill-ai-client' ); ?></p>

		<h3><?php esc_html_e( 'For Plugin Developers', 'extrachill-ai-client' ); ?></h3>
		<p><?php esc_html_e( 'Use the AI HTTP Client library to make requests:', 'extrachill-ai-client' ); ?></p>
		<pre><code>$response = apply_filters( 'ai_request', [
    'messages' => [['role' => 'user', 'content' => 'Your prompt here']],
    'model' => 'gpt-4'
], 'openai' );

if ( $response['success'] ) {
    $content = $response['data']['choices'][0]['message']['content'];
}</code></pre>
	</div>
	<?php
}