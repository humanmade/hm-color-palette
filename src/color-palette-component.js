import colorOptionsDefault from './color-palette.json';
import { useMeta } from '@humanmade/block-editor-components';
import { ReactNode, useState } from 'react';

import { BaseControl, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * HMColorPalette component.
 *
 * @param {object} props - Component props.
 * @returns {ReactNode} Component.
 */
const HMColorPalette = ( props ) => {
	const { blockColorPalette, isBlock = true, setBlockColorPalette } = props;
	const [ documentColorPalette, setDocumentColorPalette ] = useMeta(
		'document_color_palette'
	);
	
	const [ colorOptions ] = useState( colorOptionsDefault );

	/**
	 * Function to handle color change.
	 *
	 * @param {string|undefined} colorValue The value of the selected color or undefined.
	 *
	 * @returns {void}
	 */
	const onColorChange = ( colorValue ) => {
		// User clicked "clear".
		if ( colorValue === undefined || colorValue === null ) {
			if ( isBlock ) {
				// null color palette is saved to the block attribute.
				setBlockColorPalette( null );
			} else {
				// null color palette is saved to post metadata.
				setDocumentColorPalette( null );
			}
			return;
		}

		// Get the slug of the selected color value.
		const slug = Object.keys( colorOptions ).find(
			( slug ) => colorOptions[ slug ].color === colorValue
		);

		if ( slug ) {
			if ( isBlock ) {
				// Save as a block attribute.
				setBlockColorPalette( slug );
			} else {
				// Save the selected color to post metadata.
				setDocumentColorPalette( slug );
			}
		}
	};

	// Get the selected color from the meta or block object.
	const selectedColor = isBlock ? blockColorPalette : documentColorPalette;

	const colors = Object.values( colorOptions );
	const currentValue =
		selectedColor in colorOptions
			? colorOptions[ selectedColor ].color
			: '';

	return (
		<BaseControl
			id="palette-settings-control"
			label={ __( 'Choose a Color Palette', 'color-palette' ) }
		>
			<ColorPalette
				colors={ colors }
				disableCustomColors
				value={ currentValue }
				onChange={ onColorChange }
			/>
		</BaseControl>
	);
};

export default HMColorPalette;
