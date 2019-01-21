import React from 'react';
import PropTypes from 'prop-types';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
} from '@material-ui/core';
import map from 'lodash/map';
import Money from "../utils/Money";
import DateTime from "../utils/DateTime";
import InvoiceStatus from './InvoiceStatus';

import createDevData from '../../libs/utils';
const devData = [
    ...createDevData({ number: 5813, amount: 54000.20, date: 1542806279, status: 1 }, 3),
];

class InvoicesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
        };
    }

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    render() {
        const { organizationName, data } = this.props;
        const { open } = this.state;

        return (
            <React.Fragment>
                <Button onClick={this.handleOpen}>{data.length}</Button>
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'md'}
                >
                    <DialogTitle>Счета {organizationName}</DialogTitle>
                    <DialogContent>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>№ счета</TableCell>
                                    <TableCell>Сумма</TableCell>
                                    <TableCell>Дата</TableCell>
                                    <TableCell>Статус</TableCell>
                                    <TableCell align={'right'}>Действия</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(data, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.number}
                                        </TableCell>
                                        <TableCell>
                                            <Money withPenny value={item.amount}/>
                                        </TableCell>
                                        <TableCell>
                                            <DateTime value={item.date}/>
                                        </TableCell>
                                        <TableCell>
                                            <InvoiceStatus id={item.status}/>
                                        </TableCell>
                                        <TableCell align={'right'}>
                                            <Button>Удалить</Button>
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={this.handleClose}>Закрыть</Button>
                    </DialogActions>
                </Dialog>
            </React.Fragment>
        );
    }
}

InvoicesModal.propTypes = {
    /**
     * Organization name
     */
    organizationName: PropTypes.string,
    /**
     * Invoices array
     */
    data: PropTypes.arrayOf(
        PropTypes.shape({
            id:     PropTypes.number.isRequired,
            number: PropTypes.number.isRequired,
            amount: PropTypes.number.isRequired,
            date:   PropTypes.number.isRequired,
            status: PropTypes.number.isRequired,
        }),
    ),
};

InvoicesModal.defaultProps = {
    data: devData,
};

export default InvoicesModal;