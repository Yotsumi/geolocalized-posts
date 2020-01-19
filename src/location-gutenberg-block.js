jQuery( document ).ready(function() {
    registerBlockType( 'gut/location-block', {
        title: __( 'Geolocalization' ),
        icon: 'location',
        category: 'common',
        attributes: {
            nameTag: {
                type: 'string'
            },
            lastNameTag: {
                type: 'string'
            },
            lat: {
                type: 'double'
            },
            lon: {
                type: 'double'
            }
        },
        edit: function( props ) {
            const { attributes: { nameTag, lat, lon, lastNameTag }, setAttributes } = props;

            function setCategory( event ) {
                let x = JSON.parse(event.currentTarget.value);
                props.setAttributes({nameTag: x[0], lat: x[1], lon: x[2], lastNameTag: props.attributes.nameTag})
            }
            let arr = []

            Geopositions.geoList.forEach(x => {
                arr.push(wp.element.createElement("option", {value: `["${x.name}", ${x.lat}, ${x.lon}]`}, x.name));
            })

            return 'Select location: ',
            wp.element.createElement("select", {value : `["${props.attributes.nameTag}", ${props.attributes.lat}, ${props.attributes.lon}]`, onChange: setCategory},
            wp.element.createElement("option", {value: "", selected: "selected"}, "Choose location"),
            arr
            );
        },
        save ( props ) {
            return null
        },
    });
});