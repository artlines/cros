import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Button,
    LinearProgress,
    TablePagination,
} from '@material-ui/core';
import map from 'lodash/map';
import InvoicesModal from './InvoicesModal';

import createDevData from '../../libs/utils';
import CommentsModal from "./CommentsModal";
import MembersModal from "./MembersModal";
const devData = createDevData({
    name: 'NAG LLC.',
    inn: 6659099112,
    kpp: 667101001,
    total_members: 20,
    in_room_members: 14,
    comments_count: 4,
    invoices_count: 3,
}, 100);

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
        const { handlePaginationChange } = this.props;
        handlePaginationChange(page, rowsPerPage);
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { page, rowsPerPage } = this.state;
        const { handlePaginationChange } = this.props;

        if (prevState.page !== page || prevState.rowsPerPage !== rowsPerPage) {
            handlePaginationChange(page, rowsPerPage);
        }
    }

    updateComments = () => {
        const { update, loadComments } = this.props;
        update();
        loadComments();
    };

    updateInvoices = () => {
        const { update, loadInvoices } = this.props;
        update();
        loadInvoices();
    };

    updateMembers = () => {
        const { update, loadMembers } = this.props;
        update();
        loadMembers();
    };

    handleChangePage = (event, page) => {
        this.setState({page});
    };

    handleChangeRowsPerPage = (event) => {
        this.setState({rowsPerPage: event.target.value});
    };

    render() {
        const { page, rowsPerPage } = this.state;
        const { items, isFetching, total_count } = this.props;

        return (
            <React.Fragment>
                {isFetching && <LinearProgress/>}
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>ID</TableCell>
                            <TableCell>Наименование</TableCell>
                            <TableCell>Реквизиты</TableCell>
                            <TableCell>Участников<br/>всего / заселено</TableCell>
                            <TableCell>Счета</TableCell>
                            <TableCell>Комментарии</TableCell>
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
                                        trigger={<Button>{item.invoices_count}</Button>}
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
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
                <TablePagination
                    component={`div`}
                    count={total_count}
                    onChangePage={this.handleChangePage}
                    onChangeRowsPerPage={this.handleChangeRowsPerPage}
                    page={page}
                    rowsPerPage={rowsPerPage}
                />
            </React.Fragment>
        );
    }
}

OrganizationTable.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id:                 PropTypes.number.isRequired,
            name:               PropTypes.string.isRequired,
            inn:                PropTypes.number.isRequired,
            kpp:                PropTypes.number.isRequired,
            total_members:      PropTypes.number.isRequired,
            in_room_members:    PropTypes.number.isRequired,
            comments_count:     PropTypes.number.isRequired,
            invoices_count:     PropTypes.number.isRequired,
        }),
    ),

    loadComments: PropTypes.func.isRequired,
    loadInvoices: PropTypes.func.isRequired,
    loadMembers: PropTypes.func.isRequired,

    handleChangePage: PropTypes.func.isRequired,
};

const mapStateToProps = state =>
    ({
        ...state.participating.conference_organization,
    });

export default connect(mapStateToProps)(OrganizationTable);