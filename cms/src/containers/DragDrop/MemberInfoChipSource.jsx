import React from 'react';
import { DragSource } from 'react-dnd';
import { DnDItemTypes } from "../../config/lib";
import MemberInfoChip from '../../components/Abode/Settlement/MemberInfoChip';

const chipSource = {
    beginDrag(props) {
        const { member, holdPlace, changePlace, dropPlace, place } = props;
        console.log(`beginDrag member chip`);
        return {
            member,
            place,
            holdPlace,
            changePlace,
            dropPlace,
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