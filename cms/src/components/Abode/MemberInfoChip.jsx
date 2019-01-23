import React from 'react';
import PropTypes from 'prop-types';
import {
    Chip,
} from '@material-ui/core';


class MemberInfoChip extends React.PureComponent {
    render() {
        const {  } = this.props;

        return (
            <span>asdasd</span>
        );
    }
}

MemberInfoChip.propTypes = {
    first_name: PropTypes.string.isRequired,
    last_name: PropTypes.string.isRequired,
};

MemberInfoChip.defaultProps = {};

export default MemberInfoChip;