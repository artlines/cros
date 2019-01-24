import React from 'react';
import { DragSource } from 'react-dnd';
import { DnDItemTypes } from "../../config/lib";
import MemberInfoChip from '../../components/Abode/Settlement/MemberInfoChip';

const chipSource = {
    beginDrag(props) {
        const { member: { id, room_type_id } } = props;
        return {
            id,
            room_type_id,
        };
    }
};

function collect(connect, monitor) {
    return {
        connectDragSource: connect.dragSource(),
        isDragging: monitor.isDragging(),
    };
}

function WrappedMemberInfoChip({ isDragging, connectDragSource, ...props }) {
    return connectDragSource(
        <div>
            <MemberInfoChip {...props}/>
        </div>
    );
}

export default DragSource(DnDItemTypes.MEMBER, chipSource, collect)(WrappedMemberInfoChip);