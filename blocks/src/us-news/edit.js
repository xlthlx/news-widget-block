import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { __experimentalNumberControl as NumberControl, PanelBody, PanelRow } from '@wordpress/components';
export default function Edit(attributes, context, setAttributes) {
	const blockProps = useBlockProps();

	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const newsNumber = meta[ 'news_number' ];

	const updateNumber = ( newValue ) => {
		setMeta( { ...meta, 'news_number': newValue } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'News Options' ) } initialOpen={ true }>
					<PanelRow>
						<fieldset>
							<NumberControl
								label={ __( 'Number of News', 'digital-catalogue' ) }
								value={ newsNumber }
								onChange={ updateNumber }
							/>
						</fieldset>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<ServerSideRender
					attributes={attributes}
					block="news-widget-block/us-news"
					urlQueryArgs={{
						previewNewsNumber: newsNumber,
					}}
				/>
			</div>
		</>
	);
}
