import React from 'react';
import PropTypes from 'prop-types';
import { DragSource } from 'react-dnd';
import { DnDItemTypes } from "../../config/lib";

const chipSource = {
    beginDrag(props) {
        const { member, holdPlace, changePlace, dropPlace, place } = props;

        return {
            member,
            place,
            holdPlace,
            changePlace,
            dropPlace,
        };
    },
};

function collect(connect, monitor) {
    return {
        connectDragSource: connect.dragSource(),
        isDragging: monitor.isDragging(),
    };
}

function WrappedMemberInfoSource({ Component, isDragging, connectDragSource, ...props }) {
    return connectDragSource(
        <div>
            <Component {...props}/>
        </div>
    );
}

WrappedMemberInfoSource.propTypes = {
    Component: PropTypes.func.isRequired,
};

export default DragSource(DnDItemTypes.MEMBER, chipSource, collect)(WrappedMemberInfoSource);