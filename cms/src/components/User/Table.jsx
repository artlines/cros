import React from 'react';
import PropTypes from 'prop-types';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    IconButton,
    TablePagination,
    Grid,
    Typography,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import map from 'lodash/map';
import LinearProgress from '../utils/LinearProgress';
import {Edit as EditIcon} from "@material-ui/icons";
import UserRole from "../../containers/UserRole";

class UserTable extends React.Component {

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

    update = () => {
        const { update } = this.props;
        const { page, rowsPerPage } = this.state;
        update(page, rowsPerPage, true);
    };

    handleChangePage = (event, page) => this.setState({page});
    handleChangeRowsPerPage = (event) => this.setState({rowsPerPage: event.target.value});

    render() {
        const { page, rowsPerPage } = this.state;
        const { users: { items, isFetching, total_count }, onEdit } = this.props;

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
                                    <TableCell>ФИО</TableCell>
                                    <TableCell>Роль</TableCell>
                                    <TableCell>Включен/Активен</TableCell>
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
                                            {item.last_name} {item.first_name} {item.middle_name}
                                            <Typography variant={`caption`}>{item.organization_name}</Typography>
                                            {item.post && <Typography variant={`caption`}>{item.post}</Typography>}
                                        </TableCell>
                                        <TableCell>
                                            <UserRole role={item.roles}/>
                                        </TableCell>
                                        <TableCell>
                                            {item.is_active ? `da` : `net`}
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

UserTable.propTypes = {
    users: PropTypes.shape({
        isFetching: PropTypes.bool.isRequired,
        total_count: PropTypes.number.isRequired,
        items: PropTypes.arrayOf(
            PropTypes.shape({
                id:                 PropTypes.number.isRequired,
                first_name:         PropTypes.string.isRequired,
                last_name:          PropTypes.string.isRequired,
                roles:              PropTypes.string.isRequired,
                organization_name:  PropTypes.string.isRequired,
                is_active:          PropTypes.bool.isRequired,
                middle_name:        PropTypes.string,
                post:               PropTypes.string,

            }),
        ),
    }),

    update: PropTypes.func.isRequired,
    onEdit: PropTypes.func.isRequired,
};

export default UserTable;