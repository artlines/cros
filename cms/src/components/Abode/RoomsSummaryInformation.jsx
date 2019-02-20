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
                            <TableCell style={{whiteSpace: 'no-wrap'}} numeric>Свободно комнат / мест</TableCell>
                            <TableCell numeric>Мест в резерве</TableCell>
                            <TableCell numeric>Мест занято</TableCell>
                            <TableCell numeric>Мест заселено</TableCell>
                            <TableCell numeric>Всего мест</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {map(sortBy(items, 'room_type_title'), item =>
                            <TableRow key={item.room_type_id}>
                                <TableCell>{item.room_type_title}</TableCell>
                                <TableCell numeric>{item.free_rooms} / {item.total - item.busy - item.reserved}</TableCell>
                                <TableCell numeric>{item.reserved}</TableCell>
                                <TableCell numeric>{item.busy}</TableCell>
                                <TableCell numeric>{item.populated}</TableCell>
                                <TableCell numeric>{item.total}</TableCell>
                            </TableRow>
                        )}
                        {items.length > 0 &&
                            <TableRow key={`summary`}>
                                <TableCell><b>Итого</b></TableCell>
                                <TableCell numeric>{sumFreeRooms} / {sumFree}</TableCell>
                                <TableCell numeric>{sumReserved}</TableCell>
                                <TableCell numeric>{sumBusy}</TableCell>
                                <TableCell numeric>{sumPopulated}</TableCell>
                                <TableCell numeric>{sumTotal}</TableCell>
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
        }),
    ),
};

export default RoomsSummaryInformation;