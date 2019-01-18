import React from 'react';
import moment from 'moment';
import PropTypes from 'prop-types';

class DateTime extends React.PureComponent {
    constructor(props) {
        super(props);
    }

    renderValue = () => {
        const { withTime, value } = this.props;
        const format = withTime ? 'DD.MM.YYYY HH:mm' : 'DD.MM.YYYY';

        const momentDateTime = moment.unix(value).isValid()
            ? moment.unix(value) : (moment(value).isValid() ? moment(value) : false);

        return momentDateTime ? momentDateTime.format(format) : '';
    };

    render() {
        return (
            <span>{this.renderValue()}</span>
        );
    }
}

DateTime.propTypes = {
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    format: PropTypes.string,
    withTime: PropTypes.bool,
};

DateTime.defaultProps = {
    withTime: false,
};

export default DateTime;