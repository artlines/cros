import React from "react";
import PropTypes from "prop-types";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
} from "@material-ui/core";
import map from "lodash/map";

const data = [
    { id: 1, title: "Эконом" },
    { id: 2, title: "Комфорт" },
    { id: 3, title: "Бизнес" },
    { id: 4, title: "VIP" },
];

class ParticipantClasses extends React.Component {
    render() {
        const { data } = this.props;

        return (
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>Наименование</TableCell>
                        <TableCell align={`right`}>Действия</TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {map(data, item =>
                        <TableRow key={item.id}>
                            <TableCell>{item.title}</TableCell>
                            <TableCell align={`right`}>Действия</TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}

ParticipantClasses.propTypes = {
    data: PropTypes.array.isRequired,
};

ParticipantClasses.defaultProps = {
    data
};

export default ParticipantClasses;