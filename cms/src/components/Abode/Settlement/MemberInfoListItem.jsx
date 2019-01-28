import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {
    ListItem,
    ListItemText,
} from '@material-ui/core';
import find from 'lodash/find';

class MemberInfoListItem extends React.PureComponent {
    render() {
        const { room_types,
            member: { first_name, last_name, org_name, room_type_id, neighbourhood },
        } = this.props;
        const room_type = find(room_types, {id: room_type_id});

        if (!room_type) return null;

        let secondaryText = `${room_type.title}`;
        neighbourhood && (secondaryText += `\r\nСП: ${neighbourhood}`);

        return (
            <ListItem style={{
                userSelect: 'none',
                padding: 4,
            }}>
                <ListItemText
                    primary={`${first_name} ${last_name} | ${org_name}`}
                    secondary={secondaryText}
                    primaryTypographyProps={{ noWrap: true }}
                    secondaryTypographyProps={{ noWrap: true, style: { whiteSpace: 'pre' } }}
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
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type.items,
    });

export default connect(mapStateToProps)(MemberInfoListItem);