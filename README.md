# HM Color Palette

A WordPress plugin that adds customizable color palette meta data functionality for posts and pages.

## Description

This plugin allows you to assign color palettes to individual posts and pages, which can then be used to style your content dynamically. It provides:

- Post meta for storing color palette selection
- Pre-configured color schemes (6 default palettes)
- React component for the block editor
- CSS custom properties (CSS variables) for easy styling
- Body class based on selected palette
- Developer-friendly customization via filters and theme files

## Features

- **Easy Integration**: Works with any theme or block
- **Customizable Palettes**: Define your own color schemes via code or JSON
- **Block Editor Component**: Ready-to-use React component with `@humanmade/block-editor-components`
- **CSS Variables**: Automatic injection of CSS custom properties
- **Body Classes**: Adds palette-specific classes for additional styling
- **Document & Block Level**: Can be used for entire documents or individual blocks

## Installation

### For Development
1. Clone or download to `/wp-content/plugins/hm-color-palette/`
2. Run `npm install && npm run build`
3. Activate the plugin in WordPress

### For Production
1. Download from the release
2. Upload to `/wp-content/plugins/hm-color-palette/`
3. Activate the plugin in WordPress

## Managing Color Palettes

This plugin comes with 6 default color palettes. Developers can customize palettes in three ways:

### Method 1: Theme JSON File (Recommended)

Create a `color-palettes.json` file in your active theme's root directory:

```json
{
  "custom-blue": {
    "name": "Custom Blue",
    "color": "#0066cc",
    "palette": {
      "color_bright": "#0066cc",
      "color_text": "#003366",
      "color_ui": "#99ccff",
      "color_reverse_background": "#003366",
      "color_reverse_text": "#ffffff",
      "color_reverse_ui": "#0066cc"
    }
  },
  "custom-red": {
    "name": "Custom Red",
    "color": "#cc0000",
    "palette": {
      "color_bright": "#cc0000",
      "color_text": "#660000",
      "color_ui": "#ff9999",
      "color_reverse_background": "#660000",
      "color_reverse_text": "#ffffff",
      "color_reverse_ui": "#cc0000"
    }
  }
}
```

Place this file at: `wp-content/themes/your-theme/color-palettes.json`

### Method 2: Filter Hook

Add palettes programmatically using the `hm_color_palette_options` filter:

```php
add_filter( 'hm_color_palette_options', function( $palettes ) {
    $palettes['brand-primary'] = [
        'name' => 'Brand Primary',
        'color' => '#ff6b35',
        'palette' => [
            'color_bright' => '#ff6b35',
            'color_text' => '#cc5529',
            'color_ui' => '#ffa07a',
            'color_reverse_background' => '#cc5529',
            'color_reverse_text' => '#ffffff',
            'color_reverse_ui' => '#ff6b35',
        ],
    ];
    
    return $palettes;
} );
```

Add this code to your theme's `functions.php` or a custom plugin.

### Method 3: Plugin Extension

Create a separate plugin to extend color palettes:

```php
<?php
/**
 * Plugin Name: My Custom Color Palettes
 * Description: Custom color palettes for my site
 */

add_filter( 'hm_color_palette_options', function( $palettes ) {
    // Load from your own JSON file
    $custom_file = plugin_dir_path( __FILE__ ) . 'palettes.json';
    if ( file_exists( $custom_file ) ) {
        $custom_palettes = json_decode( file_get_contents( $custom_file ), true );
        $palettes = array_merge( $palettes, $custom_palettes );
    }
    
    return $palettes;
} );
```

### Palette Structure

Each palette must have this structure:

```json
{
  "palette-slug": {
    "name": "Display Name",
    "color": "#hexcode",
    "palette": {
      "color_bright": "#hexcode",
      "color_text": "#hexcode",
      "color_ui": "#hexcode",
      "color_reverse_background": "#hexcode",
      "color_reverse_text": "#hexcode",
      "color_reverse_ui": "#hexcode"
    }
  }
}
```

- **palette-slug**: Unique identifier (lowercase, hyphens)
- **name**: Display name shown in editor
- **color**: Main preview color
- **palette.color_bright**: Bright accent color
- **palette.color_text**: Text color
- **palette.color_ui**: UI elements color
- **palette.color_reverse_background**: Background for reversed sections
- **palette.color_reverse_text**: Text color for reversed sections
- **palette.color_reverse_ui**: UI color for reversed sections

**Important:** Custom palettes with the same slug as default palettes will override them.

### Color Palette Structure

Each palette consists of:
- **Slug**: Unique identifier used in code
- **Name**: Display name in the editor
- **Main Color**: Preview color in the palette selector
- **6 Color Variants**:
  - **Bright**: Lighter/brighter version for backgrounds
  - **Text**: Main text color
  - **UI**: UI elements (buttons, borders)
  - **Reverse Background**: Background for contrast sections
  - **Reverse Text**: Text for contrast sections
  - **Reverse UI**: UI elements for contrast sections

## Usage

### Using in Custom Blocks

#### 1. Document-Level Color Palette (Post/Page Meta)

Add color palette selection to the document settings sidebar:

```javascript
import { HMColorPalette } from 'color-palette';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function Edit() {
  return (
    <InspectorControls>
      <PanelBody title={__('Document Color Palette', 'your-textdomain')}>
        <HMColorPalette isBlock={false} />
      </PanelBody>
    </InspectorControls>
  );
}
```

