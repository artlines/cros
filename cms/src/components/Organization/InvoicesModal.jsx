import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {
    Button,
    IconButton,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Tooltip,
    Typography,
} from '@material-ui/core';
import map from 'lodash/map';
import Money from "../utils/Money";
import DateTime from "../utils/DateTime";
import InvoiceStatus from './InvoiceStatus';
import {
    Close as CloseIcon,
    Edit as EditIcon,
    Receipt as ReceiptIcon,
} from "@material-ui/icons";
import FabButton from "../utils/FabButton";
import find from "lodash/find";
import InvoiceForm from "./InvoiceForm";
import LinearProgress from '../utils/LinearProgress';
import API from '../../libs/api';

const api = new API();

class InvoicesModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            form: {
                initialValues: {},
                open: false,
            },
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

    delete = (id) => {
        api.delete(`invoice/${id}`)
            .then(this.update);
    };

    downloadInvoice = id => {
        const href = `${api.apiHost}/invoice/${id}/download`; //s
        window.open(href, '_blank').focus();
    };

    handleOpen = () => this.setState({open: true});
    handleClose = () => this.setState({open: false});

    openForm = (id) => {
        const { items, organizationId } = this.props;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: {
                    conference_organization_id: organizationId,
                    ...id ? find(items, {id}) : {},
                },
            },
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { organizationName, trigger, items, isFetching } = this.props;
        const { open, form } = this.state;

        return (
            <React.Fragment>
                <span onClick={this.handleOpen}>{trigger}</span>
                <InvoiceForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                />
                <Dialog
                    open={open}
                    onClose={this.handleClose}
                    fullWidth={true}
                    maxWidth={'md'}
                >
                    <DialogTitle>
                        <Grid
                            container
                            spacing={0}
                            justify={`space-between`}
                            alignItems={`center`}
                        >
                            <Grid item>
                                Счета {organizationName}
                            </Grid>
                            <Grid item>
                                {/*<FabButton title={`Добавить счет`} onClick={this.openForm}/>*/}
                            </Grid>
                        </Grid>
                    </DialogTitle>
                    <DialogContent>
                        <LinearProgress show={isFetching}/>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>№ заказа</TableCell>
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
                                            {item.number || <Typography variant={`caption`}>отсутствует</Typography>}
                                        </TableCell>
                                        <TableCell>
                                            <Money withPenny value={item.amount}/>
                                        </TableCell>
                                        <TableCell>
                                            <DateTime value={item.date}/>
                                        </TableCell>
                                        <TableCell>
                                            {item.status_text || <InvoiceStatus id={item.status}/>}
                                        </TableCell>
                                        <TableCell align={'right'}>
                                            {/*<Button onClick={() => this.openForm(item.id)}><EditIcon/></Button>*/}
                                            {/*<ConfirmDialog*/}
                                                {/*trigger={<Button><CloseIcon/></Button>}*/}
                                                {/*onConfirm={() => this.delete(item.id)}*/}
                                            {/*/>*/}
                                            <Tooltip title={`Скачать счет`}>
                                                <div>
                                                    <IconButton
                                                        disabled={!item.doc_ready}
                                                        onClick={() => this.downloadInvoice(item.id)}
                                                    >
                                                        <ReceiptIcon/>
                                                    </IconButton>
                                                </div>
                                            </Tooltip>
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
            id:             PropTypes.number.isRequired,
            number:         PropTypes.string,
            amount:         PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
            date:           PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
            status:         PropTypes.number,
            status_text:    PropTypes.string,
            doc_ready:      PropTypes.bool.isRequired,
        }),
    ),
};

const mapStateToProps = state => ({
    ...state.participating.invoice,
});

export default connect(mapStateToProps)(InvoicesModal);