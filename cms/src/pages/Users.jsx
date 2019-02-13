import React from "react";
import {connect} from 'react-redux';
import system from '../actions/system';
import participating from '../actions/participating';
import {
    TextField,
    Grid,
} from '@material-ui/core';
import isArray from 'lodash/isArray';
import isEqual from 'lodash/isEqual';
import isNumber from 'lodash/isNumber';
import find from "lodash/find";
import map from "lodash/map";
import UserForm from '../components/User/Form';
import UserTable from '../components/User/Table';
import FabButton from '../components/utils/FabButton';

class Users extends React.Component {
    constructor(props) {
        super(props);

        this.searchTimeout = null;

        this.state = {
            query: {},
            form: {
                open: false,
                initialValues: {},
            },
        };
    }

    componentDidMount() {
        this.props.fetchRoles();
        this.props.fetchOrganizationDirectory();
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { query } = this.state;
        const { fetchUsers } = this.props;

        if (!isEqual(prevState.query, query)) {
            fetchUsers(query);
        }
    }

    update = (page, rowsPerPage, force = false) => {
        const { query } = this.state;
        const { fetchUsers } = this.props;
        let newQuery = {...query};

        rowsPerPage && (newQuery['@limit'] = rowsPerPage);
        rowsPerPage && isNumber(page) && (newQuery['@offset'] = rowsPerPage * page);

        if (!force) {
            this.setState({query: newQuery});
        } else {
            fetchUsers(newQuery);
        }
    };

    handleFilterChange = field => event => {
        let newQuery = {...this.state.query};
        const value = isArray(event) ? map(event, i => i.value) : event.target.value;

        if (!value) {
            delete(newQuery[field]);
        } else {
            newQuery[field] = value;
        }

        newQuery['@offset'] = 0;

        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.setState({query: newQuery});
        }, 350);
    };

    openForm = (id) => {
        const { items } = this.props.users;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: id ? find(items, {id}) : {},
            }
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { users } = this.props;
        const { form } = this.state;

        return (
            <React.Fragment>
                <UserForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={() => this.update(null, null, true)}
                />
                <Grid container spacing={16}>
                    <Grid item xs={8}>
                        <TextField
                            fullWidth
                            helperText={`Поиск по ФИО, email и наименованию организации`}
                            onChange={this.handleFilterChange(`search`)}
                        />
                    </Grid>
                    <Grid item xs={4}>
                        <Grid container justify={`flex-end`}>
                            <Grid item>
                                <FabButton title={`Добавить пользователя`} onClick={this.openForm}/>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        <UserTable
                            users={users}
                            update={this.update}
                            onEdit={this.openForm}
                        />
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        users: state.system.users,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchUsers: (query) => dispatch(system.fetchUsers(query)),
        fetchRoles: () => dispatch(system.fetchRoles()),
        fetchOrganizationDirectory: () => dispatch(participating.fetchOrganizationDirectory()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Users);