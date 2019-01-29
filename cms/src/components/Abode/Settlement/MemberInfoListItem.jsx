import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import PropTypes from 'prop-types';
import {
    ListItem,
    ListItemText,
} from '@material-ui/core';
import { withTheme } from '@material-ui/core/styles';
import find from 'lodash/find';

class MemberInfoListItem extends React.PureComponent {
    render() {
        const { room_types, dense, theme,
            member: { first_name, last_name, org_name, room_type_id, neighbourhood },
        } = this.props;

        /** Check room_type */
        const room_type = find(room_types, {id: room_type_id});
        if (!room_type) return null;

        /** Collect primaryText */
        let primaryText = `${first_name} ${last_name}`;
        !dense && (primaryText += ` | ${org_name}`);

        /** Collect secondaryText */
        let secondaryText = dense ? org_name : room_type.title;
        !dense && neighbourhood && (secondaryText += `\r\nСП: ${neighbourhood}`);

        return (
            <ListItem style={{
                userSelect: 'none',
                padding: `0 4px`,
                margin: dense ? `2px 0` : `4px 0`,
                borderLeft: `2px solid ${theme.palette.primary.light}`
            }}>
                <ListItemText
                    primary={primaryText}
                    secondary={secondaryText}
                    primaryTypographyProps={{ noWrap: true }}
                    secondaryTypographyProps={{ noWrap: true }}
                />
            </ListItem>
        );
    }
}

MemberInfoListItem.propTypes = {
    member: PropTypes.shape({
        id:             PropTypes.number.isRequired,
        first_name:     PropTypes.string.isRequired,
        last_name:      PropTypes.string.isRequired,
        org_name:       PropTypes.string.isRequired,
        neighbourhood:  PropTypes.string,
    }),
    dense: PropTypes.bool,
};

MemberInfoListItem.defaultProps = {
    dense: false,
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type.items,
    });

export default compose(
    withTheme(),
    connect(mapStateToProps),
)(MemberInfoListItem);