This will save the palette to post meta and apply CSS variables to the entire page.

#### 2. Block-Level Color Palette

Add color palette selection to individual blocks:

```javascript
import { HMColorPalette } from 'color-palette';
import { getBlockInlineColorStyles } from 'color-palette';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function Edit({ attributes, setAttributes }) {
  const { colorPalette } = attributes;
  
  const blockProps = useBlockProps({
    style: getBlockInlineColorStyles(colorPalette),
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Color Settings', 'your-textdomain')}>
          <HMColorPalette 
            isBlock={true}
            blockColorPalette={colorPalette}
            setBlockColorPalette={(value) => setAttributes({ colorPalette: value })}
          />
        </PanelBody>
      </InspectorControls>
      
      <div {...blockProps}>
        <div style={{ backgroundColor: 'var(--primary-page-color-bright)' }}>
          Your content here
        </div>
      </div>
    </>
  );
}
```

**block.json:**
```json
{
  "attributes": {
    "colorPalette": {
      "type": "string",
      "default": null
    }
  }
}
```

**save.js:**
```javascript
import { useBlockProps } from '@wordpress/block-editor';
import { getBlockInlineColorStyles } from 'color-palette';

export default function save({ attributes }) {
  const { colorPalette } = attributes;
  
  const blockProps = useBlockProps.save({
    style: getBlockInlineColorStyles(colorPalette),
  });

  return (
    <div {...blockProps}>
      <div style={{ backgroundColor: 'var(--primary-page-color-bright)' }}>
        Your content here
      </div>
    </div>
  );
}
```

### Using in Themes

#### 1. PHP Integration

The plugin automatically injects CSS variables and body classes. Access the selected palette in your theme:

```php
<?php
// Get the current post's color palette
$palette = get_post_meta( get_the_ID(), 'color_palette', true );

// Check if a specific palette is selected
if ( 'primary' === $palette ) {
  // Do something specific for primary palette
}

// The body class is automatically added: has-{palette}-palette
// Example: has-primary-palette, has-secondary-palette
```

#### 2. CSS Styling in Themes

The plugin provides these CSS variables automatically:

```css
.your-theme-element {
  /* Use the CSS variables in your theme styles */
  background-color: var(--primary-page-color-bright);
  color: var(--primary-page-color-text);
  border-color: var(--primary-page-color-ui);
}

.your-reverse-section {
  background-color: var(--primary-page-color-reverse-background);
  color: var(--primary-page-color-reverse-text);
}

/* Target specific palettes using body classes */
body.has-primary-palette .special-element {
  /* Styles specific to primary palette */
}

body.has-secondary-palette .special-element {
  /* Styles specific to secondary palette */
}
```

#### 3. JavaScript Access in Themes

```javascript
import { colorOptions, setColorPalette } from 'color-palette';

// Get all available color palettes
console.log(colorOptions);

// Programmatically set color palette (for block attributes)
const styles = setColorPalette('primary');
// Returns object with CSS custom properties
```

### Available CSS Variables

When a palette is selected, these CSS variables are automatically available:

- `--primary-page-color-bright` - Lighter/brighter version of the palette color
- `--primary-page-color-text` - Main text color for the palette
- `--primary-page-color-ui` - UI elements color (buttons, borders, etc.)
- `--primary-page-color-reverse-background` - Background for reverse/contrast sections
- `--primary-page-color-reverse-text` - Text color for reverse sections
- `--primary-page-color-reverse-ui` - UI color for reverse sections

### Available Color Palettes

The plugin includes these pre-configured palettes:

- **Primary** - Main brand colors
- **Secondary** - Alternative brand colors
- **Success** - Green tones for positive actions
- **Warning** - Orange/yellow tones for warnings
- **Danger** - Red tones for errors/alerts
- **Dark** - Dark mode colors

### Customizing Color Palettes

Edit [src/color-palette.json](src/color-palette.json) to define your own color schemes:

```json
{
  "your-palette-slug": {
    "name": "Your Palette Name",
    "color": "#main-color",
    "palette": {
      "color_bright": "#bright-variant",
      "color_text": "#text-color",
      "color_ui": "#ui-color",
      "color_reverse_background": "#reverse-bg",
      "color_reverse_text": "#reverse-text",
      "color_reverse_ui": "#reverse-ui"
    }
  }
}
```

After editing, run `npm run build` to recompile.

## Example: Test Block

The plugin includes a test block that demonstrates the full functionality. You can find it in [src/blocks/test-block/](src/blocks/test-block/).

To see it in action:
1. Activate the plugin
2. Open the block editor
3. Add the "Color Palette Test" block
4. Open the sidebar and select different color palettes
5. Watch the block colors change dynamically

This example shows:
- How to integrate `HMColorPalette` in the sidebar
- How to use `getBlockInlineColorStyles()` for inline styles
- How to apply CSS variables to block elements
- Both editor and frontend rendering

## Development

### Setup
```bash
npm install
composer install
```

### Build
```bash
npm run build
```

### Development Mode
```bash
npm start
```

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Node.js 20.10+

## License

GPL-2.0-or-later

## Author

Human Made Limited - https://humanmade.com
