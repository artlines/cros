import React from "react";
import { DropTarget } from "react-dnd";
import { DnDItemTypes } from "../../config/lib";
import RoomBlock from "../../components/Abode/Settlement/RoomBlock";

const overlayStyle = {
    position: "absolute",
    top: 0,
    left: 0,
    opacity: 0.4,
    height: "100%",
    width: "100%",
    zIndex: 1,
};

const overlayContainerStyle = {
    position: "relative",
    width: "100%",
    height: "100%",
};

function renderOverlay(color) {
    return (
        <div style={{ ...overlayStyle, backgroundColor: color }}/>
    );
}

const target = {
    drop(props, monitor) {
        const { member, place, holdPlace, changePlace } = monitor.getItem();
        const { room } = props;

        if (place) {
            changePlace(place.id, room.id);
        } else {
            holdPlace(room.id, member.id);
        }
    },

    canDrop(props, monitor) {
        const { room, room_type } = props;
        const { member, place } = monitor.getItem();

        return member.room_type_id === room.type_id
            && room.places.length < room_type.max_places
            && (!place || place.room_id !== room.id);
    },
};

function collect(connect, monitor) {
    return {
        connectDropTarget: connect.dropTarget(),
        isOver: monitor.isOver(),
        canDrop: monitor.canDrop(),
    };
}

function WrappedRoomBlock({ connectDropTarget, isOver, canDrop, ...props}) {
    return connectDropTarget(
        <div style={overlayContainerStyle}>
            <RoomBlock {...props}/>
            {!isOver && canDrop && renderOverlay("yellow")}
            {isOver && canDrop && renderOverlay("green")}
            {isOver && !canDrop && renderOverlay("red")}
        </div>
    );
}

export default DropTarget(DnDItemTypes.MEMBER, target, collect)(WrappedRoomBlock);