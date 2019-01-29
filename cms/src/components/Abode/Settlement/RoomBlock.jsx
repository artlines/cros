import React from 'react';
import PropTypes from 'prop-types';
import {
    Grid,
    List,
    Typography,
} from '@material-ui/core';
import map from "lodash/map";
import MemberInfoChip from "./MemberInfoChip";

function RoomBlock({ MemberComponent, memberComponentProps, room_type, room }) {
    return (
        <div>
            <Typography variant={`caption`}>{room_type.title}</Typography>
            {room.places.length === 0 &&
                <Typography component={`div`} align={`center`} gutterBottom variant={`caption`}>пусто</Typography>
            }

            <List dense>
            {map(room.places, place => {
                const member = place.member;

                return (
                    <MemberComponent
                        key={member.id}
                        member={member}
                        place={place}
                        {...memberComponentProps}
                    />
                );
            })}
            </List>
        </div>
    );
}

RoomBlock.propTypes = {
    room: PropTypes.object.isRequired,
    room_type: PropTypes.object.isRequired,

    MemberComponent: PropTypes.func,
};

RoomBlock.defaultProps = {
    MemberComponent: MemberInfoChip,
};

export default RoomBlock;