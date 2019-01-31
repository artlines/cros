import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {compose} from 'redux';
import {
    Chip,
} from '@material-ui/core';
import { withStyles } from '@material-ui/core/styles';
import find from 'lodash/find';

const styles = theme => ({
    chip: {
        margin: theme.spacing.unit / 4,
    },
});

class MemberInfoChip extends React.PureComponent {
    render() {
        const {
            member: { first_name, last_name, org_name, room_type_id, neighbourhood },
            classes, extendInfo, room_types,
        } = this.props;

        let label = `${first_name} ${last_name}`;

        if (extendInfo) {
            label += `\r\n${org_name}`;
            //room_type_id && (label += ` - ${find(room_types, {id: room_type_id}).title}`);
            neighbourhood && (label += ` - Совм. проживание с ${neighbourhood}`);
        }

        return (
            <Chip className={classes.chip} label={label}/>
        );
    }
}

MemberInfoChip.propTypes = {
    member: PropTypes.shape({
        first_name: PropTypes.string.isRequired,
        last_name: PropTypes.string.isRequired,
        org_name: PropTypes.string,
    }),
    extendInfo: PropTypes.bool,
};

MemberInfoChip.defaultProps = {
    extendInfo: false,
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type.items,
    });

export default compose(
    withStyles(styles),
    connect(mapStateToProps),
)(MemberInfoChip);