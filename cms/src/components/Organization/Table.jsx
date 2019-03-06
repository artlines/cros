import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {compose} from 'redux';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Tooltip,
    Button,
    IconButton,
    TablePagination,
    Grid,
} from '@material-ui/core';
import { Receipt as ReceiptIcon } from '@material-ui/icons';
import { green, red } from '@material-ui/core/colors';
import map from 'lodash/map';
import InvoicesModal from './InvoicesModal';
import CommentsModal from "./CommentsModal";
import MembersModal from "./MembersModal";
import MakeInvoiceModal from "./MakeInvoiceModal";
import LinearProgress from '../utils/LinearProgress';
import {Edit as EditIcon} from "@material-ui/icons";

class OrganizationTable extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            page: 0,
            rowsPerPage: 10,
        };
    }

    componentDidMount() {
        const { page, rowsPerPage } = this.state;
        const { update } = this.props;
        update(page, rowsPerPage);
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { page, rowsPerPage } = this.state;
        const { update } = this.props;

        if (prevState.page !== page || prevState.rowsPerPage !== rowsPerPage) {
            update(page, rowsPerPage);
        }
    }

    updateComments = (data) => {
        this.props.loadComments(data);
        this.update();
    };

    updateInvoices = (data) => {
        this.props.loadInvoices(data);
        this.update();
    };

    updateMembers = (data) => {
        this.props.loadMembers(data);
        this.update();
    };

    update = () => {
        const { update } = this.props;
        const { page, rowsPerPage } = this.state;
        update(page, rowsPerPage, true);
    };

    handleChangePage = (event, page) => this.setState({page});
    handleChangeRowsPerPage = (event) => this.setState({rowsPerPage: event.target.value});

    render() {
        const { page, rowsPerPage } = this.state;
        const { items, isFetching, total_count, onEdit, update } = this.props;

        return (
            <React.Fragment>

                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <LinearProgress show={isFetching}/>
                    </Grid>
                    <Grid item xs={12}>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell align={`center`}>ID</TableCell>
                                    <TableCell>Наименование</TableCell>
                                    <TableCell>Реквизиты</TableCell>
                                    <TableCell align={`center`}>Участников<br/>всего / заселено</TableCell>
                                    <TableCell align={`center`}>Счета</TableCell>
                                    <TableCell align={`center`}>Комментарии</TableCell>
                                    <TableCell> </TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell align={`center`}>
                                            {item.id}
                                        </TableCell>
                                        <TableCell>
                                            {item.name}
                                            {item.invited_by &&
                                                <div style={{fontSize: 10, color: '#a4a4a4'}}>
                                                    {item.invited_by}
                                                </div>
                                            }
                                        </TableCell>
                                        <TableCell>
                                            <div style={{whiteSpace: 'nowrap'}}><b>ИНН:</b> {item.inn}</div>
                                            <div style={{whiteSpace: 'nowrap'}}><b>КПП:</b> {item.kpp}</div>
                                        </TableCell>
                                        <TableCell align={`center`}>
                                            <MembersModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={<Button>{item.total_members} / {item.in_room_members}</Button>}
                                                update={this.updateMembers}
                                            />
                                        </TableCell>
                                        <TableCell align={`center`}>
                                            {item.invoices_count === 0 &&
                                                <MakeInvoiceModal
                                                    organization_id={item.id}
                                                    organization_name={item.name}
                                                    update={this.updateMembers}
                                                    onSuccess={() => update(null, null, true)}
                                                    trigger={
                                                        <Tooltip title={`Выставить счет`}>
                                                            <IconButton>
                                                                <ReceiptIcon/>
                                                            </IconButton>
                                                        </Tooltip>
                                                    }
                                                />
                                            }
                                            {item.invoices_count > 0 &&
                                                <InvoicesModal
                                                    organizationId={item.id}
                                                    organizationName={item.name}
                                                    trigger={
                                                        <Button
                                                            style={{
                                                                color: item.invoices_payed === item.invoices_count ? green[700] : red[700]
                                                            }}
                                                        >
                                                            {item.invoices_count}
                                                        </Button>
                                                    }
                                                    update={this.updateInvoices}
                                                />
                                            }
                                        </TableCell>
                                        <TableCell align={`center`}>
                                            <CommentsModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={<Button>{item.comments_count}</Button>}
                                                update={this.updateComments}
                                            />
                                        </TableCell>
                                        <TableCell align={`right`}>
                                            <IconButton onClick={() => onEdit(item.id)}><EditIcon/></IconButton>
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                        <TablePagination
                            component={`div`}
                            rowsPerPageOptions={[5, 10, 25]}
                            count={total_count}
                            onChangePage={this.handleChangePage}
                            onChangeRowsPerPage={this.handleChangeRowsPerPage}
                            page={page}
                            rowsPerPage={rowsPerPage}
                            labelRowsPerPage={``}
                            labelDisplayedRows={({ from, to, count }) => `${from}-${to} из ${count}`}
                        />
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

OrganizationTable.propTypes = {
    loadComments: PropTypes.func.isRequired,
    loadInvoices: PropTypes.func.isRequired,
    loadMembers: PropTypes.func.isRequired,

    update: PropTypes.func.isRequired,
    onEdit: PropTypes.func.isRequired,
};

const mapStateToProps = state =>
    ({
        ...state.participating.conference_organization,
    });

export default compose(
    connect(mapStateToProps),
)(OrganizationTable);