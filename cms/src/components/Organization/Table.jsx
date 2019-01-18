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
import map from 'lodash/map';
import InvoiceModal from './InvoiceModal';

import createDevData from '../../libs/utils';
const devData = createDevData({
    name: 'NAG LLC.',
    inn: 6659099112,
    kpp: 667101001,
    total_members: 20,
    in_room_members: 14,
    comments_count: 4,
    invoices: [
        { id: 4, number: 5813, amount: 54000.00, date: 1542806279, status: 1 },
        { id: 3, number: 5123, amount: 21000.00, date: 1542801279, status: 2 },
        { id: 5, number: 5733, amount: 88000.00, date: 1542806879, status: 3 },
    ],
}, 100);

/**
 * TODO: OrganizationModal
 * TODO: CommentsModal
 * TODO: InvoicesModal
 * TODO: OrganizationMembersModal
 */

class OrganizationTable extends React.PureComponent {
    render() {
        const { data } = this.props;

        return (
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>ID</TableCell>
                        <TableCell>Наименование</TableCell>
                        <TableCell>ИНН / КПП</TableCell>
                        <TableCell>Участников<br/>всего / заселено</TableCell>
                        <TableCell>Счета</TableCell>
                        <TableCell>Комментарии</TableCell>
                    </TableRow>
                </TableHead>
                <TableBody>
                    {map(data, item =>
                        <TableRow key={item.id}>
                            <TableCell>
                                {item.id}
                            </TableCell>
                            <TableCell>
                                {item.name}
                            </TableCell>
                            <TableCell>
                                {item.inn} / {item.kpp}
                            </TableCell>
                            <TableCell>
                                {item.total_members} / {item.in_room_members}
                            </TableCell>
                            <TableCell>
                                <InvoiceModal organizationName={item.name}/>
                            </TableCell>
                            <TableCell>
                                <Button>{item.comments_count}</Button>
                            </TableCell>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}

OrganizationTable.propTypes = {
    data: PropTypes.arrayOf(
        PropTypes.shape({
            id:                 PropTypes.number.isRequired,
            name:               PropTypes.string.isRequired,
            inn:                PropTypes.number.isRequired,
            kpp:                PropTypes.number.isRequired,
            total_members:      PropTypes.number.isRequired,
            in_room_members:    PropTypes.number.isRequired,
            comments_count:     PropTypes.number.isRequired,
            invoices:           PropTypes.arrayOf(
                PropTypes.shape({
                    id:     PropTypes.number.isRequired,
                    number: PropTypes.number.isRequired,
                    amount: PropTypes.number.isRequired,
                    date:   PropTypes.number.isRequired,
                    status: PropTypes.number.isRequired,
                }),
            ),
        }),
    ),
};

OrganizationTable.defaultProps = {
    data: devData,
};

export default OrganizationTable;