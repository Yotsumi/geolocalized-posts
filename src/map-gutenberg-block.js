const { __ } = wp.i18n;
const { registerBlockType, RichText } = wp.blocks;

jQuery( document ).ready(function() {
    registerBlockType( 'gut/map-block', {
        title: __( 'Map_Poi(Only Page)' ),
        icon: 'location-alt',
        category: 'common',
        attributes: {},
        edit: function( props ) {
            const { attributes: { }, setAttributes } = props;

            return wp.element.createElement('div', {}, 'Map');
        },
        save ( props ) {
            return null
        },
    });
});