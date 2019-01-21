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
import CommentsModal from "./CommentsModal";
import MembersModal from "./MembersModal";
import {Close as CloseIcon, Edit as EditIcon} from "@material-ui/icons";

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
        const { update, loadComments } = this.props;
        const { page, rowsPerPage } = this.state;
        update(page, rowsPerPage);
        loadComments(data);
    };

    updateInvoices = (data) => {
        const { update, loadInvoices } = this.props;
        const { page, rowsPerPage } = this.state;
        update(page, rowsPerPage);
        loadInvoices(data);
    };

    updateMembers = (data) => {
        const { update, loadMembers } = this.props;
        const { page, rowsPerPage } = this.state;
        update(page, rowsPerPage);
        loadMembers(data);
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
                                <TableCell>
                                    <Button><EditIcon/></Button>
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
            inn:                PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
            kpp:                PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
            city:               PropTypes.string,
            address:            PropTypes.string,
            requisites:         PropTypes.string,
            total_members:      PropTypes.number.isRequired,
            in_room_members:    PropTypes.number.isRequired,
            comments_count:     PropTypes.number.isRequired,
            invoices_count:     PropTypes.number.isRequired,
        }),
    ),

    loadComments: PropTypes.func.isRequired,
    loadInvoices: PropTypes.func.isRequired,
    loadMembers: PropTypes.func.isRequired,

    update: PropTypes.func.isRequired,
};

const mapStateToProps = state =>
    ({
        ...state.participating.conference_organization,
    });

export default connect(mapStateToProps)(OrganizationTable);