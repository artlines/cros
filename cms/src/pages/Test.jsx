import React from 'react';
import { DragSource, DropTarget, DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import {
    Grid,
    Paper,
    Chip,
    Typography,
} from '@material-ui/core';

const types = {
    CHIP: 'chip',
};

/**
 * CHIP SOURCE
 */
const chipSource = {
    beginDrag(props) {
        console.log(`Chip::beginDrag`);
        return {
            id: props.id,
            type_id: props.type_id,
        };
    }
};
function collectSource(connect, monitor) {
    return {
        connectDragSource: connect.dragSource(),
        isDragging: monitor.isDragging(),
    };
}
function MyChip({ id, isDragging, connectDragSource }) {
    return connectDragSource(
        <div style={{cursor: 'pointer'}}><Chip label={`Chip #${id} | Dragging: ${isDragging ? 'true' : 'false'}`}/></div>
    );
}
const DragChip = DragSource(types.CHIP, chipSource, collectSource)(MyChip);

/**
 * END CHIP SOURCE
 */

/**
 * PAPER TARGET
 */
const paperTarget = {
    drop(props, monitor) {
        const item = monitor.getItem();
        // TODO: sideEffect
        console.log('Dropped');
    },

    canDrop(props, monitor) {
        const item = monitor.getItem();
        return props.type_id === item.type_id;
    },
};
function collectTarget(connect, monitor) {
    return {
        connectDropTarget: connect.dropTarget(),
        isOver: monitor.isOver(),
        canDrop: monitor.canDrop(),
    };
}
function renderOverlay(color) {
    return (
        <div style={{
            position: 'absolute',
            top: 0,
            left: 0,
            backgroundColor: color,
            opacity: 0.4,
            height: '100%',
            width: '100%',
        }} />
    );
}

function MyPaper({ id, connectDropTarget, isOver, canDrop, children }) {
    return connectDropTarget(
        <div>
            <Paper style={{
                padding: 16,
                position: 'relative',
                width: '100%',
                height: '100%',
            }}>
                <Typography gutterBottom variant={`h4`}>Place #{id}</Typography>
                <div style={{
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'start',
                }}>
                    {children}
                </div>
                {!isOver && canDrop && renderOverlay('yellow')}
                {isOver && canDrop && renderOverlay('green')}
                {isOver && !canDrop && renderOverlay('red')}
            </Paper>
        </div>
    );
}
const DropPaper = DropTarget(types.CHIP, paperTarget, collectTarget)(MyPaper);
/**
 * END PAPER TARGET
 */

function Test() {
    return (
        <Grid container spacing={16}>
            <Grid item xs={6}>
                <DropPaper id={1}>
                    <DragChip id={1} type_id={1}/>
                    <DragChip id={2} type_id={1}/>
                </DropPaper>
            </Grid>
            <Grid item xs={6}>
                <DropPaper id={2} type_id={2}>
                    <DragChip id={3} type_id={2}/>
                </DropPaper>
            </Grid>
            <Grid item xs={6}>
                <DropPaper id={3} type_id={1}>

                </DropPaper>
            </Grid>
            <Grid item xs={6}>
                <DropPaper id={4} type_id={2}>
                    <DragChip id={5} type_id={2}/>
                    <DragChip id={6} type_id={2}/>
                </DropPaper>
            </Grid>
        </Grid>
    );
}

export default DragDropContext(HTML5Backend)(Test);