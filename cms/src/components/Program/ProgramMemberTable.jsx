import React from 'react';
import PropTypes from 'prop-types';
import {
    Button,
    IconButton,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Tooltip,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import {
    Edit as EditIcon,
    Check as CheckIcon,
    Close as CloseIcon,
} from "@material-ui/icons";

class ProgramMemberTable extends React.PureComponent {
    render() {
        const { items, onEdit } = this.props;

        return (
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell align={`center`}>ID</TableCell>
                        <TableCell>Фото</TableCell>
                        <TableCell>Информация</TableCell>
                        <TableCell align={`center`}>Опубликован?</TableCell>
                        <TableCell align={`right`}> </TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {items.map(item =>
                        <TableRow key={item.id}>
                            <TableCell align={`center`}>{item.id}</TableCell>
                            <TableCell>
                                <img style={{maxWidth: '180px'}} src={item.photo_original}/>
                            </TableCell>
                            <TableCell>
                                <b>ФИО:</b> {item.last_name} {item.first_name} {item.middle_name}
                                <br/>
                                <b>Организация:</b> {item.org_name}
                            </TableCell>
                            <TableCell align={`center`}>
                                {item.publish
                                    ? <CheckIcon style={{color: green[700]}}/>
                                    : <CloseIcon style={{color: red[700]}}/>
                                }
                            </TableCell>
                            <TableCell align={`right`}>
                                <Tooltip title={`Редактировать`}>
                                    <IconButton onClick={() => this.props.onEdit(item)}>
                                        <EditIcon/>
                                    </IconButton>
                                </Tooltip>
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}

ProgramMemberTable.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id:             PropTypes.number.isRequired,
            last_name:      PropTypes.string.isRequired,
            first_name:     PropTypes.string.isRequired,
            middle_name:    PropTypes.string,
            org_name:       PropTypes.string.isRequired,
            photo_original: PropTypes.string,
            publish:        PropTypes.bool.isRequired,
            ordering:       PropTypes.number.isRequired,
        })
    ),
    /** Fired then click Edit */
    onEdit: PropTypes.func,
};

export default ProgramMemberTable;