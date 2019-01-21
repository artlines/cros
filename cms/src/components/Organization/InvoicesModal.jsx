import React from 'react';
import {connect} from 'react-redux';
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

class InvoicesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        if (this.state.open && !prevState.open) {
            this.update();
        }
    }

    update = () => {
        const { organizationId, update } = this.props;
        update({conference_organization_id: organizationId});
    };

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    render() {
        const { organizationName, trigger, items } = this.props;
        const { open } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
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
                                {map(items, item =>
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
     * Trigger
     */
    trigger: PropTypes.node.isRequired,

    /**
     * Organization info
     */
    organizationId:     PropTypes.number.isRequired,
    organizationName:   PropTypes.string.isRequired,

    /**
     * Invoices array
     */
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id:     PropTypes.number.isRequired,
            number: PropTypes.number.isRequired,
            amount: PropTypes.number.isRequired,
            date:   PropTypes.number.isRequired,
            status: PropTypes.number.isRequired,
        }),
    ),
};

const mapStateToProps = state => ({
    ...state.participating.invoice,
});

export default connect(mapStateToProps)(InvoicesModal);