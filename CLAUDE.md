# ExtraChill AI Client

Network-activated WordPress plugin providing centralized AI provider integration for the ExtraChill Platform. Wraps the ai-http-client library with network-wide API key management and multisite support.

## Plugin Information

- **Name**: ExtraChill AI Client
- **Version**: 1.0.0
- **Text Domain**: `extrachill-ai-client`
- **Author**: Chris Huber
- **Author URI**: https://chubes.net
- **License**: GPL v3 or later
- **Network**: true (network activated across all sites)
- **Requires at least**: 5.0
- **Tested up to**: 6.4
- **Requires PHP**: 7.4

## Architecture

### Network-Activated Plugin Pattern
- Minimal wrapper plugin loading ai-http-client library via Composer
- Network-wide API key management using `get_site_option()` / `update_site_option()`
- Single point of configuration for all AI providers across entire multisite network
- Zero configuration needed in individual plugins - API keys loaded automatically

### Composer Integration
```json
{
    "require": {
        "chubes4/ai-http-client": "dev-main"
    },
    "repositories": [
        {
            "type": "path",
            "url": "../../../ai-http-client"
        }
    ]
}
```

**Path Repository Approach:**
- Uses local ai-http-client directory as Composer dependency
- Symlinked at `vendor/chubes4/ai-http-client`
- Always uses latest local code (no version management for development)
- Run `composer update chubes4/ai-http-client` to pick up library changes

### AI HTTP Client Library (v1.1.3+)
- **Multisite Support**: Uses `get_site_option()` / `update_site_option()` for network-wide storage
- **Backward Compatible**: Works with single-site WordPress installations
- **Filter-Based Architecture**: All functionality exposed via WordPress filters
- **Five AI Providers**: OpenAI, Anthropic, Google Gemini, Grok, OpenRouter

## Network Admin Settings

**Location**: Network Admin → Settings → AI Client

### API Key Management
Centralized configuration page for all AI provider API keys:
- OpenAI API Key
- Anthropic API Key
- Google Gemini API Key
- Grok API Key
- OpenRouter API Key

**Storage**: Network-wide option `ai_http_shared_api_keys` via `get_site_option()`
**Access**: All sites in multisite network automatically load these keys
**Security**: Capability check `manage_network_options` required

## Integration Patterns

### For Plugin Developers

#### Making AI Requests
```php
// Simple AI request with automatic API key loading
$response = apply_filters( 'ai_request', [
    'messages' => [
        ['role' => 'user', 'content' => 'Generate a creative blog title']
    ],
    'model' => 'gpt-4'
], 'openai' );

if ( $response['success'] ) {
    $content = $response['data']['choices'][0]['message']['content'];
    // Use the AI-generated content
}
```

#### Checking Available Providers
```php
// Get all registered AI providers
$providers = apply_filters( 'ai_providers', [] );

foreach ( $providers as $key => $info ) {
    if ( $info['type'] === 'llm' ) {
        echo $info['name']; // "OpenAI", "Anthropic", etc.
    }
}
```

#### Getting Available Models
```php
// Get models for a specific provider
$models = apply_filters( 'ai_models', 'openai', ['api_key' => 'optional'] );

// Models are cached for 24 hours automatically
foreach ( $models as $model_id => $model_name ) {
    echo "$model_id: $model_name";
}
```

### Plugin Settings Integration

Plugins can use the library's component system to add AI provider/model selection to their own settings:

```php
// In your plugin's settings page
echo '<form method="post">';
echo '<table class="form-table">';

// Render AI provider/model selector component
echo apply_filters( 'ai_render_component', '', [
    'selected_provider' => get_option( 'my_plugin_ai_provider', 'openai' ),
    'selected_model' => get_option( 'my_plugin_ai_model', 'gpt-4' )
]);

echo '</table>';
submit_button();
echo '</form>';
```

**Result**: User can select provider and model in plugin settings, but API keys are loaded from network-wide storage automatically.

## Architecture Benefits

### Centralized API Key Management
- ✅ Configure API keys once at network level
- ✅ All plugins automatically access shared keys
- ✅ No duplicate API key storage across plugins
- ✅ Easy updates without touching individual plugins

### Distributed Provider/Model Selection
- ✅ Each plugin chooses its own AI provider
- ✅ Each plugin chooses its own model
- ✅ Blocks plugin can use GPT-4
- ✅ Chat plugin can use Claude
- ✅ Artist Platform can use Gemini
- ✅ All sharing the same network-wide API keys

