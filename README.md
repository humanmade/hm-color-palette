# HM Color Palette

A WordPress plugin that adds customizable color palette meta data or block attribute functionality for posts and pages.

## Description

This plugin allows you to assign color palettes to individual posts and pages, which can then be used to style your content dynamically. It provides:

- Post meta for storing color palette selection
- React component for the block editor sidebar
- Support for using the component in blocks with block attributes
- Body class based on selected palette color
- Developer-friendly customization via a filter that can be used in theme files

## Features

- **Easy Integration**: Works with any theme or block
- **Customizable Palettes**: Define your own color schemes via code or theme.json color palette
- **Block Editor Component**: Ready-to-use React component with `@humanmade/block-editor-components`
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

This plugin uses the theme color palette defined in the active theme's theme.json by default. Developers can also customize the palette programmatically using the `hm_color_palette_options` filter:

```php
add_filter( 'hm_color_palette_options', function( $palette ) {
  // Add a new color.
  $palette[] = [
    'color' => '#000000',
    'name'  => 'Black',
    'slug'  => 'black'
  ];

  // Remove a color without removing from theme.json.
  $item_to_remove = array_find_key( $palette, function ( $value ) {
    return $value['slug'] === 'white';
  } );
  unset( $palette[$item_to_remove] );
  $palette = array_values($palette);
    
  return $palette;
} );
```

### Palette Structure

Each palette must be structured like the theme.json color object:

```json
[
  {
    "color": "#000000",
    "name": "Black",
    "slug": "black"
  },
]
```

## Usage

### Document-Level Color Palette (Post/Page Meta)

A block editor sidebar panel is included with this plugin to set the color at the page/post level. Using the color palette from the included block editor sidebar panel will save the color to post meta and add a class to the page/post body element on the frontend.

### Using In Custom Blocks To Set Color At The Block Level

Add color palette selection to individual blocks:

```javascript
import { HMColorPalette } from 'hm-color-palette';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function Edit({ attributes, setAttributes }) {
  const { colorPalette } = attributes;
  
  const blockProps = useBlockProps({
    className: `has-${ colorPalette }-color-palette`,
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Color Settings', 'your-textdomain')}>
          <HMColorPalette
            blockColorPalette={colorPalette}
            setBlockColorPalette={(value) => setAttributes({ colorPalette: value })}
          />
        </PanelBody>
      </InspectorControls>
      
      <div {...blockProps}>
        Your content here
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

export default function save({ attributes }) {
  const { colorPalette } = attributes;
  
  const blockProps = useBlockProps.save({
    className: `has-${ colorPalette }-color-palette`,
  });

  return (
    <div {...blockProps}>
      Your content here
    </div>
  );
}
```

### Using in Themes

#### 1. PHP Integration

The plugin automatically injects body classes. Access the selected palette in your theme:

```php
<?php
// Get the current post's color palette.
$palette = get_post_meta( get_the_ID(), 'document_color_palette', true );

// Check if a specific palette is selected.
if ( 'primary' === $palette ) {
  // Do something specific for primary palette.
}

// The body class is automatically added: has-{palette}-color-palette
// Example: has-primary-color-palette, has-secondary-color-palette
```

#### 2. CSS Styling in Themes

```css
/* Target specific palettes using body classes */
body.has-primary-color-palette .special-element {
  /* Styles specific to primary palette */
}

body.has-secondary-color-palette .special-element {
  /* Styles specific to secondary palette */
}
```

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
