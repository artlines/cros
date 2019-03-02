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
    Button,
    IconButton,
    TablePagination,
    Grid,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import map from 'lodash/map';
import InvoicesModal from './InvoicesModal';
import CommentsModal from "./CommentsModal";
import MembersModal from "./MembersModal";
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
        const { items, isFetching, total_count, onEdit } = this.props;

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
                                    <TableCell>ID</TableCell>
                                    <TableCell>Наименование</TableCell>
                                    <TableCell>Реквизиты</TableCell>
                                    <TableCell>Участников<br/>всего / заселено</TableCell>
                                    <TableCell>Счета</TableCell>
                                    <TableCell>Комментарии</TableCell>
                                    <TableCell> </TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
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
                                        <TableCell>
                                            <MembersModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={<Button>{item.total_members} / {item.in_room_members}</Button>}
                                                update={this.updateMembers}
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <InvoicesModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={
                                                    <Button
                                                        style={{
                                                            color: item.invoices_count > 0
                                                                ? item.invoices_payed === item.invoices_count ? green[700] : red[700]
                                                                : 'inherit'
                                                        }}
                                                    >
                                                        {item.invoices_count}
                                                    </Button>
                                                }
                                                update={this.updateInvoices}
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <CommentsModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={<Button>{item.comments_count}</Button>}
                                                update={this.updateComments}
                                            />
                                        </TableCell>
                                        <TableCell>
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