### Example Use Cases

**ExtraChill Blocks Plugin:**
- Uses network-wide OpenAI API key
- Configures GPT-4 model in block settings
- AI Adventure block generates dynamic stories

**ExtraChill Chat Plugin (Future):**
- Uses network-wide Anthropic API key
- Configures Claude 3.5 Sonnet in plugin settings
- Provides AI chatbot for logged-in users

**ExtraChill Artist Platform:**
- Uses network-wide Gemini API key
- Configures Gemini Pro in artist profile settings
- Generates artist bios and content suggestions

## Development Workflow

### Local Development
```bash
cd extrachill-ai-client

# Install dependencies
composer install

# Update ai-http-client library
composer update chubes4/ai-http-client

# Run code quality checks
composer run lint:php
composer run analyse
```

### Production Build
```bash
./build.sh
```

**Build Process:**
1. Extracts version from plugin header
2. Runs `composer install --no-dev` for production dependencies
3. Copies essential files (excludes dev files via `.buildignore`)
4. Creates ZIP: `dist/extrachill-ai-client-{version}.zip`
5. Restores development dependencies

## File Structure

```
extrachill-ai-client/
├── extrachill-ai-client.php    # Main plugin file (Network: true)
├── composer.json                # Composer configuration
├── inc/
│   └── admin-settings.php       # Network admin settings page
├── vendor/                      # Composer dependencies (symlinked ai-http-client)
├── build.sh                     # Production build script
├── .buildignore                 # Files excluded from production builds
└── CLAUDE.md                    # This documentation file
```

## Security Implementation

### Network Admin Capabilities
- All API key management requires `manage_network_options` capability
- WordPress nonce verification on form submissions
- Input sanitization via `sanitize_text_field()` and `wp_unslash()`

### API Key Storage
- Stored in `wp_sitemeta` table (multisite) via `update_site_option()`
- Stored in `wp_options` table (single-site) via automatic fallback
- WordPress core handles encryption and security

## Dependencies

### PHP Requirements
- **PHP**: 7.4+
- **WordPress**: 5.0+ multisite network
- **Multisite**: Required (enforced on activation)

### Composer Dependencies

**Production:**
- `chubes4/ai-http-client: dev-main` - AI HTTP Client library (v1.1.3+)

**Development:**
- `phpunit/phpunit: ^9.0 || ^10.0` - Unit testing
- `phpstan/phpstan: ^1.0` - Static analysis
- `squizlabs/php_codesniffer: ^3.7` - Code standards
- `wp-coding-standards/wpcs: ^3.0` - WordPress coding standards

## Common Development Commands

```bash
# Install dependencies
composer install

# Update ai-http-client library to latest
composer update chubes4/ai-http-client

# Run PHP linting
composer run lint:php

# Fix PHP coding standards issues
composer run lint:fix

# Run static analysis
composer run analyse

# Run all quality checks
composer run check

# Create production build
./build.sh
```

## Multisite Network Integration

### Blog ID Architecture
The ExtraChill Platform uses hardcoded blog IDs for performance:
- **Blog ID 1**: extrachill.com (main site)
- **Blog ID 2**: community.extrachill.com (community forums)
- **Blog ID 3**: shop.extrachill.com (e-commerce)
- **Blog ID 4**: app.extrachill.com (mobile API - planned)

### Network-Wide Availability
- Plugin must be network-activated
- API keys accessible from all 4 sites automatically
- No per-site configuration needed

## Future Enhancements

### Potential Features
- Model testing interface in network admin
- Usage tracking and analytics
- Rate limiting configuration
- Cost monitoring per provider
- API key rotation management

### Plugin Integration Roadmap
- **Phase 1**: ExtraChill Blocks refactoring (replace standalone OpenAI client)
- **Phase 2**: ExtraChill Chat plugin development (new chatbot functionality)
- **Phase 3**: Artist Platform AI features (bio generation, content suggestions)
- **Phase 4**: News Wire AI integration (article summarization, tag suggestions)

## User Info

- Name: Chris Huber
- Dev website: https://chubes.net
- GitHub: https://github.com/chubes4
- Founder & Editor: https://extrachill.com
- Creator: https://saraichinwag.com