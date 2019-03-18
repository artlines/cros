import React from 'react';
import PropTypes from 'prop-types';
import {
    Table,
    TableRow,
    TableCell,
    TableBody,
    TableHead,
} from '@material-ui/core';
import map from 'lodash/map';
import sortBy from 'lodash/sortBy';
import reduce from 'lodash/reduce';

class RoomsSummaryInformation extends React.PureComponent {
    render() {
        const { items } = this.props;

        const sumFreeRooms = reduce(items, (sum, item) => sum + item.free_rooms, 0);
        const sumTotalRooms = reduce(items, (sum, item) => sum + item.total_rooms, 0);
        const sumReserved = reduce(items, (sum, item) => sum + item.reserved, 0);
        const sumBusy = reduce(items, (sum, item) => sum + item.busy, 0);
        const sumPopulated = reduce(items, (sum, item) => sum + item.populated, 0);
        const sumTotal = reduce(items, (sum, item) => sum + item.total, 0);
        const sumFree = sumTotal - sumBusy;

        return (
            <React.Fragment>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Тип комнаты</TableCell>
                            <TableCell style={{whiteSpace: 'no-wrap'}} align={`right`}>Свободно комнат / мест</TableCell>
                            <TableCell align={`right`}>Мест в резерве</TableCell>
                            <TableCell align={`right`}>Мест занято</TableCell>
                            <TableCell align={`right`}>Мест заселено</TableCell>
                            <TableCell align={`right`}>Всего комнат / мест</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {map(sortBy(items, 'room_type_title'), item =>
                            <TableRow key={item.room_type_id}>
                                <TableCell>{item.room_type_title}</TableCell>
                                <TableCell align={`right`}>{item.free_rooms} / {item.total - item.busy - item.reserved}</TableCell>
                                <TableCell align={`right`}>{item.reserved}</TableCell>
                                <TableCell align={`right`}>{item.busy}</TableCell>
                                <TableCell align={`right`}>{item.populated}</TableCell>
                                <TableCell align={`right`}>{item.total_rooms} / {item.total}</TableCell>
                            </TableRow>
                        )}
                        {items.length > 0 &&
                            <TableRow key={`summary`}>
                                <TableCell><b>Итого</b></TableCell>
                                <TableCell align={`right`}>{sumFreeRooms} / {sumFree}</TableCell>
                                <TableCell align={`right`}>{sumReserved}</TableCell>
                                <TableCell align={`right`}>{sumBusy}</TableCell>
                                <TableCell align={`right`}>{sumPopulated}</TableCell>
                                <TableCell align={`right`}>{sumTotalRooms} / {sumTotal}</TableCell>
                            </TableRow>
                        }
                    </TableBody>
                </Table>
            </React.Fragment>
        );
    }
}

RoomsSummaryInformation.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            room_type_id:       PropTypes.number.isRequired,
            room_type_title:    PropTypes.string.isRequired,
            populated:          PropTypes.number.isRequired,
            reserved:           PropTypes.number.isRequired,
            busy:               PropTypes.number.isRequired,
            total:              PropTypes.number.isRequired,
            free_rooms:         PropTypes.number.isRequired,
            total_rooms:        PropTypes.number.isRequired,
        }),
    ),
};

export default RoomsSummaryInformation;