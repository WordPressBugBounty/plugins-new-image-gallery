(function(blocks, editor, element, components) {
    'use strict';

    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = editor.InspectorControls;
    var SelectControl = components.SelectControl;
    var PanelBody = components.PanelBody;

    registerBlockType('new-image-gallery/image-gallery-block', {
        title: 'Image Gallery',
        description: 'Display an individual image gallery.',
        icon: 'images-alt2',
        category: 'widgets',
        attributes: {
            galleryId: {
                type: 'string',
                default: ''
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Load localized galleries
            var gData = window.igp_gutenberg_data || { galleries: [] };
            
            // Build galleries list
            var galleryOptions = [{ label: '-- Select Gallery --', value: '' }];
            gData.galleries.forEach(function(g) {
                galleryOptions.push({ label: g.title + ' (ID: ' + g.id + ')', value: g.id.toString() });
            });

            return [
                // Block settings in sidebar Inspector
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Gallery Display Settings', initialOpen: true },
                        el(SelectControl, {
                            label: 'Select Gallery',
                            value: attributes.galleryId,
                            options: galleryOptions,
                            onChange: function(value) {
                                setAttributes({ galleryId: value });
                            }
                        })
                    )
                ),

                // Editor view placeholder/preview
                el('div', { key: 'view', className: 'ig-block-placeholder-preview' },
                    el('div', {
                        style: {
                            background: 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                            color: '#ffffff',
                            padding: '25px',
                            borderRadius: '12px',
                            textAlign: 'center',
                            boxShadow: '0 4px 15px rgba(79, 70, 229, 0.15)',
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
						}
                    },
                        el('span', { className: 'dashicons dashicons-images-alt2', style: { fontSize: '32px', width: '32px', height: '32px', marginBottom: '8px' } }),
                        el('h4', { style: { margin: '0 0 5px 0', fontSize: '16px', fontWeight: '700' } }, 'Image Gallery Block'),
                        el('p', { style: { margin: 0, opacity: 0.9, fontSize: '13px' } }, 
                            attributes.galleryId 
                                ? 'Rendering Gallery ID: ' + attributes.galleryId
                                : 'Please choose a gallery in the block settings.'
                        )
                    )
                )
            ];
        },

        save: function() {
            // Server-side rendering
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.blockEditor || window.wp.editor,
    window.wp.element,
    window.wp.components
);
