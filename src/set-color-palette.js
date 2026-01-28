import colorOptionsDefault from './color-palette.json';

// Export a mutable object that will be updated with custom palettes
export let colorOptions = { ...colorOptionsDefault };

/**
 * Load and merge custom palettes with defaults.
 * This is called by the component on mount.
 *
 * @param {Object} customPalettes Custom palettes object.
 */
export const mergeCustomPalettes = ( customPalettes ) => {
	colorOptions = { ...colorOptionsDefault, ...customPalettes };
};

/**
 * Function to update the CSS runtime variables.
 * That's only needed for user to see the changes done
 * reflected in the editor in real time.
 *
 * @param {Element} domElement    The current dom Element.
 * @param {string}  selectedColor Slug of the palette selected.
 * @returns {void}
 */
export const setColorPalette = ( domElement, selectedColor ) => {
	const colorObj =
		selectedColor && selectedColor in colorOptions
			? colorOptions[ selectedColor ]
			: Object.values( colorOptions )[0];

	domElement.style.setProperty(
		'--primary-page-color-bright',
		colorObj.palette.color_bright
	);

	domElement.style.setProperty(
		'--primary-page-color-text',
		colorObj.palette.color_text
	);

	domElement.style.setProperty(
		'--primary-page-color-ui',
		colorObj.palette.color_ui
	);

	domElement.style.setProperty(
		'--primary-page-color-reverse-background',
		colorObj.palette.color_reverse_background
	);

	domElement.style.setProperty(
		'--primary-page-color-reverse-text',
		colorObj.palette.color_reverse_text
	);

	domElement.style.setProperty(
		'--primary-page-color-reverse-ui',
		colorObj.palette.color_reverse_ui
	);
};

/**
 * Gets the color palette inline styles.
 *
 * @param {string} selectedColor The selected color object slug.
 * @returns {string} The inline styles string.
 */
export const getBlockInlineColorStyles = ( selectedColor ) => {
	if ( ! ( selectedColor in colorOptions ) ) {
		return {};
	}

	const palette = colorOptions[ selectedColor ].palette;

	return {
		'--primary-page-color-bright': palette.color_bright,
		'--primary-page-color-text': palette.color_text,
		'--primary-page-color-ui': palette.color_ui,
		'--primary-page-color-reverse-background':
			palette.color_reverse_background,
		'--primary-page-color-reverse-text': palette.color_reverse_text,
		'--primary-page-color-reverse-ui': palette.color_reverse_ui,
	};
};

