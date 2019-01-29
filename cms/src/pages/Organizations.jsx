import React from "react";
import {connect} from 'react-redux';
import participating from '../actions/participating';
import abode from '../actions/abode';
import system from '../actions/system';
import OrganizationTable from '../components/Organization/Table';
import {
    TextField,
    Grid,
} from '@material-ui/core';
import isArray from 'lodash/isArray';
import isEqual from 'lodash/isEqual';
import find from "lodash/find";
import map from "lodash/map";
import OrganizationForm from '../components/Organization/Form';
import FabButton from '../components/utils/FabButton';
import MultiSelectField from "../components/utils/MultiSelectField";

class Organizations extends React.Component {
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
        this.props.fetchRoomTypes();
        this.props.fetchManagers();
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { query } = this.state;
        const { fetchOrganizations } = this.props;

        if (!isEqual(prevState.query, query)) {
            fetchOrganizations(query);
        }
    }

    update = (page, rowsPerPage, force = false) => {
        const { query } = this.state;
        const { fetchOrganizations } = this.props;
        let newQuery = {...query};

        newQuery['@limit'] = rowsPerPage;
        newQuery['@offset'] = rowsPerPage * page;

        if (!force) {
            this.setState({query: newQuery});
        } else {
            fetchOrganizations(newQuery);
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
        const { items } = this.props;
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
        const { fetchMembers, fetchComments, fetchInvoices, managers } = this.props;
        const { form } = this.state;

        return (
            <React.Fragment>
                <OrganizationForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                />
                <Grid container spacing={16}>
                    <Grid item xs={4}>
                        <TextField
                            fullWidth
                            helperText={`Поиск по наименованию организации и ИНН`}
                            onChange={this.handleFilterChange(`search`)}
                        />
                    </Grid>
                    <Grid item xs={4}>
                        <MultiSelectField
                            options={map(managers, i => ({ value: i.id, label: `${i.first_name} ${i.last_name}` }))}
                            onChange={this.handleFilterChange(`invited_by[]`)}
                            isSearchable
                            isMulti
                            placeholder={`Начните вводить имя`}
                        />
                    </Grid>
                    <Grid item xs={4}>
                        <Grid container justify={`flex-end`}>
                            <Grid item>
                                <FabButton title={`Добавить организацию`} onClick={this.openForm}/>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        <OrganizationTable
                            loadComments={fetchComments}
                            loadInvoices={fetchInvoices}
                            loadMembers={fetchMembers}
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
        managers: state.system.users.items,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchOrganizations: (data = {}) => {
            dispatch(participating.fetchConferenceOrganizations(data))
        },
        fetchComments: (data = {}) => {
            dispatch(participating.fetchComments(data))
        },
        fetchInvoices: (data = {}) => {
            dispatch(participating.fetchInvoices(data))
        },
        fetchMembers: (data = {}) => {
            dispatch(participating.fetchMembers(data))
        },
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
        fetchManagers: () => dispatch(system.fetchUsers()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Organizations);