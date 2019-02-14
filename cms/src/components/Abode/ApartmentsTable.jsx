import React from 'react';
import PropTypes from 'prop-types';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Button,
} from '@material-ui/core';
import map from "lodash/map";
import sortBy from "lodash/sortBy";
import filter from "lodash/filter";
import ApartmentType from '../../containers/ApartmentType';
import RoomType from '../../containers/RoomType';
import ConfirmDialog from "../utils/ConfirmDialog";

class ApartmentsTable extends React.PureComponent {
    getRoomsByApart = apart_id => filter(this.props.rooms, room => room.apartment === apart_id);

    render() {
        const { apartments, deleteApartment } = this.props;

        return (
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>Номер</TableCell>
                        <TableCell>Этаж</TableCell>
                        <TableCell>Тип</TableCell>
                        <TableCell>Комнаты</TableCell>
                        <TableCell align={`right`}>Действия</TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {map(apartments, item => {
                        const _rooms = this.getRoomsByApart(item.id);

                        return (
                            <TableRow key={item.id}>
                                <TableCell>{item.number}</TableCell>
                                <TableCell>{item.floor}</TableCell>
                                <TableCell><ApartmentType id={item.type}/></TableCell>
                                <TableCell>
                                    {map(sortBy(_rooms, 'id'), (_r, i) =>
                                        <div key={i}>
                                            #{Number(i) + 1}: <RoomType id={_r.type}/> ({_r.places.length}/{_r.max_places})
                                        </div>
                                    )}
                                </TableCell>
                                <TableCell align={`right`}>
                                    <ConfirmDialog
                                        trigger={
                                            <Button>Удалить</Button>
                                        }
                                        onConfirm={() => deleteApartment(item.id)}
                                    />
                                </TableCell>
                            </TableRow>
                        )
                    })}
                </TableBody>
            </Table>
        );
    }
}

ApartmentsTable.propTypes = {
    apartments: PropTypes.arrayOf(PropTypes.object),
    rooms:      PropTypes.arrayOf(PropTypes.object),

    deleteApartment: PropTypes.func.isRequired,
};

export default ApartmentsTable;