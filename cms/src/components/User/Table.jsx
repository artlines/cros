import React from 'react';
import PropTypes from 'prop-types';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    Button,
    TablePagination,
    Grid,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import map from 'lodash/map';
import LinearProgress from '../utils/LinearProgress';
import {Edit as EditIcon} from "@material-ui/icons";

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
                                    <TableCell>Наименование</TableCell>
                                    <TableCell>Реквизиты</TableCell>
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
                                            <Button onClick={() => onEdit(item.id)}><EditIcon/></Button>
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
    update: PropTypes.func.isRequired,
    onEdit: PropTypes.func.isRequired,
};

export default UserTable;