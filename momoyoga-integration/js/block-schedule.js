const { __, _x, _n, sprintf } = wp.i18n;

var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    PlainText = wp.blockEditor.PlainText,
    SVG = wp.components.SVG,
    Path = wp.components.Path,
    G = wp.components.G,
    icon = el( SVG, {
        viewBox: '0 0 20 20',
        xmlns: 'http://www.w3.org/2000/svg',
        height: '20px',
        width: '20px',
        className: 'dashicon'
    }, el( G, {
        stroke: 'none',
        strokeWidth: 1,
        fill: 'none',
        fillRule: 'evenodd'
    }, el( Path, {
        d: 'M14.508,19.5 C17.2648576,19.5 19.5,17.2648576 19.5,14.508 L19.5,5.492 C19.5,2.73514237 17.2648576,0.5 14.508,0.5 L5.491,0.5 C2.73436127,0.5 0.5,2.73492344 0.5,5.492 L0.5,14.508 C0.5,17.2650766 2.73436127,19.5 5.491,19.5 L14.508,19.5 Z',
        stroke: '#555d66'
    }), el (Path, {
        d: 'M12.9122469,6 C11.8302797,6 11.0077146,6.45515911 10.3076181,7.18514947 C9.60655738,6.45515911 8.93731919,6 7.85535198,6 C7.26036644,6 6.69816779,6.29893925 6.19382835,6.54387657 L5.88621022,6.15621986 L4,6.15621986 L4,9.92285439 L4,13.8486017 L6.19382835,13.8486017 L6.19382835,13.4599807 L6.19382835,9.92285439 C6.19382835,8.99132112 6.62970106,8.23336548 7.54773385,8.23336548 C8.46480231,8.23336548 8.90163934,8.99132112 8.90163934,9.92285439 L8.90163934,9.93056895 L8.90163934,13.4599807 L8.90163934,13.8486017 L11.0983607,13.8486017 L11.0983607,13.4599807 L11.0983607,9.93056895 C11.0983607,9.92767599 11.0973963,9.92574735 11.0973963,9.92285439 C11.0973963,8.99132112 11.533269,8.23336548 12.4513018,8.23336548 C13.3693346,8.23336548 13.8052073,8.99132112 13.8052073,9.92285439 L13.8052073,13.4599807 L13.8052073,13.8486017 L16,13.8486017 L16,9.92285439 C16,7.75699132 15.0424301,6 12.9122469,6',
        fill: '#ef5651'
    })));

registerBlockType( 'momoyoga-integration/schedule', {
    title: __('Momoyoga schedule', 'momoyoga-integration'),
    category: 'widgets',
    icon: icon,

    attributes: {
        scheduleUrl: {
            type: 'string',
            source: 'attribute',
            selector: 'div',
            attribute: 'data-momo-schedule'
        }
    },

    edit: function(props) {
        return el( 'div', { className: props.className }, el('label', {
            for: 'block-momoyoga-integration-schedule-url'
        }, icon, __('Schedule URL', 'momoyoga-integration')), el( PlainText, {
            className: 'editor-plain-text input-control', 
            id: 'block-momoyoga-integration-schedule-url',
            onChange: function ( content ) {
                props.setAttributes( { scheduleUrl: content } );
            },
            placeholder: __('https://www.momoyoga.com/example-yoga-studio', 'momoyoga-integration'),
            value: props.attributes.scheduleUrl
        }));
    },

    save: function(props) {
        return el ('div', {
            className: 'momoyoga-schedule',
            'data-momo-schedule': props.attributes.scheduleUrl
        });
    },
} );
