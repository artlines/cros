import React from 'react';
import PropTypes from 'prop-types';
import {
    Chip,
} from '@material-ui/core';

const style = {
    margin: 4,
};

class MemberInfoChip extends React.PureComponent {
    render() {
        const { first_name, last_name, org_name } = this.props;

        return (
            <Chip style={style} label={`${first_name} ${last_name} (${org_name})`}/>
        );
    }
}

MemberInfoChip.propTypes = {
    first_name: PropTypes.string.isRequired,
    last_name: PropTypes.string.isRequired,
    org_name: PropTypes.string.isRequired,
};

MemberInfoChip.defaultProps = {};

export default MemberInfoChip;