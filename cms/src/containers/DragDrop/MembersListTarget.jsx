import React from "react";
import { DropTarget } from "react-dnd";
import { DnDItemTypes } from "../../config/lib";
import MembersList from "../../components/Abode/Settlement/MembersList";

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
        const { place, dropPlace } = monitor.getItem();

        dropPlace(place.id);
    },
    canDrop(props, monitor) {
        const { place } = monitor.getItem();
        return !!place;
    }
};

function collect(connect, monitor) {
    return {
        connectDropTarget: connect.dropTarget(),
        isOver: monitor.isOver(),
        canDrop: monitor.canDrop(),
    };
}

function WrappedMembersList({ connectDropTarget, isOver, canDrop, ...props}) {
    return connectDropTarget(
        <div style={overlayContainerStyle}>
            <MembersList {...props}/>
            {!isOver && canDrop && renderOverlay("yellow")}
            {isOver && canDrop && renderOverlay("green")}
            {isOver && !canDrop && renderOverlay("red")}
        </div>
    );
}

export default DropTarget(DnDItemTypes.MEMBER, target, collect)(WrappedMembersList);