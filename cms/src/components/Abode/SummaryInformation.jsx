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
import reduce from 'lodash/reduce';
import RoomType from "../../containers/RoomType";

class SummaryInformation extends React.PureComponent {
    render() {
        const { items } = this.props;

        return (
            <React.Fragment>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Тип комнаты</TableCell>
                            <TableCell>Занято</TableCell>
                            <TableCell>Заселено</TableCell>
                            <TableCell>Всего</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {map(items, item =>
                            <TableRow key={item.room_type_id}>
                                <TableCell><RoomType id={item.room_type_id}/></TableCell>
                                <TableCell>{item.busy}</TableCell>
                                <TableCell>{item.populated}</TableCell>
                                <TableCell>{item.total}</TableCell>
                            </TableRow>
                        )}
                        {items.length > 0 &&
                            <TableRow key={`summary`}>
                                <TableCell><b>Итого</b></TableCell>
                                <TableCell>{reduce(items, (sum, item) => sum + item.busy, 0)}</TableCell>
                                <TableCell>{reduce(items, (sum, item) => sum + item.populated, 0)}</TableCell>
                                <TableCell>{reduce(items, (sum, item) => sum + item.total, 0)}</TableCell>
                            </TableRow>
                        }
                    </TableBody>
                </Table>
            </React.Fragment>
        );
    }
}

SummaryInformation.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            room_type_id:   PropTypes.number.isRequired,
            populated:      PropTypes.number.isRequired,
            busy:           PropTypes.number.isRequired,
            total:          PropTypes.number.isRequired,
        }),
    ),
};

export default SummaryInformation;