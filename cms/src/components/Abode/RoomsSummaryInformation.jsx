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
                            <TableCell>Свободно комнат</TableCell>
                            <TableCell>Свободно</TableCell>
                            <TableCell>Занято</TableCell>
                            <TableCell>Заселено</TableCell>
                            <TableCell>Всего</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {map(sortBy(items, 'room_type_title'), item =>
                            <TableRow key={item.room_type_id}>
                                <TableCell>{item.room_type_title}</TableCell>
                                <TableCell>{item.free_rooms}</TableCell>
                                <TableCell>{item.total - item.busy}</TableCell>
                                <TableCell>{item.busy}</TableCell>
                                <TableCell>{item.populated}</TableCell>
                                <TableCell>{item.total}</TableCell>
                            </TableRow>
                        )}
                        {items.length > 0 &&
                            <TableRow key={`summary`}>
                                <TableCell><b>Итого</b></TableCell>
                                <TableCell>{sumFreeRooms}</TableCell>
                                <TableCell>{sumFree}</TableCell>
                                <TableCell>{sumBusy}</TableCell>
                                <TableCell>{sumPopulated}</TableCell>
                                <TableCell>{sumTotal}</TableCell>
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
            busy:               PropTypes.number.isRequired,
            total:              PropTypes.number.isRequired,
            free_rooms:         PropTypes.number.isRequired,
        }),
    ),
};

export default RoomsSummaryInformation;