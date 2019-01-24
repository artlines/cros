import React from 'react';
import PropTypes from 'prop-types';
import {
    Chip,
} from '@material-ui/core';
import { withStyles } from '@material-ui/core/styles';

const styles = theme => ({
    chip: {
        margin: theme.spacing.unit / 4,
    },
});

class MemberInfoChip extends React.PureComponent {
    render() {
        const { first_name, last_name, org_name, classes, } = this.props;

        let label = `${first_name} ${last_name}`;
        org_name && (label += ` (${org_name})`);

        return (
            <Chip className={classes.chip} label={label}/>
        );
    }
}

MemberInfoChip.propTypes = {
    first_name: PropTypes.string.isRequired,
    last_name: PropTypes.string.isRequired,

    org_name: PropTypes.string,
};

MemberInfoChip.defaultProps = {};

export default withStyles(styles)(MemberInfoChip);